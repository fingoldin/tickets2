jsPsych.plugins["bar-choose"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

        if(trial.passed) {
            jsPsych.finishTrial({ repeat_num: trial.repeat_num, result: "passed" });
            return;
        }
		
        trial.instructions = trial.instructions || "No instructions given";
		trial.subtitle = trial.subtitle || "";
		trial.categories = trial.categories || [];
		trial.min_val = trial.min_val || 0;
		trial.max_val = trial.max_val || 100;
		trial.answers = trial.answers || [];
		trial.phase = trial.phase || 0;
		trial.number = trial.number || 0;
        trial.repeat_num = trial.repeat_num || 0;
//		trial.points = trial.points || { points: 0, subtitle: "" };
		
        trial.instructions = trial.instructions.replace("MAXVAL", trial.max_val);
        
        display_element.empty();

		display_element.load("/christiane/tickets3/utils/bar-choose.html", function()
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

			        var results = display_element.find("#bar-graph").barChooseGraph("get");

                    var data = {};

                    if(trial.pass_threshold)
                        data.passed = (results.total_offby / trial.max_val) <= trial.pass_threshold;

                    data.responses = results.categories;
					data.phase = trial.phase;
					data.number = trial.number;
                    data.repeat_num = trial.repeat_num;
                    data.max_val = trial.max_val;

					display_element.html("");

					//console.log(JSON.stringify(data.responses));

					jsPsych.finishTrial(data);
				});
			});
		});
	}

	return plugin;
})();
