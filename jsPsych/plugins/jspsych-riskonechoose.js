jsPsych.plugins["riskonechoose"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

		display_element.empty();

        $.post(SITE_PREFIX + "/risk_one_choice.php", function(r) {
          if(r.substring(0, 4) == "none") {
            jsPsych.finishTrial({ none: true });
          } else {
            var vals = r.split("\n");
            display_element.load(SITE_PREFIX + "/utils/riskchoose.html", function() {
                var risk_int = parseInt(vals[0]);
                document.getElementById("riskchoose-result").innerHTML = risk_int;
                document.getElementById("riskchoose-idx").innerHTML = vals[1];
                document.getElementById("riskchoose-money").innerHTML = (parseInt(vals[2]) / 1000).toFixed(3);
                document.getElementById("riskchoose-return").onclick = function() {
		            display_element.empty();
                    jsPsych.finishTrial({ "risk-payoff": risk_int });
                };
            });
          }
      });
	}

	return plugin;
})();
