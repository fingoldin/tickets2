jsPsych.plugins["ticket-choose"] = (function()
{
	var plugin = {};

	function gt()
	{
		var d = new Date();
		return d.getTime();
	}

	plugin.trial = function(display_element, trial)
	{
		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

		trial.prices = trial.prices || [];
        trial.fixed = trial.fixed || 0;
		trial.continue_message = trial.continue_message || "Continue";
		trial.sequence = trial.sequence || "";
		trial.showpoints = trial.showpoints || false;
		trial.phase = trial.phase || 0;
		trial.group = trial.group || 0;

		//console.log("Trial: " + trial.prices);

		//console.log(trial.prices);

		var num_prices = trial.prices.length;
		if(!num_prices)
			jsPsych.finishTrial({ "result": "error" });

		var times = [];

		display_element.html("");
        
        display_element.load(SITE_PREFIX + "/utils/risk.html", function() {
            var progress_bar = display_element.find("#ticket-choose-progress"); 
            progress_bar.css("width", (100 / num_prices).toFixed(0) + "%");
            progress_bar.html("1/" + num_prices);
            
            var price_num = -1;
			var next_num = 0;

            var result = 180;
            
            var canvas = document.getElementById("risk-canvas");
            var low = document.getElementById("risk-low-button");
            var result_done = document.getElementById("risk-result-done");
            
            var result_cont = display_element.find("#risk-result");
            var money = display_element.find("#risk-result-money");
            
            var spinner_result = display_element.find("#risk-spinner-result");
            var spinner_result_text = display_element.find("#risk-spinner-result-text");
            var spinner_result_yes = document.getElementById("risk-spinner-result-yes");
            var spinner_result_no = document.getElementById("risk-spinner-result-no");

            var hw = 150;
            canvas.width = 2 * hw;
            canvas.height = 2 * hw;

            if(canvas.getContext) {
                function next_price()
                {
                    next_num++;
                    price_num++;
                    if(price_num >= num_prices) {
                        price_num = num_prices - 1;

                        times[num_prices - 1] = gt() - next_price.startTime;

                        select_price();
                    }
                    else if(price_num === 0) {
                        next_price.startTime = gt();
                    }
                    else {
                        times[price_num-1] = gt() - next_price.startTime;

                        progress_bar.html((price_num + 1) + "/" + num_prices);
                        progress_bar.css("width", (100 * (price_num + 1) / num_prices).toFixed(0) + "%");
                        next_price.startTime = gt();
                    }
                }

                function end_trial(ps, r, ti)
                {
                    if(r == -1) {
			            jsPsych.finishTrial({ "result": "error" });
                        return;
                    }

                    var trial_data = {
                        "result": trial.prices[price_num],
                        "points": ps,
                        "place": r,
                        "phase": trial.phase,
                        "sequence": trial.row,
                        "prices": trial.prices,
                        "times": ti,
                        "next_num": next_num,
                        "group": trial.group
                    };

                    jsPsych.finishTrial(trial_data);
                }
                
                result_done.onclick = function() {
                    result_cont.animate({ "opacity": "0" }, 500, function() {
                        jsPsych.finishTrial();
                    });
                };
            
                function end() {
                    money.html("Your ticket will cost $" + result.toFixed(0) + ".");
                    result_cont.css("display", "block").animate({ "opacity": "1" }, 500);
                }
                
                low.onclick = function() {
                    $.post(SITE_PREFIX + "/risk.php", { "choice": "fixed" }, function(r) {
                        result = parseInt(r);
                        end();
                    });
                };

                spinner_result_yes.onclick = function() {
                    spinner_result.css("display", "none");
                    end();
                    console.log("yes");
                };
                
                spinner_result_no.onclick = function() {
                    spinner_result.css("display", "none");
                    low.disabled = false;
                };

                function choose() {
                    spinner_result_text.html("Accept this $" + result.toFixed(0) + " ticket?");
                    spinner_result.css("display", "block");
                }
            
                var max = 220
                var min = 140
                var a = -1; // -3 acceleration of pointer, radians per second ^ 2
                var vel = 696; // 569
                var ang = 0;
                var dt = 1;

                var c = canvas.getContext("2d");

                canvas.onclick = function() {
                    low.disabled = true;
                    $.post(SITE_PREFIX + "/risk.php", { "choice": "wheel" }, function(r) {
                        result = parseInt(r);
                        vel = Math.round(Math.sqrt(2000 * (22 + result)));
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
                    
                    c.save();
                    
                    c.translate(hw, hw);
                    c.rotate(Math.PI * 2 * ((ang / 1000) % (max - min + 1)) / (max - min + 1));
                    c.translate(-hw, -hw);
                    
                    c.lineWidth = 3;
                    c.strokeStyle = "red";
                    var l = 15;
                    c.beginPath();
                    c.moveTo(hw, buf);
                    c.lineTo(hw, buf - l);
                    c.moveTo(hw, 2 * hw - buf);
                    c.lineTo(hw, 2 * hw - buf + l);
                    c.moveTo(buf, hw);
                    c.lineTo(buf - l, hw);
                    c.moveTo(2 * hw - buf, hw);
                    c.lineTo(2 * hw - buf + l, hw);
                    c.closePath();
                    c.stroke();
                    
                    c.fillStyle = "red";
                    var red = true;
                    var s = 2.0 * Math.PI;
                    var m = max;
                    if((max - min + 1) % 2 == 0) {
                        s /= (max - min + 1);
                    }
                    else {
                        s /= (max - min + 2);
                        m += 1;
                    }

                    var buf = 40;

                    for(var i = min; i <= m; i++) {
                        if(!red)
                            c.fillStyle = "red";
                        else
                            c.fillStyle = "black";

                        red = !red;

                        c.beginPath();
                        c.moveTo(hw, hw);
                        c.arc(hw, hw, hw - buf, i * s, (i + 1) * s, false);
                        c.closePath();
                        c.fill();
                    }
                    
                    c.restore();

                    c.fillStyle = "black";
                    c.beginPath();
                    c.arc(hw, hw, 0.7 * (hw - buf), 0, 2 * Math.PI, false);
                    c.fill();
                    
                    c.fillStyle = "white";
                    c.beginPath();
                    c.arc(hw, hw, 10, 0, 2 * Math.PI, false);
                    c.fill();
                    
                    c.fillStyle = "black";
                    var rhw = 30;
                    var rhh = 16;
                    var rr = 5;
                    roundedRect(hw - rhw, 0.5 * (buf - 2 * rhh), 2 * rhw, 2 * rhh, rr);
                    
                    c.textAlign = "center";
                    c.textBaseline = "middle";
                    c.fillStyle = "white";
                    c.font = "16px 'Roboto', sans-serif";
                    var n = (Math.floor(ang / 1000) % (max - min + 1)) + min;
                    c.fillText("$" + n.toFixed(0), hw, rhh + 0.5 * (buf - 2 * rhh));

                    c.lineWidth = 6;
                    c.lineCap = "round";
                    c.strokeStyle = "white";

                    c.beginPath();
                    c.moveTo(hw, hw);
                    c.lineTo(hw, buf + 10);
                    c.stroke();
                }

                function spin() {
                    draw();
                    //var dt = tdiff();
                    ang += vel * dt;
                    vel += a * dt;
                    console.log(ang);

                    if(vel > 0.0)
                        window.requestAnimationFrame(spin);
                    else
                        choose();
                }
                
                draw();
            } else {
			    jsPsych.finishTrial({ "result": "error" });
            }
        });
	}

  	return plugin;
})();
