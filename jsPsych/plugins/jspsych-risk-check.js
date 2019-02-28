jsPsych.plugins["risk-check"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

		display_element.empty();

        display_element.load(SITE_PREFIX + "/utils/risk.html", function() {
            var result = 180;
            
            var canvas = document.getElementById("risk-canvas");
            var low = document.getElementById("risk-low-button");
            var result_done = document.getElementById("risk-result-done");
            
            var result_cont = display_element.find("#risk-result");
            var money = display_element.find("#risk-result-money");

            result_done.onclick = function() {
                console.log(result);
                result_cont.animate({ "opacity": "0" }, 500);
                jsPsych.finishTrial({ result: result });
            };
            
            var hw = 150;
            canvas.width = 2 * hw;
            canvas.height = 2 * hw;

            function choose() {
                money.html("Your ticket will cost $" + result.toFixed(0) + ".");
                result_cont.css("display", "block").animate({ "opacity": "1" }, 500);
            }
            
            low.onclick = function() {
                canvas.onclick = null;
                low.onclick = null;
                choose();
            };
            
            if(canvas.getContext) {
                var a = -3; // acceleration of pointer, radians per second ^ 2
                var vel = 30.346;
                var ang = 0;
                var dt = 0.03;

                var c = canvas.getContext("2d");

                canvas.onclick = function() {
                    canvas.onclick = null;
                    low.onclick = null;
                    low.disabled = true;
                    $.post(SITE_PREFIX + "/risk.php", { "choice": "wheel" }, function(r) {
                        // 30.04 - 30.652, 30.346
                        var f = parseFloat(r)
                        if(f > 0.5) {
                            vel = 2.0 * (f - 0.5) * (30.64 - 30.35) + 30.35;
                            result = 200;
                        }
                        else {
                            vel = 2.0 * f * (30.34 - 30.05) + 30.05;
                            result = 140;
                        }
                        spin();
                    });
                };

                function roundedRect(x, y, width, height, radius) {
                    c.beginPath();
                    c.moveTo(x, y + radius);
                    c.lineTo(x, y + height - radius);
                    c.arcTo(x, y + height, x + radius, y + height, radius);
                    c.lineTo(x + width - radius, y + height);
                    c.arcTo(x + width, y + height, x + width, y + height-radius, radius);
                    c.lineTo(x + width, y + radius);
                    c.arcTo(x + width, y, x + width - radius, y, radius);
                    c.lineTo(x + radius, y);
                    c.arcTo(x, y, x, y + radius, radius);
                    c.fill();
                }

                function draw() { 
                    c.clearRect(0, 0, 2 * hw, 2 * hw);
                    c.fillStyle = "red";
                    c.beginPath();
                    c.arc(hw, hw, hw, 0.5 * Math.PI, 1.5 * Math.PI, false);
                    c.fill();
                    
                    c.fillStyle = "blue";
                    c.beginPath();
                    c.arc(hw, hw, hw, 1.5 * Math.PI, 0.5 * Math.PI, false);
                    c.fill();
                    
                    c.fillStyle = "white";
                    c.beginPath();
                    c.arc(hw, hw, 10, 0, 2 * Math.PI, false);
                    c.fill();
                    
                    c.fillStyle = "black";
                    var rhw = 30;
                    var rhh = 16;
                    var rr = 5;
                    roundedRect(hw / 2 - rhw, hw - rhh, 2 * rhw, 2 * rhh, rr);
                    roundedRect(3 * hw / 2 - rhw, hw - rhh, 2 * rhw, 2 * rhh, rr);
                    
                    c.textAlign = "center";
                    c.textBaseline = "middle";
                    c.fillStyle = "white";
                    c.font = "16px 'Roboto', sans-serif";
                    c.fillText("$200", hw / 2, hw);
                    c.fillText("$140", 3 * hw / 2, hw);

                    c.save();

                    c.lineWidth = 6;
                    c.lineCap = "round";
                    c.strokeStyle = "white";
                    
                    c.translate(hw, hw);
                    c.rotate(ang);
                    c.translate(-hw, -hw);

                    c.beginPath();
                    c.moveTo(hw, hw);
                    c.lineTo(hw, 10);
                    c.stroke();
                
                    c.restore();
                }

                function spin() {
                    draw();
                    //var dt = tdiff();
                    ang += vel * dt;
                    vel += a * dt;

                    if(vel > 0.0)
                        window.requestAnimationFrame(spin);
                    else
                        choose();
                }
                
                draw();
            } else {
            }
        });

        data = {};

       // jsPsych.finishTrial(data);
	}

	return plugin;
})();
