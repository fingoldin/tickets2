jsPsych.plugins["riskchoose"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

		display_element.empty();

        $.post(SITE_PREFIX + "/riskchoose.php", function(v) {
            display_element.load(SITE_PREFIX + "/utils/riskchoose.html", function() {
                document.getElementById("riskchoose-result").innerHTML = (v / 1000).toFixed(2);
                document.getElementById("riskchoose-return").onclick = function() {
		            display_element.empty();
                    jsPsych.finishTrial({});
                };
            });
        });
	}

	return plugin;
})();
