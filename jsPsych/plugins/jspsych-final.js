jsPsych.plugins["final"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

		trial.points = trial.points.p;

		display_element.empty();


		var wrap = document.createElement("DIV")
		var top = document.createElement("DIV");
		var bot = document.createElement("DIV");

		$(wrap).append(top).append(bot);
		display_element.append(wrap);

		function tfs(s) {
			top.style.fontSize = s + "px";
			top.style.lineHeight = Math.floor(1.4 * s) + "px";
		}

		function bfs(s) {
                        bot.style.fontSize = s + "px";
                        bot.style.lineHeight = Math.floor(1.4 * s) + "px";
                }

		tfs(55);
		bfs(35);

		top.innerHTML = "Congratulations!";
		bot.innerHTML = "The experiment is now over.";

		$(bot).css("opacity", "0");
		$(top).css("opacity", "0").animate({ "opacity": "1" }, 1000, function() {
			setTimeout(function() {
				$(bot).animate({ "opacity": "1" }, 1000);
				setTimeout(function() {
					$(wrap).animate({ "opacity": "0" }, 600, function() {
						tfs(40);
						bfs(50);
						$(bot).css("opacity", "0");

						/*var sbot = [];
						sbot[0] = trial.points;
						sbot[1] = " * ";
						sbot[2] = "0.025";
						sbot[3] = " = ";
						sbot[4] = "$" + ((trial.points[0] + trial.points[1]) * 0.025);*/

						var tp = trial.points[0] + trial.points[1];

						// in decimal (0.3 is 30%);
						var tpfraction = Math.round(100 * (tp / 300)) / 100;

						// bonus is counted in cents
						var bonus = parseInt(Math.round(100 * tpfraction * 4));

						// in dollars
						var totalmoney = 2 + bonus/100;

						// in percent
						var tppercent = parseInt(tpfraction * 100);

						bot.innerHTML = "$2 + " + tpfraction.toFixed(2) + " * $4 = $" + totalmoney.toFixed(2);
						top.innerHTML = "You scored " + trial.points[0] + " + " + trial.points[1] + " = " + tp + (tp === 1 ? " point" : " points") + " out of 300 points. <br> This is " + tppercent + " % and you receive <br> ";

						$(wrap).animate({ "opacity": "1" }, 600, function() {
							setTimeout(function() {
								//$(bot).css("opacity", "1");

								$(bot).animate({ "opacity": "1" }, 600);

								setTimeout(function() {
											var cont = document.createElement("BUTTON");
											cont.classList += " big-btn final-btn";
											cont.innerHTML = "Finish";
											setTimeout(function() {
												$(wrap).append(cont);
											}, 300);

											$(cont).click(function() {
												top.innerHTML = "";
												bot.style.fontSize = "30px";
												bot.style.lineHeight = "40px";
												bot.style.margin = "40px";
												bot.innerHTML = "Before you're done, what else were you doing (if anything) during the experiment?";
												cont.classList = "";
												cont.style.margin = "40px";

												var d = document.createElement("DIV");
												d.style.display = "block";
												var s = document.createElement("SELECT");
												s.style.width = "300px";
												s.style.maxWidth = "80vw";

												var array = ["Nothing", "Watching TV", "Eating", "Talking with a friend", "Browsing the internet", "Playing an online game", "Other"];

												for (var i = 0; i < array.length; i++)
												{
    													var option = document.createElement("option");
    													option.value = array[i];
    													option.text = array[i];
    													s.appendChild(option);
												}

												d.appendChild(s);
												wrap.insertBefore(d, cont);
												$(s).select2({ minimumResultsForSearch: -1 });

												$(cont).off("click").click(function() {
													var data = {
														during: $(s).select2("val"),
														bonus: bonus
													};

													display_element.animate({ opacity: 0 }, 200, function() {
														display_element.empty().css("opacity", "1");
														//console.log(data);
														jsPsych.finishTrial(data);
													});
												});
											});
								}, 1100);
							}, 800);
						});
					});
				}, 3000);
			}, 1500);
		});
	}

	return plugin;
})();
