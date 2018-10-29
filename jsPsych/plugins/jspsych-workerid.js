jsPsych.plugins["workerid"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

		display_element.empty().load("/tickets/utils/workerid.html", function() {
			$(".age-main").css("opacity", "1");

			$("#worker-form").submit(function(e) {
				e.preventDefault();

				var id = $("#id-input").val().replace(" " , "");
				if(!id || id === "")
					alert("Please enter a valid id");
				else
				{
					var data = { worker_id: id };
					//console.log(id);
					display_element.empty();
					jsPsych.finishTrial(data);
				}
			});
		});
	}

	return plugin;
})();
