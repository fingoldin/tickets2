jsPsych.plugins["bar-choose"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
		trial.instructions = trial.instructions || "No instructions given";
		trial.subtitle = trial.subtitle || "";
		trial.categories = trial.categories || [];
		trial.min_val = trial.min_val || 0;
		trial.max_val = trial.max_val || 100;
		trial.answers = trial.answers || [];
		trial.phase = trial.phase || 0;
		trial.number = trial.number || 0;
//		trial.points = trial.points || { points: 0, subtitle: "" };

		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

		display_element.empty();

		display_element.load("/tickets/utils/bar-choose.html", function()
		{
//			showPoints(display_element, trial.points);

			display_element.find("#bar-instructions").html(trial.instructions);
			display_element.find("#bar-subtitle").html(trial.subtitle);

			display_element.find("#bar-graph").height(600).barChooseGraph("init", { categories: trial.categories, min: trial.min_val, max: trial.max_val });

			display_element.find("#bar-submit").click(function() {
				if($(".bar-graph-input input:focus").length || !confirm("Are you sure you want to submit your answers?"))
					return;

				display_element.find("#bar-graph").barChooseGraph("show", { answers: trial.answers, max: trial.max_val });

				$(this).html("Next section");

				display_element.find("#bar-submit").off("click").click(function() {

					var data = {
						responses: display_element.find("#bar-graph").barChooseGraph("get")
					}

					data["phase"] = trial.phase;
					data["number"] = trial.number;

					display_element.html("");

					//console.log(JSON.stringify(data.responses));

					jsPsych.finishTrial(data);
				});
			});
		});
	}

	return plugin;
})();
