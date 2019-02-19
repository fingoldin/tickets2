jsPsych.plugins["points-update"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

        display_element.empty();

        $.post(SITE_PREFIX + "/getpoints.php", function(d) {
            var points = parseInt(d);

            display_element.load(SITE_PREFIX + "/utils/points-update.html", function() {
                $("#points-update-text").html("Great! You are done with the first 100 sequencese and so far, you have earned $" + (0.001 * points).toFixed(3));
                $("#points-update-button").click(function() {
                    display_element.empty();
                    jsPsych.finishTrial({});
                });
            });
        });
    }
	return plugin;
})();
