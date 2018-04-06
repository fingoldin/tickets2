jsPsych.plugins["instructions_check"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

		display_element.load(trial.url, function() {
			var count = 1;

			var id = 0;
			var showWrong = function() {
				$("#wrong-top").animate({ top: "0px" });
				if(id) {
					clearTimeout(id);
					id = 0;
				}
				id = setTimeout(function() {
					$("#wrong-top").animate({ top: "-40px" });
				}, 5000);
			};

			var begin = function() {
				$("#" + trial.instructions).css("display", "block").css("opacity", "1");
				$("#" + trial.check).css("display", "none").css("opacity", "0");
				if(count > 1)
                                	showWrong();

				$("#" + trial.cont_btn).off("click").click(function() {
					$("#" + trial.instructions).animate({ opacity: 0 }, 100, function()
					{
						clearInterval(id);
						$("#wrong-top").css("top", "-40px");

						$("#" + trial.instructions).css("display", "none");
						$("#" + trial.check).css("display", "block");
						$("#" + trial.check).animate({ opacity: 1 }, 100);

						$("#" + trial.right).off("click").click(function() {
							console.log(count);
							display_element.empty();
							jsPsych.finishTrial({ tries: count });
						});
						$("#" + trial.wrong).off("click").click(function() {
							count++;
							begin();
						});
					});
				});
			};

			begin();
		});
	}

	return plugin;
})();
