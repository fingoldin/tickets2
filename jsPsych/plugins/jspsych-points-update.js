jsPsych.plugins["points-update"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

		trial.points = trial.points.p;

        display_element.empty();

        $.post("/tickets2/getpoints.php", function(d) {
            var points = parseInt(d);

            display_element.load("/tickets2/utils/points-update.html", function() {
                $("#points-update-text").html("So far, you have earned $" + money.toFixed(3));
                $("#points-update-button").click(function() {
                    display_element.empty();
                    jsPsych.finishTrial({});
                });
            });
        });
    }
	return plugin;
})();
