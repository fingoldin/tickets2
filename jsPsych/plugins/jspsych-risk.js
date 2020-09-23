jsPsych.plugins["risk"] = (function()
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

        // Is this an example trial (doesn't count for money?)
        var example = trial.example || false;
        var spinner = trial.spinner || [];
        var total_trials = trial.total_trials || 1;
        var trial_idx = trial.trial_idx || 0;
        var seq_idx = trial.seq_idx || 0;
        var one_trial = true;

        var all_choices = trial.all_choices;

        var trial_num = 0;
        var num_trials = all_choices.length;

        display_element.empty();

        if(num_trials < 1) {
            jsPsych.finishTrial({ result: "Error" });
        }

        var php_site = SITE_PREFIX + "/utils/risk.php";
        if(example) {
            php_site = SITE_PREFIX + "/utils/risk_example.php";
        } else {
            php_site = SITE_PREFIX + "/utils/risk.php";
        }

        var post_site = SITE_PREFIX + "/risk.php";

        var seq = document.getElementById("risk-seq");
        seq.style.opacity = "1";
        var seq_num = document.getElementById("risk-seq-num");
        seq_num.innerHTML = trial_idx + 1;
        document.getElementById("risk-seq-total").innerHTML = total_trials;

        display_element.load(php_site, function() {
            display_element.find("#risk-count").html(num_trials);

            var progress_bar_wrap;
            var progress_bar;
            progress_bar_wrap = display_element.find("#risk-progress-wrap");
            progress_bar = display_element.find("#risk-progress");

            progress_bar.css("width", (100 / (num_trials)).toFixed(0) + "%");
            progress_bar.html("1/" + (num_trials));

            var result = 180;
            var outcome = "";
            var times = [];
            var startTime = 0;

            var canvas = document.getElementById("risk-canvas");
            var low;
//            low = document.getElementById("risk-low-button");
//            low.innerHTML = "$" + all_choices[0];

            var result_cont = display_element.find("#risk-result");
            var money = display_element.find("#risk-result-money");
            var result_done = document.getElementById("risk-result-done");
            var result_no = document.getElementById("risk-result-no");

            var first_box = document.getElementById("risk-first");

            var max_vel = 400;
            var vel = 0;
            var ang = 0;
            var target_ang = 0;
            var valid_click = true;
            var valid_done_click = false;
            var hw = 200;
            var pad = 40;

            var chose_fixed = false;
            var max_points = 1500;

            var canvas_click = function() {
                if(!valid_click)
                    return;

                startTime = gt();

                valid_click = false;
                //low.disabled = true;
                result = all_choices[trial_num];
                var r_idx = result - 120;

                var frac = spinner[r_idx].fraction;
                var sliced= spinner.slice(0, r_idx);
                frac += sliced.reduce(function(total, cur) { return total + cur.fraction; }, 0.0);

                target_ang = parseInt(10000 * (1.0 + frac)); // 10000 corresponds to 2 * PI radians
                outcome = "The spinner returned $" + result + ".";
                vel = max_vel;
                if(trial_num < num_trials - 1) {
                  outcome += " Would you like to choose this value or spin the wheel again?";
                  spin();
                } else {
                    var prices = all_choices.slice(0);
                    let points = Math.min(max_points, Math.max(0, Math.round(max_points * (all_choices[trial_num] - 120) / (200 - 120))));
                    prices.sort(function(a, b){return a - b});
                    let out = "You will win $" + (points / 1000).toFixed(3) + " if this trial is chosen.";
                    if(example) {
                      outcome += " " + out; //+ " You earned $0.300.";
                      spin();
                    } else {
                      $.post(post_site, { "ticket": trial_num, "index": trial_idx }, (p) => {
                        outcome += " " + out;// + " You earned $" + (parseInt(p) * 0.001).toFixed(3) + ".";
                        spin();
                      });
                    }
                }
            };

            function result_click(force_end) {
                if(!valid_done_click)
                    return;

                times.push(gt() - startTime);

                valid_done_click = false;
                trial_num += 1;
                if(trial_num == num_trials || force_end) {
                    function finish() {
                        display_element.empty();
                        data = { result: result, trial_idx: trial_idx, all_choices: all_choices, seq_idx: seq_idx, choice_idx: (trial_num - 1), times: times };
                        jsPsych.finishTrial(data);
                    }
                    if(!chose_fixed && trial_num < num_trials) {
                      function cont(p) {
                        var prices = all_choices.slice(0);
                        prices.sort(function(a, b){return a - b});
                        let points = Math.min(max_points, Math.max(0, Math.round(max_points * (all_choices[trial_num - 1] - 120) / (200 - 120))));

                        let out = "You will win $" + (points / 1000).toFixed(3) + " if this trial is chosen.";
                        money.html(out)// + " You earned $" + (parseInt(p) * 0.001).toFixed(3) + " in real money.");
                        result_done.innerHTML = "Ok";
                        result_no.style.display = "none";
                        result_done.onclick = finish;
                      }
                      if(example) {
                        cont("300");
                      } else {
                        $.post(post_site, { "ticket": (trial_num - 1), "index": trial_idx }, cont);
                      }
                    } else {
                      finish();
                    }
                } else {
                    ang = 0;
                    vel = 0;
                    var p = (100 * (trial_num + 1) / (num_trials));
                    //low.innerHTML = "$" + all_choices[0];
                    progress_bar.html((trial_num + 1) + "/" + (num_trials));
                    progress_bar.css("width", Math.max(p, 5).toFixed(0) + "%");
                    seq.style.opacity = "1";
                    valid_click = true;
                    canvas_click();
			result_cont.css("display", "none");
                    draw();
                }
            }

            result_done.onclick = function() { result_click(true); }
            result_no.onclick = function() { result_click(false); }

            /*low.onclick = function() {
                if(!valid_click)
                    return;

                valid_click = false;
                low.disabled = true;
                function low_post(r) {
                  console.log(r);
                    result = all_choices[0];
                    outcome = "You chose the fixed reward of $" + result + ".";
                    var earned = parseInt(r);
                    outcome += " You earned $" + (earned * 0.001).toFixed(3) + ".";
                    chose_fixed = true;
                    show(false);
                }

                if(example) {
                    low_post("300");
                } else {
                    $.post(post_site, { "ticket": "fixed", "index": trial_idx }, low_post);
                }
            };*/

            function show(is_spin) {
                seq.style.opacity = "0";
                money.html(outcome);

                if(is_spin && trial_num < num_trials - 1) {
                    result_done.innerHTML = "Choose this";
                    result_no.style.display = "inline-block";
                } else {
                    result_done.innerHTML = "Done";
                    result_no.style.display = "none";
                }
                result_cont.css("display", "block");
	    valid_done_click = true;
            }

            var c = null;
            if(canvas.getContext) {
                canvas.width = 2 * hw + 2 * pad;
                canvas.height = 2 * hw + 2 * pad;
                c = canvas.getContext("2d");
            } else {
                canvas.style.border = "1px solid #444";
                canvas.style.borderRadius = "5px";
                canvas.style.width = (2 * hw + 2 * pad) + "px";
                canvas.style.height = (2 * hw + 2 * pad) + "px";
            }


            canvas.onclick = canvas_click;

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

                c.clearRect(0, 0, 2 * (hw + pad), 2 * (hw + pad));

                c.strokeStyle = "black";
                c.lineWidth = 2;

                var spin_vals = spinner;
                var start_sector_ang = 0.0;
                var last_sector_ang = start_sector_ang;

                function HSVtoRGB(h, s, v) {
                    var r, g, b, i, f, p, q, t;
                    if (arguments.length === 1) {
                        s = h.s, v = h.v, h = h.h;
                    }
                    i = Math.floor(h * 6);
                    f = h * 6 - i;
                    p = v * (1 - s);
                    q = v * (1 - f * s);
                    t = v * (1 - (1 - f) * s);
                    switch (i % 6) {
                        case 0: r = v, g = t, b = p; break;
                        case 1: r = q, g = v, b = p; break;
                        case 2: r = p, g = v, b = t; break;
                        case 3: r = p, g = q, b = v; break;
                        case 4: r = t, g = p, b = v; break;
                        case 5: r = v, g = p, b = q; break;
                    }
                    return {
                        r: Math.round(r * 255),
                        g: Math.round(g * 255),
                        b: Math.round(b * 255)
                    };
                }

                function getColor(v) {
                  let vmax = 200;
                  let vmin = 120;
                  let f = (v - vmin) / (vmax - vmin);
                  let rgb = HSVtoRGB(f, 1, 1);

                  return "rgb(" + rgb.r + ", " + rgb.g + ", " + rgb.b + ")";
                }

                for(var i = 0; i < spin_vals.length; i++) {
                    var d_sector_ang = spin_vals[i].fraction;
                    if(spin_vals[i].show) {
                        var sang = -0.5 * Math.PI + 2.0 * Math.PI * last_sector_ang;
                        var fang = -0.5 * Math.PI + 2.0 * Math.PI * (start_sector_ang + d_sector_ang);
                        var color_int = parseInt(65535.0 * (fang - sang) / (2.0 * Math.PI));
                        c.fillStyle = getColor(parseInt(spin_vals[i].value));
                        c.beginPath();
                        c.arc(hw + pad, hw + pad, hw, sang, fang, false);
                        c.lineTo(hw + pad, hw + pad);
                        c.fill();
                        c.stroke();

                        last_sector_ang = start_sector_ang + d_sector_ang;
                    }
                    start_sector_ang += d_sector_ang;
                }

                c.textAlign = "center";
                c.textBaseline = "middle";
                c.font = "16px 'Roboto', sans-serif";
                var rect_w = 50;
                var rect_h = 32;
                var rect_r = 5;

                c.lineWidth = 2;

                start_sector_ang = 0.0;
                for(var i = 0; i < spin_vals.length; i++) {
                    var d_sector_ang = spin_vals[i].fraction * 2.0 * Math.PI;
                    var hl = 5;
                    if(spin_vals[i].show) {
                        hl = 10;
                        c.lineWidth = 3;
                        c.strokeStyle = "white";
                    } else {
                        c.lineWidth = 2;
                        c.strokeStyle = "black";
                    }

                    c.save();

                    c.translate(hw + pad, hw + pad);
                    c.rotate(start_sector_ang + d_sector_ang);
                    c.translate(-hw - pad, -hw - pad);

                    c.beginPath();
                    c.moveTo(hw + pad, pad - hl);
                    c.lineTo(hw + pad, pad + hl);
                    c.stroke();

                    c.restore();

                    start_sector_ang += d_sector_ang;
                }

                start_sector_ang = -0.5 * Math.PI;
                for(var i = 0; i < spin_vals.length; i++) {
                    var d_sector_ang = spin_vals[i].fraction * 2.0 * Math.PI;
                    if(spin_vals[i].show) {
                        var x = Math.cos(start_sector_ang + d_sector_ang) * (hw + 0.5 * rect_w) + hw + pad;
                        var y = Math.sin(start_sector_ang + d_sector_ang) * (hw + 0.5 * rect_w) + hw + pad;
                        if(i == spin_vals.length - 1) {
                          x -= 23;
                        }
                        else if(i == 0) {
                          x += 23;
                        }

                        c.fillStyle = "black";
                        roundedRect(x - 0.5 * rect_w, y - 0.5 * rect_h, rect_w, rect_h, rect_r);

                        c.fillStyle = "white";
                        c.fillText("$" + spin_vals[i].value, x, y);
                    }
                    start_sector_ang += d_sector_ang;
                }

                c.save();

                c.translate(hw + pad, hw + pad);
                c.rotate(Math.PI * 2 * (ang / 10000));
                c.translate(-hw - pad, -hw - pad);

                c.lineWidth = 6;
                c.lineCap = "round";
                c.strokeStyle = "white";

                c.beginPath();
                c.moveTo(hw + pad, hw + pad);
                c.lineTo(hw + pad, 10 + pad);
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
