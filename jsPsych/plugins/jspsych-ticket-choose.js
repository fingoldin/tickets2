jsPsych.plugins["ticket-choose"] = (function()
{
	var plugin = {};

	function gt()
	{
		var d = new Date();
		return d.getTime();
	}

	plugin.trial = function(display_element, trial)
	{
		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

		trial.prices = trial.prices || [];
		trial.continue_message = trial.continue_message || "Continue";
		trial.sequence = trial.sequence || "";
        trial.sequence_id = trial.sequence_id || 0;
        trial.num_sequences = trial.num_sequences || 0;
		trial.phase = trial.phase || 0;
		trial.group = trial.group || 0;
        trial.max_points = trial.max_points || 0;
        trial.name = trial.name || "";
        trial.product_id = trial.product_id || 0;

		var num_prices = trial.prices.length;
		if(!num_prices)
			jsPsych.finishTrial({ "result": "error" });

		var times = [];

		display_element.html("");

		display_element.load(SITE_PREFIX + "/utils/ticket-choose.php", function()
		{
            var average_price = 0.0;
            for(var i = 0; i < trial.prices.length; i++) {
                average_price += trial.prices[i] / trial.prices.length;
            }

            average_price = 0.5*Math.round(2*average_price);
            
            $("#ticket-choose-seq-num").html(trial.sequence_id + 1);
            $("#ticket-choose-seq-total").html(trial.num_sequences);
            $("#ticket-choose-seq-avg").html("$" + average_price.toFixed(2));
            $("#ticket-choose-seq").css("opacity", "1");
            
            var ticket_wrap = display_element.find("#ticket-wrap");
            ticket_wrap.append(trial.image.img);

            var ticket_name = display_element.find("#ticket-name");
            ticket_name.html(trial.name);
            
            var progress_bar = display_element.find("#ticket-choose-progress"); 
            progress_bar.css("width", (100 / num_prices).toFixed(0) + "%");
            progress_bar.html("1/" + num_prices);
            
            var price_num = -1;
			var next_num = 0;

            var ticket_main = display_element.find("#ticket-choose-main"); 

			var price = display_element.find("#number-animation");
			next_price.startTime = gt();
			next_price();

			var select = display_element.find("#ticket-choose-select");
			select.click(select_price);

			var next = display_element.find("#ticket-choose-next");
			next.click(next_price);

			var above = display_element.find("#number-animation-above");
			var below = display_element.find("#number-animation-below");

            above.html("Price <span>1</span> of <span>" + num_prices + "</span>:");
			
			var listener = jsPsych.pluginAPI.getKeyboardResponse({
				callback_function: next_price,
				valid_responses: [32],
				rt_method: "date",
				persist: true,
				allow_held_key: false
			});

			function select_price()
			{
				if(price_num < num_prices)
				{
					select.off("click");
					next.off("click");
                    jsPsych.pluginAPI.cancelKeyboardResponse(listener);

					times[price_num] = gt() - next_price.startTime;
	
					ticket_main.animate({ opacity: "0" }, 200, function() {
                        display_element.find("#ticket-choose-progress-wrap").remove();
                        ticket_name.remove();
                        
                        var prices = trial.prices.slice(0);
                        prices.sort(function(a, b){return a - b});

                        var points = 0;

                        var r = prices.indexOf(trial.prices[price_num]);

                        below.html("");

                        if(r === 0) {
                            points = trial.max_points;
                            above.html("Congratulations! Your chose first best product!");
                        }
                        else {
                            var frac = (prices[prices.length - 1] - trial.prices[price_num]) / (prices[prices.length - 1] - prices[0]);
                            //var frac = (prices.length - r - 1)/(prices.length - 1);
                            points = Math.round(trial.max_points * frac);
                            
                            //var diff = trial.prices[price_num] - prices[0];

                            var prefix = "You chose the ";
/*                            if(frac >= 0.9) {
                                prefix = "Good job! You would only";
                            }
*/                          prefix += r+1;
                            if(r == 1) {
                                prefix += "nd";
                            } else if(r == 2) {
                                prefix += "rd";
                            } else {
                                prefix += "th";
                            }
                            above.html(prefix + " best product.");
                        }
                        
                        above.show();

                        price.hide();

                        ticket_wrap.hide();

                        listener = jsPsych.pluginAPI.getKeyboardResponse({
                            callback_function: function() { end_trial(points, r, times); },
                            valid_responses: [32],
                            rt_method: "date",
                            persist: true,
                            allow_held_key: false
                        });

                        select.hide();
                        next.html(trial.continue_message).addClass("big-btn").click(function() { end_trial(points, r, times); });

                        ticket_main.animate({ opacity: "1" }, 200);
					});
				}
				else
					end_trial(0, -1, 0);
			}

			function next_price()
			{
				if(price.is(":animated"))
					return;
				else
				{
					next_num++;
                    price_num++;
					if(price_num >= num_prices) {
						price_num = num_prices - 1;

						times[num_prices - 1] = gt() - next_price.startTime;

						select_price();
					}
					else if(price_num === 0) {
						price.html("<span>$</span>" + trial.prices[price_num].toFixed(2)).css("transform", "translateX(-30px)");
               	        price.animate({ transform: "translateX(0px)", opacity: "1" }, 200);
						next_price.startTime = gt();
						ticket_main.css("opacity", "1");
					}
					else {
						times[price_num-1] = gt() - next_price.startTime;

                        progress_bar.html((price_num + 1) + "/" + num_prices);
                        progress_bar.css("width", (100 * (price_num + 1) / num_prices).toFixed(0) + "%");
						price.animate({ transform: "translateX(30px)", opacity: "0" }, 200, function() {
							price.html("<span>$</span>" + trial.prices[price_num].toFixed(2)).css("transform", "translateX(-30px)");
							price.animate({ transform: "translateX(0px)", opacity: "1" }, 200);
							above.html("Price <span>" + (price_num + 1) + "</span> of <span>" + num_prices + "</span>:");
                            next_price.startTime = gt();
						});
					}
				}
			}

			function end_trial(ps, r, ti)
            {
				if(r == -1) {
					console.log("Whoops! There was an error");
					jsPsych.finishTrial({});
					return;
				}

				ticket_main.css("opacity", "0");

                jsPsych.pluginAPI.cancelAllKeyboardResponses();
                var trial_data = {
                    "result": trial.prices[price_num],
					"points": ps,
					"place": r,
					"phase": trial.phase,
					"sequence": trial.sequence_id,
					"prices": trial.prices,
                    "name": trial.name,
					"times": ti,
					"next_num": next_num,
                    "group": trial.group,
                    "average_price": average_price,
                    "product_id": trial.product_id
                };

                console.log(trial_data);
                jsPsych.finishTrial(trial_data);
            }
		});
	}

  	return plugin;
})();
