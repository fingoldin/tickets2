jsPsych.plugins["risk2"] = (function()
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
        var fixed_order = trial.fixed_order || false;
        console.log("Fixed order:");
        console.log(fixed_order);

        $.post(SITE_PREFIX + "/get_risk_one.php", { "example": example, "fixed_order": (fixed_order ? "true" : "false") }, (res) => {
          var all_choices = JSON.parse(res);
          console.log(all_choices);
          var trial_num = 0;
          var num_trials = all_choices.length;

          display_element.empty();

          if(num_trials < 1) {
              return jsPsych.finishTrial({ none: true });
          }

          var php_site = SITE_PREFIX + "/utils/risk_one.php";
          if(example) {
              php_site = SITE_PREFIX + "/utils/risk_one_example.php";
          }

          var post_site = SITE_PREFIX + "/risk_one.php";

          var choice_site = SITE_PREFIX + "/risk_choice.php";

          var seq = document.getElementById("risk-seq");
          seq.style.opacity = "1";
          var seq_num = document.getElementById("risk-seq-num");
          seq_num.innerHTML = "1";
          document.getElementById("risk-seq-total").innerHTML = num_trials;

          display_element.load(php_site, function() {
              display_element.find("#risk-count").html(num_trials);

              var progress_bar_wrap;
              var progress_bar;
              //progress_bar_wrap = display_element.find("#risk-progress-wrap");
              //progress_bar = display_element.find("#risk-progress");

              //progress_bar.css("width", (100 / num_trials).toFixed(0) + "%");
              //progress_bar.html("1/" + num_trials);

              var result = 180;
              var outcome = "";

              var canvas = document.getElementById("risk-canvas");
              var low;
              low = document.getElementById("risk-low-button");
              low.innerHTML = "$" + all_choices[trial_num].fixed;

              var result_cont = display_element.find("#risk-result");
              var money = display_element.find("#risk-result-money");
              var result_done = document.getElementById("risk-result-done");

              var first_box = document.getElementById("risk-first");

              var max_vel = 400;
              var vel = 0;
              var ang = 0;
              var target_ang = 0;
              var valid_click = true;
              var valid_done_click = false;
              var choices = [];
              var hw = 200;
              var max_points = 1500;
              var pad = 60;
              var startTime = gt();
              var times = [];

              var chose_fixed = false;

                function setup() {
                  if(!example && fixed_order) {
                    $("#midpage").css("display", "none");
                    $("#risk-main").css("display", "block");
                  }
                  ang = 0;
                  vel = 0;
                  draw();
                  var p = (100 * (trial_num + 1) / num_trials);
                  low.innerHTML = "$" + all_choices[trial_num].fixed;
                  //progress_bar.html((trial_num + 1) + "/" + num_trials);
                  //progress_bar.css("width", Math.max(p, 5).toFixed(0) + "%");
                  seq.style.opacity = "1";
                  seq_num.innerHTML = trial_num + 1;
  result_cont.css("display", "none");
  valid_click = true;
  low.disabled = false;
  startTime = gt();
                }

              if(!example && fixed_order) {
                $("#midpage-cont").click(setup);
              }

              function result_click() {
                  if(!valid_done_click)
                      return;

                  valid_done_click = false;
                  choices.push({ result: result, chose_fixed: chose_fixed, seq_choice_idx: all_choices[trial_num].seq_choice_idx, seq_idx: all_choices[trial_num].seq_idx });
                  trial_num += 1;
                  if(trial_num == num_trials) {
                    console.log(times);
                    console.log(choices);
                      jsPsych.finishTrial({ choices: choices, times: times });
                  } else {
                      if(!example && fixed_order && trial_num % (num_trials / 4) == 0) {
                        $("#midpage").css("display", "block");
                        $("#risk-main").css("display", "none");
                      } else {
                        setup();
                      }
                  }
              }

              result_done.onclick = function() { result_click(); }

              low.onclick = function() {
                  if(!valid_click)
                      return;

                  times.push(gt() - startTime);
                  valid_click = false;
                  low.disabled = true;
                  chose_fixed = true;
                  function low_post() {
                      result = all_choices[trial_num].fixed;
                      //let points = Math.min(max_points, Math.max(0, Math.round(max_points * (result - 125) / (195 - 125))));
                      outcome = "You chose the fixed reward of $" + result + ". You will earn $" + (result / 100).toFixed(3) + " if this trial is chosen.";
                      show(false);
                  }

                  if(example) {
                      low_post();
                  } else {
                      $.post(post_site, { "choice": "fixed", "index": trial_num, "seq_idx": all_choices[trial_num].seq_idx, "seq_choice_idx": all_choices[trial_num].seq_choice_idx, "fixed_order": (fixed_order ? "true" : "false") }, low_post);
                  }
              };

              function show(is_spin) {
                  seq.style.opacity = "0";
                  money.html(outcome);

                  if(is_spin && trial_num < num_trials - 1) {
                      result_done.innerHTML = "Ok";
                  } else {
                      result_done.innerHTML = "Done";
                  }
                  result_cont.css("display", "block");
	      valid_done_click = true;
                  $(result_done).focus();
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

              canvas.onclick = function() {
                  if(!valid_click)
                      return;

                  times.push(gt() - startTime);
                  valid_click = false;
                  low.disabled = true;
                  chose_fixed = false;
                  function canvas_click(r) {
                      var r_idx = parseInt(r);

                      var frac;
                      result = all_choices[trial_num].spinner[r_idx].value;
                      frac = all_choices[trial_num].spinner[r_idx].fraction;
                      if(r_idx > 0) {
                          var sliced;
                          sliced = all_choices[trial_num].spinner.slice(0, r_idx);
                          frac += sliced.reduce(function(total, cur) { return total + cur.fraction; }, 0.0);
                      }

                      target_ang = parseInt(10000 * (1.0 + frac)); // 10000 corresponds to 2 * PI radians
//                      let points = Math.min(max_points, Math.max(0, Math.round(max_points * (result - 125) / (195 - 125))));
                      outcome = "The spinner returned $" + result + ". You will earn $" + (result / 100).toFixed(3) + " if this trial is chosen.";
                      vel = max_vel;
                      spin();
                  }

                  if(example) {
                      canvas_click("40");
                  } else {
                      $.post(post_site, { "choice": "wheel", "index": trial_num, "seq_idx": all_choices[trial_num].seq_idx, "seq_choice_idx": all_choices[trial_num].seq_choice_idx, "fixed_order": (fixed_order ? "true" : "false") }, canvas_click);
                  }
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

                  c.clearRect(0, 0, 2 * (hw + pad), 2 * (hw + pad));

                  c.strokeStyle = "black";
                  c.lineWidth = 2;

                  var spin_vals;
                  spin_vals = all_choices[trial_num].spinner;
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

                function getColor(v, mintick, factor) {
                  let vmax = 200;
                  let vmin = 120;
                  let f = (((factor + 1) * (v - vmin) / (vmax - vmin)) / 1.5) % 1;
                  let rgb = HSVtoRGB(f, 0.5, 1);

                  return "rgb(" + rgb.r + ", " + rgb.g + ", " + rgb.b + ")";
                }

                  for(var i = 0; i < spin_vals.length; i++) {
                      var d_sector_ang = spin_vals[i].fraction;
                      if(spin_vals[i].show && spin_vals[i].value >= all_choices[trial_num].min_tick) {
                          var sang = -0.5 * Math.PI + 2.0 * Math.PI * last_sector_ang;
                          var fang = -0.5 * Math.PI + 2.0 * Math.PI * (start_sector_ang + d_sector_ang);
                          var color_int = parseInt(65535.0 * (fang - sang) / (2.0 * Math.PI));
                          c.fillStyle = getColor(parseInt(spin_vals[i].value), all_choices[trial_num].min_tick, 0); //all_choices[trial_num].seq_choice_idx);
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
                  last_sector_ang = 0.0;
                  let first = true;
                  console.log(all_choices[trial_num]);
                  console.log(spin_vals[0]);
                  for(var i = 0; i < spin_vals.length; i++) {
                      var d_sector_ang = spin_vals[i].fraction * 2.0 * Math.PI;
                      let hl = 5;
                      if(spin_vals[i].show && spin_vals[i].value >= all_choices[trial_num].min_tick) {
                          hl = 10;
                          c.lineWidth = 3;
                          if(i === spin_vals.length - 1 || first) {
                            c.lineWidth = 5;
                            hl = 15;
                          }
                          c.strokeStyle = "black";
                      } else {
                          c.lineWidth = 2;
                          c.strokeStyle = "white";
                      }
//                      if(first || start_sector_ang > 0.06) {
                        if(first) first = false;
                        c.save();

                        c.translate(hw + pad, hw + pad);
                        c.rotate(start_sector_ang + d_sector_ang);
                        c.translate(-hw - pad, -hw - pad);

                        c.beginPath();
                        c.moveTo(hw + pad, pad - hl);
                        c.lineTo(hw + pad, pad + hl);
                        c.stroke();

                        c.restore();
  //                    }

                      start_sector_ang += d_sector_ang;
                  }

                  start_sector_ang = -0.5 * Math.PI;
                  let last_label_ang = 0.0;
                  first = true;
                  let zero_so_far = true;
                  for(var i = 0; i < spin_vals.length; i++) {
                      var d_sector_ang = spin_vals[i].fraction * 2.0 * Math.PI;
                      if(spin_vals[i].show && (zero_so_far || spin_vals[i].value >= all_choices[trial_num].min_tick)) {
                          var x = Math.cos(start_sector_ang + d_sector_ang) * (hw + 0.5 * rect_w) + hw + pad;
                          var y = Math.sin(start_sector_ang + d_sector_ang) * (hw + 0.5 * rect_w) + hw + pad;

//                          c.fillStyle = "black";
//                          roundedRect(x - 0.5 * rect_w, y - 0.5 * rect_h, rect_w, rect_h, rect_r);

                          let add = "";
                          let first_nonzero = (zero_so_far && spin_vals[i].fraction != 0.0);
                          if(first_nonzero) {
                            zero_so_far = false;
                          }
                          if(i === spin_vals.length - 1) {
                            //add = " $" + spin_vals[0].value;
                            x -= 20;
                          } else if(i === 0 || spin_vals[i].value == all_choices[trial_num].min_tick) {
                            x += 20;
                          }

                          if(first_nonzero || last_label_ang > 0.1 || spin_vals[i].value == all_choices[trial_num].min_tick) {
                            c.fillStyle = "black";
                            c.fillText("$" + spin_vals[i].value + add, x, y);
                            last_label_ang = 0.0;
                          }
                      }
                      start_sector_ang += d_sector_ang;
                      last_label_ang += d_sector_ang;
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
     });
	}

	return plugin;
})();
