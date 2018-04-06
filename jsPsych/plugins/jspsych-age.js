jsPsych.plugins["age"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

		display_element.empty().load("/tickets/utils/age.html", function() {
			$("#gender-drop").select2({ minimumResultsForSearch: -1 });
			$(".age-main").css("opacity", "1");

			$("#age-form").submit(function(e) {
				e.preventDefault();

				var g = $("#gender-drop").select2("val");
				var a = parseInt($("#age-input").val());
				if(isNaN(a) || !a)
					alert("Please enter a valid age");
				else
				{
					var data = { age: a, gender: g };
					display_element.empty();
					jsPsych.finishTrial(data);
				}
			});
		});
	}

	return plugin;
})();
