jsPsych.plugins["special_sequence"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

		display_element.empty().load("/tickets/utils/special_sequence.html", function() {
			$(".age-main").css("opacity", "1");

			$("#worker-form").submit(function(e) {
				e.preventDefault();

				var comment = $("#id-input").val();

					var data = { special_comment: comment };
					//console.log(id);
					display_element.empty();
					jsPsych.finishTrial(data);

			});
		});
	}

	return plugin;
})();
