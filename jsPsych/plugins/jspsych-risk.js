jsPsych.plugins["risk"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

        // Is this a risk set where the user ends upon clicking the fixed amount?
        var one_trial = trial.one_trial || false;

        // Of this format:
        // [{ fixed: 140, spinner: [{fraction: 0.2, value: 170}]}]
        //var all_choices = trial.all_choices || [];
        
        var all_choices = [
            { fixed: 170, spinner: [
                { fraction: 0.1, value: 140 },
                { fraction: 0.2, value: 150 },
                { fraction: 0.3, value: 160 },
                { fraction: 0.4, value: 170 }
            ] },
            { fixed: 120, spinner: [
                { fraction: 0.5, value: 100 },
                { fraction: 0.25, value: 110 },
                { fraction: 0.15, value: 120 },
                { fraction: 0.1, value: 130 }
            ] },
            { fixed: 100, spinner: [
                { fraction: 0.5, value: 140 },
                { fraction: 0.15, value: 180 },
                { fraction: 0.15, value: 120 },
                { fraction: 0.2, value: 130 }
            ] }
        ];
        
        var trial_num = 0;
        var num_trials = all_choices.length;
        
		display_element.empty();

        if(num_trials < 1) {
            jsPsych.finishTrial({ result: "Error" });
        }

        var php_site = SITE_PREFIX + "/utils/risk.php";
        if(one_trial)
            php_site = SITE_PREFIX + "/utils/risk_one.php";

        display_element.load(php_site, function() {
            display_element.find("#risk-count").html(num_trials);
            
            var progress_bar_wrap = display_element.find("#risk-progress-wrap");

            var progress_bar = display_element.find("#risk-progress"); 
            progress_bar.css("width", (100 / num_trials).toFixed(0) + "%");
            progress_bar.html("1/" + num_trials);
            
            var result = 180;
            var outcome = "";
            
            var canvas = document.getElementById("risk-canvas");
            var low = document.getElementById("risk-low-button");
            low.innerHTML = "$" + all_choices[trial_num].fixed;
            
            var result_cont = display_element.find("#risk-result");
            var money = display_element.find("#risk-result-money");
            var result_done = document.getElementById("risk-result-done");
            var result_no = document.getElementById("risk-result-no");

            var first_box = document.getElementById("risk-first");

            var vel = 0;
            var ang = 0;
            var target_ang = 0;
            var valid_click = true;
            var valid_done_click = false;
            var choices = [];
            var hw = 150;

            function result_click(force_end) {
                if(!valid_done_click)
                    return;

                valid_done_click = false;
                choices.push({ result: result, choices: all_choices[trial_num] });
                trial_num += 1;
                if(trial_num == num_trials || force_end) {
                    console.log(choices);
		            display_element.empty();
                    jsPsych.finishTrial({ choices: choices });
                    return;
                }
                ang = 0;
                vel = 0;
                draw();
                low.innerHTML = "$" + all_choices[trial_num].fixed;
                progress_bar.html((trial_num + 1) + "/" + num_trials);
                var p = (100 * (trial_num + 1) / num_trials); 
                progress_bar.css("width", Math.max(p, 5).toFixed(0) + "%");
                result_cont.animate({ "opacity": "0" }, 500, function() {
                    $(this).css("display", "none");
                    valid_click = true;
                    low.disabled = false;
                });
            }
                    
            result_no.onclick = function() { result_click(false); }
			
            low.onclick = function() {
                if(!valid_click)
                    return;

                valid_click = false;
                low.disabled = true;
                $.post(SITE_PREFIX + "/risk.php", { "choice": "fixed", "index": trial_num }, function(r) {
                    result = all_choices[trial_num].fixed;
                    outcome = "You chose the fixed reward of $" + result + ".";
                    console.log("showing in low");
                    show(false);
                });
            };
            
            function show(is_spin) {
                console.log("show");
                money.html(outcome);

                if(one_trial)
                    result_done.onclick = function() { result_click(true); }
                else
                    result_done.onclick = function() { result_click(false); }

                if(one_trial && is_spin && trial_num < num_trials - 1) {
                    result_done.innerHTML = "Yes";
                    result_no.style.display = "inline-block";
                } else {
                    result_done.innerHTML = "Done";
                    result_no.style.display = "none";
                }
                result_cont.css("display", "block").animate({ "opacity": "1" }, 500, function() {
                    valid_done_click = true;
                });
                $(result_done).focus();
            }
        
            var c = null;
            if(canvas.getContext) {
                canvas.width = 2 * hw;
                canvas.height = 2 * hw;
                c = canvas.getContext("2d");
            } else {
                canvas.style.border = "1px solid #444";
                canvas.style.borderRadius = "5px";
                canvas.style.width = (2 * hw) + "px";
                canvas.style.height = (2 * hw) + "px";
            }

            canvas.onclick = function() {
                if(!valid_click)
                    return;

                valid_click = false;
                low.disabled = true;
                $.post(SITE_PREFIX + "/risk.php", { "choice": "wheel", "index": trial_num }, function(r) {
                    var j_r = JSON.parse(r);
                    result = j_r.result;
                    target_ang = parseInt(10000 * j_r.fraction); // 10000 corresponds to 2 * PI radians
                    outcome = "The spinner returned $" + result + ".";
                    if(one_trial && trial_num < num_trials - 1)
                        outcome += " Would you like to choose this value?";
                    vel = 100;
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
                if(!c) {
                    return;
                }

                c.clearRect(0, 0, 2 * hw, 2 * hw);

                c.strokeStyle = "black";
                c.lineWidth = 2;
               
                var spin_vals = all_choices[trial_num].spinner;
                var start_sector_ang = 0.0;
              
                for(var i = 0; i < spin_vals.length; i++) {
                    var d_sector_ang = spin_vals[i].fraction * 2.0 * Math.PI;

                    c.fillStyle = "rgb(0, 0, " + parseInt(200.0 * spin_vals[i].fraction + 55.0) + ")";
                    c.beginPath();
                    c.arc(hw, hw, hw, start_sector_ang, start_sector_ang + d_sector_ang, false); 
                    c.lineTo(hw, hw);
                    c.fill();
                    c.stroke();
                    
                    start_sector_ang += d_sector_ang;
                }
                
                c.textAlign = "center";
                c.textBaseline = "middle";
                c.font = "16px 'Roboto', sans-serif";
                var rect_w = 50;
                var rect_h = 32;
                var rect_r = 5;
               
                start_sector_ang = 0.0; 
                for(var i = 0; i < spin_vals.length; i++) {
                    var d_sector_ang = spin_vals[i].fraction * 2.0 * Math.PI;
                    var x = Math.cos(start_sector_ang + 0.5 * d_sector_ang) * hw * 0.5 + hw;
                    var y = Math.sin(start_sector_ang + 0.5 * d_sector_ang) * hw * 0.5 + hw;
                
                    c.fillStyle = "black";
                    roundedRect(x - 0.5 * rect_w, y - 0.5 * rect_h, rect_w, rect_h, rect_r);

                    c.fillStyle = "white";
                    c.fillText("$" + spin_vals[i].value, x, y);

                    start_sector_ang += d_sector_ang;
                }

                c.save();
                
                c.translate(hw, hw);
                c.rotate(Math.PI * 2 * (ang / 10000));
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
                
                if(!c) {
                    show(true);
                    return;
                }

                ang += vel;

                if(ang < target_ang)
                    window.requestAnimationFrame(spin);
                else {
                    ang = target_ang;
                    draw();
                    show(true);
                }
            }
           
            draw();
        });
	}

	return plugin;
})();
