jsPsych.plugins["simplerisk"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

        var num_trials = trial.num_trials || 0;
        var trial_num = 0;
        
		display_element.empty();

        display_element.load(SITE_PREFIX + "/utils/simplerisk.php", function() {
            var progress_bar = display_element.find("#risk-progress"); 
            progress_bar.css("width", (100 / num_trials).toFixed(0) + "%");
            progress_bar.html("1/" + num_trials);
            
            var result = 180;
            
            var canvas = document.getElementById("risk-canvas");
            var low = document.getElementById("risk-low-button");
            
            var result_cont = display_element.find("#risk-result");
            var money = display_element.find("#risk-result-money");
            var result_done = document.getElementById("risk-result-done");
            
            var vel = 0;
            var ang = 0;
            var valid_click = true;
            var choices = [];

            function result_click() {
                choices.push(result);
                trial_num += 1;
                if(trial_num == num_trials) {
                    console.log(choices);
		            display_element.empty();
                    jsPsych.finishTrial({ choices: choices });
                    return;
                }
                ang = 0;
                vel = 0;
                draw();
                progress_bar.html((trial_num + 1) + "/" + num_trials);
                var p = (100 * (trial_num + 1) / num_trials); 
                progress_bar.css("width", Math.max(p, 5).toFixed(0) + "%");
                result_cont.animate({ "opacity": "0" }, 500, function() {
                    $(this).css("display", "none");
                    valid_click = true;
                    low.disabled = false;
                });
            }
			
            /*var listener = jsPsych.pluginAPI.getKeyboardResponse({
				callback_function: result_click,
				valid_responses: [32],
				rt_method: "date",
				persist: false,
				allow_held_key: false
			});*/
            
            var hw = 150;
            canvas.width = 2 * hw;
            canvas.height = 2 * hw;

            if(canvas.getContext) {
                result_done.onclick = result_click;

                low.onclick = function() {
                    if(!valid_click)
                        return;

                    valid_click = false;
                    low.disabled = true;
                    $.post(SITE_PREFIX + "/risk.php", { "choice": "fixed" }, function(r) {
                        result = parseInt(r);
                        console.log("showing in low");
                        show();
                    });
                };
                
                function show() {
                    console.log("show");
                    valid_click = true;
                    money.html("You won $" + result.toFixed(0) + "!");
                    result_cont.css("display", "block").animate({ "opacity": "1" }, 500);
                    $(result_done).focus();
                }
            
                var max = 220;
                var min = 140;
                var a = -10; // -3 acceleration of pointer, radians per second ^ 2
                var dt = 3;

                var c = canvas.getContext("2d");

                canvas.onclick = function() {
                    if(!valid_click)
                        return;

                    valid_click = false;
                    low.disabled = true;
                    $.post(SITE_PREFIX + "/risk.php", { "choice": "wheel" }, function(r) {
                        result = parseInt(r);
                        if(result == min) {
                            vel = 5000 + Math.round(75 * Math.random());
                            result = min;
                        }
                        else {
                            vel = 5080 + Math.round(70 * Math.random());
                            result = max;
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
                        
                    c.fillStyle = "blue";
                    c.beginPath();
                    c.arc(hw, hw, hw, -0.5 * Math.PI, 0.5 * Math.PI, false);
                    c.closePath();
                    c.fill();
                    
                    c.fillStyle = "red";
                    c.beginPath();
                    c.arc(hw, hw, hw, 0.5 * Math.PI, 1.5 * Math.PI, false);
                    c.closePath();
                    c.fill();
                    
                    var w = 50;
                    var h = 30;
                    c.fillStyle = "black";
                    roundedRect(hw / 2 - w / 2, hw - h / 2, w, h, 5);
                    roundedRect(3 * hw / 2 - w / 2, hw - h / 2, w, h, 5);

                    c.textAlign = "center";
                    c.textBaseline = "middle";
                    c.fillStyle = "white";
                    c.font = "16px 'Roboto', sans-serif";
                    c.fillText("$" + min, hw / 2, hw);
                    c.fillText("$" + max, 3 * hw / 2, hw);
                    
                    c.save();
                    
                    c.translate(hw, hw);
                    c.rotate(Math.PI * 2 * ((ang / 1000) % (max - min + 1)) / (max - min + 1));
                    c.translate(-hw, -hw);
                    
                    c.lineWidth = 6;
                    c.lineCap = "round";
                    c.strokeStyle = "white";

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
                    //console.log(ang);

                    if(vel > 0)
                        window.requestAnimationFrame(spin);
                    else {
                        console.log("showing in spin");
                        show();
                    }
                }
                
                draw();
            } else {
            }
        });
	}

	return plugin;
})();