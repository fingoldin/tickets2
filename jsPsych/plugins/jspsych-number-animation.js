jsPsych.plugins["number-animation"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
    	trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

        if(trial.passed) {
            jsPsych.finishTrial({ repeat_num: trial.repeat_num, result: "passed" });
            return;
        }
		
        trial.prices = trial.prices || [];
		trial.continue_message = trial.continue_message || "Continue";
		trial.phase = trial.phase || 0;
        trial.repeat_num = trial.repeat_num || 0;
        trial.sequence_num = trial.sequence_num || 0;

		//console.log(trial.prices);

		var num_prices = trial.prices.length;
		//xxvar num_prices = 2;
		if(!num_prices) {
			jsPsych.finishTrial({ result: "error" });
            return;
        }

		display_element.html("");
		display_element.load("/christiane/tickets3/utils/number-animation.html", function()
		{
			//window.viewportUnitsBuggyfill.refresh();

			var price_num = -1;

/*	var wrap = display_element.find("#jspsych-animation-image");
      $(wrap).html("");
      $(wrap).append("<div class='number-animation-above'>Price of ticket:</div>");

      var number = document.createElement("DIV");
      number.classList.add("number-animation");
      number.innerHTML = "<span>" + trial.prefix + "</span>" + trial.stimuli[animate_frame];
      $(wrap).append(number);

      $(number).css("transform", "translateX(0px)").css("opacity", "0");
      $(number).stop().animate({ transform: "translateX(0px)", opacity: "1" }, interval_time / 2, function() {
        $(number).stop().animate({ transform: "translateX(0px)", opacity: "0" }, interval_time / 2);
      });*/
		
            if(trial.repeat_num > 1 && trial.sequence_num == 0)
            {
				$("#wrong-top").animate({ top: "0px" });
				setTimeout(function() {
					$("#wrong-top").animate({ top: "-40px" });
				}, 5000);
            }

			var price = display_element.find(".number-animation");
			next_price();

			var next = display_element.find("#ticket-choose-next");
			next.click(next_price);

			var listener = jsPsych.pluginAPI.getKeyboardResponse({
				callback_function: next_price,
				valid_responses: [32],
				rt_method: "date",
				persist: true,
				allow_held_key: false
			});


			function next_price()
			{
				//console.log("next");
//				if(selected)
//					end_trial();
				if(price.is(":animated"))
					return;
				else if(++price_num >= num_prices) {
					price_num = num_prices - 1;
					end_trial();
				}
				else if(price_num === 0) {
					price.html("<span>$</span>" + trial.prices[price_num]).css("transform", "translateX(-30px)");
                                       	showTicket(trial.phase, $("#ticket-wrap"));
                                       	price.animate({ transform: "translateX(0px)", opacity: "1" }, 200);

					display_element.find(".ticket-choose-main").css("opacity", "1");
				}
				else {
					price.animate({ transform: "translateX(30px)", opacity: "0" }, 200, function() {
						price.html("<span>$</span>" + trial.prices[price_num]).css("transform", "translateX(-30px)");
						showTicket(trial.phase, $("#ticket-wrap"));
						price.animate({ transform: "translateX(0px)", opacity: "1" }, 200);
					});
				}
			}

			function end_trial()
                	{
				display_element.find(".ticket-choose-main").css("opacity", "0");
                                jsPsych.pluginAPI.cancelAllKeyboardResponses();

                        	var trial_data = {
                        	};

                        	jsPsych.finishTrial(trial_data);
                	}
		});
	}

  	return plugin;
})();
