jsPsych.plugins["riskchoose"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

		display_element.empty();

        $.post(SITE_PREFIX + "/riskchoose.php", function(r) {
            var vals = r.split("\n");
            display_element.load(SITE_PREFIX + "/utils/riskchoose.html", function() {
                document.getElementById("riskchoose-result").innerHTML = parseInt(vals[0]);
                document.getElementById("riskchoose-idx").innerHTML = vals[1];
                document.getElementById("riskchoose-money").innerHTML = (parseInt(vals[2]) / 1000).toFixed(3);
                document.getElementById("riskchoose-return").onclick = function() {
		            display_element.empty();
                    jsPsych.finishTrial({});
                };
            });
        });
	}

	return plugin;
})();
