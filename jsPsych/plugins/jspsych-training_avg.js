jsPsych.plugins["training_avg"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
		trial.sequence = trial.sequence || [];
		trial.sequence_num = trial.sequence_num || 0;
		trial.min_val = trial.min_val || 0;
		trial.max_val = trial.max_val || 0;
		trial.phase = trial.phase || 0;

		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

		var avg = 0;
		for(var i = 0; i < trial.sequence.length; i++)
			avg += trial.sequence[i];
		avg = Math.round(avg / trial.sequence.length);

		display_element.empty().load("/tickets/utils/training_avg.html", function() {
			for(var i = trial.min_val; i <= trial.max_val; i++)
				$("#avg-drop").append("<option value='" + i + "'>" + i + "</option>");

			setTimeout(function() {
				$("#avg-drop").select2({ minimumResultsForSearch: 1 });
				$(".avg-main").css("opacity", "1");

				$("#avg-form").submit(function(e) {
					e.preventDefault();

					var a = parseInt($("#avg-drop").select2("val"));

					$("#avg-drop").select2("destroy").parent().remove();
					if(avg === a)
						$(".age-header").html("The average was $" + avg + ". You were dead on!");
					else
						$(".age-header").html("The average was $" + avg + ". You were off by " + Math.abs(avg - a) + "!");
					$(".avg-main").find("button").html("Continue");
					
					$("#avg-form").off("submit").submit(function(e) {
						e.preventDefault();

						var data = { phase: trial.phase, sequence: trial.sequence_num, avg: avg, response: a };
						console.log(data);
						display_element.empty();
						jsPsych.finishTrial(data);
					});
				});
			}, 100);
		});
	}

	return plugin;
})();
