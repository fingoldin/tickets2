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
		trial.showpoints = trial.showpoints || false;
		trial.phase = trial.phase || 0;
		trial.group = trial.group || 0;
        trial.max_points = trial.max_points || 0;

		//console.log("Trial: " + trial.prices);

		//console.log(trial.prices);

		var num_prices = trial.prices.length;
		if(!num_prices)
			jsPsych.finishTrial({ "result": "error" });

		var times = [];

		display_element.html("");

		display_element.load(SITE_PREFIX + "/utils/ticket-choose.php", function()
		{
			//window.viewportUnitsBuggyfill.refresh();
            /*var but_wrap = display_element.find("#ticket-choose-but-wrap");
            var n_d = document.createElement("DIV");
            n_d.classList = "ticket-choose-but ticket-choose-but-sel";

            but_wrap.append(n_d);
            for (var i = 1; i < num_prices; i++) {
				var n_d2 = document.createElement("DIV");
                n_d2.classList = "ticket-choose-but";
                //n_d2.style = "width: " + x
                but_wrap.append(n_d2);
            }*/
            display_element.find("#ticket-choose-seq-num").html(trial.sequence_id + 1);
            display_element.find("#ticket-choose-seq-total").html(trial.num_sequences);
            
            var progress_bar = display_element.find("#ticket-choose-progress"); 
            progress_bar.css("width", (100 / num_prices).toFixed(0) + "%");
            progress_bar.html("1/" + num_prices);
            
            var price_num = -1;
			var next_num = 0;

			if(trial.showpoints)
				$("#points-s").html(trial.sequence);

			var price = display_element.find(".number-animation");
			next_price.startTime = gt();
			next_price();

			var select = display_element.find("#ticket-choose-select");
			select.click(select_price);

			var next = display_element.find("#ticket-choose-next");
			next.click(next_price);

			var above = display_element.find(".number-animation-above");
			var below = display_element.find(".number-animation-below");
			
            above.html("Selling price <span>1</span> of <span>" + num_prices + "</span>:");

			var listener = jsPsych.pluginAPI.getKeyboardResponse({
				callback_function: next_price,
				valid_responses: [32],
				rt_method: "date",
				persist: true,
				allow_held_key: false
			});

			var selected = false;

			function select_price()
			{
				if(price_num < num_prices)
				{
					select.off("click");
					next.off("click");
                    jsPsych.pluginAPI.cancelKeyboardResponse(listener);

					times[price_num] = gt() - next_price.startTime;
	
					display_element.find(".ticket-choose-main").animate({ opacity: "0" }, 200, function() {
//                        display_element.find("#ticket-choose-progress-wrap").remove();
//                        display_element.find("#ticket-choose-seq").remove();
                        $(".progress").hide();
                        
                        var prices = trial.prices.slice(0);
                                        prices.sort(function(a, b){return a - b});

                        //console.log(prices);
                        //console.log(trial.prices[price_num]);

                        var points = 0;

                        var r = prices.indexOf(trial.prices[price_num]);

                        //console.log(points);

                        below.html("");

                        console.log(r);
                        if(r === (prices.length - 1)) {
                            points = trial.max_points;
                            above.html("Congratulations! You chose the highest stock price!");
                        }
                        else {
                            points = Math.round(trial.max_points * (trial.prices[price_num] - prices[0]) / (prices[prices.length - 1] - prices[0]));
                            
                            var diff = prices[prices.length - 1] - trial.prices[price_num];

                            above.html("You could have made $" + diff.toFixed(0) + " more if had you chosen a different stock price");
                        }

                        price.hide();

                        $("#ticket-wrap").hide();

                        listener = jsPsych.pluginAPI.getKeyboardResponse({
                                            callback_function: function() { end_trial(points, r, times); },
                                            valid_responses: [32],
                                            rt_method: "date",
                                            persist: true,
                                            allow_held_key: false
                                    });

                        select.hide();
                        next.html(trial.continue_message).addClass("big-btn").off("click").click(function() { end_trial(points, r, times); });

                        selected = true;

                        display_element.find(".ticket-choose-main").animate({ opacity: "1" }, 200);
					});
				}
				else
					end_trial(0, -1);
			}

			function next_price()
			{
//				if(selected)
//					end_trial();
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
						price.html("<span>$</span>" + trial.prices[price_num]).css("transform", "translateX(-30px)");
               	        showTicket(trial.phase, $("#ticket-wrap"));
               	        price.animate({ transform: "translateX(0px)", opacity: "1" }, 200);
						next_price.startTime = gt();
						display_element.find(".ticket-choose-main").css("opacity", "1");
					}
					else {
						times[price_num-1] = gt() - next_price.startTime;

                        progress_bar.html((price_num + 1) + "/" + num_prices);
                        progress_bar.css("width", (100 * (price_num + 1) / num_prices).toFixed(0) + "%");
						price.animate({ transform: "translateX(30px)", opacity: "0" }, 200, function() {
							price.html("<span>$</span>" + trial.prices[price_num]).css("transform", "translateX(-30px)");
							price.animate({ transform: "translateX(0px)", opacity: "1" }, 200, function() {
                                //but_wrap.children().eq(price_num-1).removeClass("ticket-choose-but-sel");
                                //but_wrap.children().eq(price_num).addClass("ticket-choose-but-sel");
                            });
							//showTicket(trial.phase, $("#ticket-wrap"));
							above.html("Stock <span>" + (price_num + 1) + "</span> of <span>" + num_prices + "</span>:");
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

				display_element.find(".ticket-choose-main").css("opacity", "0");
                display_element.find("#ticket-choose-seq").css("opacity", "0");
                                jsPsych.pluginAPI.cancelAllKeyboardResponses();
//console.log("answer: " + trial.prices[price_num]);
                        	var trial_data = {
                                	"result": trial.prices[price_num],
					"points": ps,
					"place": r,
					"phase": trial.phase,
					"sequence": trial.sequence_id,
					"prices": trial.prices,
					"times": ti,
					"next_num": next_num,
                    "group": trial.group
                        	};

				//console.log("Prices for this sequence: " + trial.prices);

                        	jsPsych.finishTrial(trial_data);
                	}
		});
	}

  	return plugin;
})();
