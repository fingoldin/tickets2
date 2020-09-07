<?php

session_start();

require("./includes.php");

startSession();

store_url();

//phpinfo();

grant_bonuses();

$preview = true;

// CHANGE THIS TO EITHER GET THE WORKER AND ASSIGNMENT IDS FROM $_GET OR GENERATE RANDOM ONES
$use_random = !isset($_GET["assignmentID"]);

if($use_random)
{
	$assignmentid = rand(1, 1000000);

	$preview = false;
	//startSession();
	$_SESSION["assignmentId"] = $assignmentid;
	$_SESSION["workerId"] = "NOT_SET";
}
else
{
	if(isset($_GET["assignmentID"]) && $_GET["assignmentID"] != "ASSIGNMENT_ID_NOT_AVAILABLE")
	{
		$preview = false;

		//startSession();
		$_SESSION["assignmentId"] = htmlspecialchars($_GET["assignmentID"]);
		$_SESSION["workerId"] = "NOT_SET";
	}
}

logging("Setup done");

$site_prefix = $_SESSION["site_prefix"];

?>

<!DOCTYPE html>

<!--[if lt IE 8]>
<html class="ie-warning">
<![endif]-->

<!--[if IE 8]>
<html class="ie8">
<![endif]-->

<!--[if IE 9]>
<html class="ie9">
<![endif]-->

<!--[if !IE]> -->
<html class="normal">
<!-- <![endif]-->

<head>

<script type="text/javascript">
SITE_PREFIX = "<?= $site_prefix ?>"
</script>

<script src="<?= $site_prefix ?>/utils/jquery.min.js"></script>
<script src="<?= $site_prefix ?>/utils/jquery-ui.min.js"></script>
<script src="<?= $site_prefix ?>/utils/jquery.transform2d.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/jspsych.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-text.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-html.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-animation.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-number-animation.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-html-animation.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-bar-choose.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-ticket-choose.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-final.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-age.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-instructions_check.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-training_avg.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-call-function.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-workerid.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-special_sequence.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-points-update.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-risk.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-risk2.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-riskonechoose.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-mainchoose.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-riskchoose.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-survey-multi.js"></script>
<script src="<?= $site_prefix ?>/utils/general.js"></script>
<script src="<?= $site_prefix ?>/utils/bar-choose-plugin.js"></script>
<script src="<?= $site_prefix ?>/utils/json2.js"></script>
<script src="<?= $site_prefix ?>/utils/browserdetect.js"></script>
<script src="<?= $site_prefix ?>/utils/select2.full.min.js"></script>
<link href="<?= $site_prefix ?>/utils/select2.min.css" rel="stylesheet" type="text/css"></link>
<link href="<?= $site_prefix ?>/jsPsych/css/jspsych.css" rel="stylesheet" type="text/css"></link>
<link href="<?= $site_prefix ?>/utils/general.css" rel="stylesheet" type="text/css"></link>
<link href="<?= $site_prefix ?>/utils/bar-choose-plugin.css" rel="stylesheet" type="text/css"></link>
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<link href="<?= $site_prefix ?>/utils/age.css" rel="stylesheet" type="text/css"></link>
<link href="<?= $site_prefix ?>/utils/consent.css" rel="stylesheet" type="text/css"></link>
<link href="<?= $site_prefix ?>/utils/start.css" rel="stylesheet" type="text/css"></link>
<link href="<?= $site_prefix ?>/utils/points.css" rel="stylesheet" type="text/css"></link>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=0.5, user-scalable=no">

<title>Ticket prices</title>

<script type="text/javascript">

var consent_trial = {
	type: "html",
	url: "<?= $site_prefix ?>/utils/consent.html",
	cont_btn: "agree2"
}

var age_trial = {
	type: "age"
}

var workerid_trial = {
	type: "workerid"
}

var special_sequence_trial = {
	type: "special_sequence"
}

var instructions_trial = {
	type: "instructions_check",
	url: "<?= $site_prefix ?>/utils/instructions.php",
	instructions: "id-int",
  followup: "id-followup",
	check: "id-check",
	right: "c-right",
	wrong: "c-wrong",
	cont_btn1: "continue1",
	cont_btn2: "continue2",
}

var start_trial = {
	type: "html",
	url: "<?= $site_prefix ?>/utils/start.html",
	cont_btn: "start1"
}

var animation_trial = {
	type: "number-animation",
	//prices: animdata,
	phase: 0,
	continue_message: "Next step"
}

var training_trial = {
	type: "bar-choose",
	instructions: "Imagine you would see MAXVAL more price offers.",
	subtitle: "Please drag the bar or type in the input field to determine the number of price offers <br> that are in the equivalent price range. <br><br>Press continue when you are sure of your answers.",
//	categories: ["$135 - $150", "$151 - $165", "$166 - 180", "$181 - $195", "$196 - $210", "$211 - $225", "$226 - $240"],
	min_val: 0,
	max_val: 20,
	phase: 0,
	number: 0
	//answers: animanswers
}

var testing_instructions_trial = {
	type: "html",
        url: "<?= $site_prefix ?>/utils/testing.html",
        cont_btn: "testingstart",
    on_start: function(trial) {
        $("#wheel").css("display", "none");
    }
}

// Second testing instructions (after example sequence)
var testing_instructions2_trial = {
	type: "html",
	url: "<?= $site_prefix ?>/utils/testing_after.html",
	cont_btn: "testingstart"
}

// Second bar graph to see learning
var training_trial2 = {
	type: "bar-choose",
        instructions: "Imagine you would see another MAXVAL stock prices.",
        subtitle: "Please drag the bar or type in the input field to determine the number of stock prices <br> that are in the equivalent price range for this trip. Press continue when you are sure of your answers.",
//        categories: ["$135 - $150", "$151 - $165", "$166 - 180", "$181 - $195", "$196 - $210", "$211 - $225", "$226 - $240"],
        min_val: 0,
        max_val: 20,
	phase: 0,
	number: 1
	//answers: animanswers
}

var mid_test_trial = {
    type: "html",
    url: "<?= $site_prefix ?>/utils/mid_test.html",
    cont_btn: "continue"
}

var training_trial3 = {
	type: "bar-choose",
        instructions: "Imagine you would see another MAXVAL stock prices.",
        subtitle: "Please drag the bar or type in the input field to determine the number of stock prices <br> that are in the equivalent price range for this trip. Press continue when you are sure of your answers.",
//        categories: ["$135 - $150", "$151 - $165", "$166 - 180", "$181 - $195", "$196 - $210", "$211 - $225", "$226 - $240"],
        min_val: 0,
        max_val: 20,
	phase: 0,
	number: 2
	//answers: animanswers
}

// Second training phase instructions
var p2_start_trial = {
	type: "html",
	url: "<?= $site_prefix ?>/utils/start2.html",
	cont_btn: "start2"
}

// Second training phase
var p2_animation_trial = {
        type: "number-animation",
        //prices: animdata2,
	phase: 1,
        continue_message: "Next step"
}

var p2_training_trial = {
        type: "bar-choose",
        instructions: "Imagine you would see MAXVAL more tickets for your trip to Mexico City.",
        subtitle: "Please drag the bar or type in the input field to determine the amount of tickets <br> that are in the equivalent price range for this trip. Press continue when you are sure of your answers.",
//        categories: ["$105 - $130", "$131 - $155", "$156 - $180", "$181 - $205", "$206 - $230", "$231 - $255", "$256 - $280"],
        min_val: 0,
        max_val: 20,
	phase: 1,
	number: 0
	//answers: animanswers2
}

var p2_testing_instructions_trial = {
        type: "html",
        url: "<?= $site_prefix ?>/utils/testing2.html",
        cont_btn: "testingstart",
    on_start: function(trial) {
        $("#wheel").css("display", "none");
    }
}

// Second bar graph to see learning
var p2_training_trial2 = {
        type: "bar-choose",
        instructions: "Imagine you would see yet another MAXVAL tickets to Mexico City.",
        subtitle: "Please drag the bar or type in the input field to determine the amount of tickets <br> that are in the equivalent price range for this trip. Press continue when you are sure of your answers.",
//        categories: ["$105 - $130", "$131 - $155", "$156 - $180", "$181 - $205", "$206 - $230", "$231 - $255", "$256 - $280"],
        min_val: 0,
        max_val: 20,
	phase: 1,
	number: 1
	//answers: animanswers2
}

var p2_mid_test_trial = {
    type: "html",
    url: "<?= $site_prefix ?>/utils/mid_test.html",
    cont_btn: "continue"
}

var p2_training_trial3 = {
        type: "bar-choose",
        instructions: "Imagine you would see yet another MAXVAL tickets to Mexico City.",
        subtitle: "Please drag the bar or type in the input field to determine the amount of tickets <br> that are in the equivalent price range for this trip. Press continue when you are sure of your answers.",
//        categories: ["$105 - $130", "$131 - $155", "$156 - $180", "$181 - $205", "$206 - $230", "$231 - $255", "$256 - $280"],
        min_val: 0,
        max_val: 20,
	phase: 1,
	number: 2
	//answers: animanswers2
}

var points_update_trial = {
    type: "points-update"
}

var p2_points_update_trial = {
    type: "points-update"
}

var risk_trial = {
    type: "risk",
    one_trial: true,
    spinner: [],
    all_choices: []
}

var risk_one_trial = {
    type: "risk2",
    one_trial: true,
    all_choices: []
}

var risk_example_trial = {
    type: "risk",
    example: true,
    one_trial: true,
    spinner: [],
    total_trials: 1,
    trial_idx: 0,
    all_choices: [ 160, 170, 192, 140, 183, 166, 169, 135, 195, 189 ]
}

var risk_one_example_trial = {
    type: "risk2",
    one_trial: true,
    example: true,
    all_choices: []
}

var riskonechoose_trial = {
	type: "riskonechoose"
}

var riskchoose_trial = {
	type: "riskchoose"
}

var mainchoose_trial = {
	type: "mainchoose"
}

var final_trial = {
	type: "final"
}

var risk_midexample_trial = {
	type: "html",
	url: "<?= $site_prefix ?>/utils/risk_midexample.html",
	cont_btn: "continue"
}

var risk_one_midexample_trial = {
	type: "html",
	url: "<?= $site_prefix ?>/utils/risk_one_midexample.html",
	cont_btn: "continue"
}

var risk3_instructions_trial = {
	type: "html",
	url: "<?= $site_prefix ?>/utils/risk3_instructions.html",
	cont_btn: "continue"
}

var risk_instructions_trial = {
	type: "html",
	url: "<?= $site_prefix ?>/utils/risk_instructions.html",
	cont_btn: "continue"
}

var main_instructions_trial = {
	type: "html",
	url: "<?= $site_prefix ?>/utils/main_instructions.html",
	cont_btn: "continue"
}

var risk_prompts = ["Admitting that your tastes are different from those of a friend.",
										"Going camping in the wilderness.",
										"Betting a day's income at the horse races.",
                    "Investing 10% of your annual income in a moderate growth diversified fund.",
										"Drinking heavily at a social function.",
										"Taking some questionable deductions on your income tax return.",
										"Disagreeing with an authority figure on a major issue.",
										"Betting a day's income at a high-stakes poker game.",
										"Having an affair with a married man/woman.",
										"Passing off somebody else's work as your own.",
										"Going down a ski run that is beyond your ability.",
                    "Investing 5% of your annual income in a very speculative stock.",
										"Going whitewater rafting at high water in the spring.",
                    "Betting a day's income on the outcome of a sporting event.",
										"Engaging in unprotected sex.",
										"Revealing a friend's secret to someone else.",
										"Driving a car without wearing a seat belt.",
                    "Investing 10% of your annual income in a new business venture.",
										"Taking a skydiving class.",
										"Riding a motorcycle without a helmet.",
										"Choosing a career that you truly enjoy over a more secure one.",
										"Speaking your mind about an unpopular issue in a meeting at work.",
										"Sunbathing without sunscreen.",
										"Bungee jumping off a tall bridge.",
										"Piloting a small plane.",
										"Walking home alone at night in an unsafe area of town.",
										"Moving to a city far away from your extended family.",
										"Starting a new career in your mid-thirties.",
										"Leaving your young children alone at home while running an errand.",
										"Not returning a wallet you found that contains $200."];

var risk_options_full = ["Extremely Unlikely", "Moderately Unlikely", "Somewhat Unlikely", "Not Sure",
                    "Somewhat Likely", "Moderately Likely", "Extremely Likely"];

var risk_options = ["1", "2", "3", "4", "5", "6", "7"];

var risksurvey_trial = {
    type: "survey-multi",
    preamble: "</span>For each of the following statements, please indicate the <b>likelihood</b> that you would engage in the described activity or behavior if you were to find yourself in that situation, by selecting one of the following options for each:</span>",
    title: "Please take this survey to continue",
    questions: risk_prompts,
    required: [],
    horizontal: [],
    options: []
}

var closure_prompts = ["I don't like situations that are uncertain.",
                       "I dislike questions which could be answered in many different ways.",
                       "I find that a well ordered life with regular hours suits my temperament.",
                       "I feel uncomfortable when I don't understand the reason why an event occurred in my life.",
                       "I feel irritated when one person disagrees with what everyone else in a group believes.",
                       "I don't like to go into a situation without knowing what I can expect from it.",
                       "When I have made a decision, I feel relieved.",
                       "When I am confronted with a problem, I’m dying to reach a solution very quickly.",
                       "I would quickly become impatient and irritated if I would not find a solution to a problem immediately.",
                       "I don't like to be with people who are capable of unexpected action.",
                       "I dislike it when a person's statement could mean many different things.",
                       "I find that establishing a consistent routine enables me to enjoy life more.",
                       "I enjoy having a clear and structured mode of life.",
                       "I do not usually consult many different opinions before forming my own view.",
                       "I dislike unpredictable situations."];

var closure_options_full = ["Strongly Disagree", "Moderately Disagree", "Slightly Disagree",
                       "Slightly Agree", "Moderately Agree", "Strongly Agree"];

var closure_options = ["1", "2", "3", "4", "5", "6"];

var closuresurvey_trial = {
    type: "survey-multi",
    preamble: "<span>Read each of the following statements and indicate how much you agree with each according to your beliefs and experiences, by selecting one of the following options for each:</span>",
    title: "Please take this survey to continue",
    questions: closure_prompts,
    required: [],
    horizontal: [],
    options: []
}

function preload()
{
	for(var i = 0; i < NUM_TICKETS; i++)
	{
		TICKET_IMAGES[0][i] = new Image();
		TICKET_IMAGES[0][i].src = "<?= $site_prefix ?>/utils/tickets/" + window.ticketprefix + "/ticket" + (i+1) + ".jpg";
		TICKET_IMAGES[0][i].classList += " ticket-img";

//		TICKET_IMAGES[1][i] = new Image();
//                TICKET_IMAGES[1][i].src = "<?= $site_prefix ?>/utils/tickets/" + window.ticketprefix + "/2ticket" + (i+1) + ".jpg";
//                TICKET_IMAGES[1][i].classList += " ticket-img";
	}
}

function init_exp()
{
//	if(Function('/*@cc_on return document.documentMode===10@*/')()){ $("body").addClass("ie10"); }
	/*BrowserDetect.init();

	if(document.getElementsByTagName("html")[0].classList == "ie-warning" || (BrowserDetect.browser == "Explorer" && BrowserDetect.version <= 7))
	{
		document.getElementsByTagName("body")[0].innerHTML = "<p class='ie-nope'>It's time to upgrade your browser. Seriously, stop using this clunky thing.</p>";
		return;
	}

	if(BrowserDetect.browser == "Explorer" && BrowserDetect.version == 10)
        	$("html").addClass("ie10");

	if(BrowserDetect.browser == "Explorer" && BrowserDetect.version == 8)
		window.ticketprefix = "ie8";
	else if(BrowserDetect.browser == "Explorer" && BrowserDetect.version < 11)
                window.ticketprefix = "ie9";
	else*/
		window.ticketprefix = "normal";

	preload();

	//window.viewportUnitsBuggyfill.init({refreshDebounceWait: 250});

	var timeline = [];

	$.post("<?= $site_prefix ?>/get.php", { f7g12d: "y" }, function(d) {

    var animdata = [];
	var animanswers = [];
	var animdata2 = [];
	var animanswers2 = [];
	var testing_data = [];
	var p2_testing_data = [];
	var training_ranges = [];

	var da = JSON.parse(d);
	testing_data = da["testing"][0];
  var risk_data = da["risk_data"];
	p2_testing_data = da["testing"][1];

	animdata = da["training"][0];
	animdata2 = da["training"][1];
	animanswers = da["answers"][0];
	animanswers2 = da["answers"][1];
	training_ranges = da["training_ranges"];

  var phase_order = da["phase_order"];

    var training_sort = da["training_sort"];
    var threshold = parseFloat(da["training_threshold"]);

    /*
    risk_one_trial.all_choices = da["risk_one_options"];
    risk_trial.all_choices = da["testing"][0][0];
    risk_trial.spinner = da["risk_options"];

    risk_one_example_trial.all_choices = da["risk_one_options"].slice(0, 2);
    risk_example_trial.all_choices = da["risk_options"].slice(0, 1);
*/
  var spinner = da["risk_options"];
  risk_example_trial.spinner = spinner;
	//animation_trial.prices = animdata;
	//p2_animation_trial.prices = animdata2;
	training_trial.answers = animanswers;
	training_trial2.answers = animanswers;
	training_trial3.answers = animanswers;
    p2_training_trial.answers = animanswers2;
	p2_training_trial2.answers = animanswers2;
    p2_training_trial3.answers = animanswers2;

	training_trial.categories = da["categories"][0];
	training_trial2.categories = da["categories"][0];
    training_trial3.categories = da["categories"][0];
	p2_training_trial.categories = da["categories"][1];
    p2_training_trial2.categories = da["categories"][1];
    p2_training_trial3.categories = da["categories"][1];

    training_trial.max_val = training_sort[0];
	training_trial2.max_val = training_sort[0];
	training_trial3.max_val = training_sort[0];
    p2_training_trial.max_val = training_sort[1];
	p2_training_trial2.max_val = training_sort[1];
    p2_training_trial3.max_val = training_sort[1];

    training_trial.pass_threshold = threshold;
    p2_training_trial.pass_threshold = threshold;

    for(idx in risk_prompts) {
        risksurvey_trial.options.push(risk_options);
        risksurvey_trial.required.push(true);
        risksurvey_trial.horizontal.push(true);
    }

    for(idx in closure_prompts) {
        closuresurvey_trial.options.push(closure_options);
        closuresurvey_trial.required.push(true);
        closuresurvey_trial.horizontal.push(true);
    }

    for(idx in closure_options_full) {
        closuresurvey_trial.preamble += "<br>" + (parseInt(idx) + 1) + ":  " + closure_options_full[idx];
    }

    for(idx in risk_options_full) {
        risksurvey_trial.preamble += "<br>" + (parseInt(idx) + 1) + ":  " + risk_options_full[idx];
    }

    var assignment_id = "<?= $_SESSION['assignmentId'] ?>";
/*    timeline.push(consent_trial);
	timeline.push(age_trial);

	workerid_trial.on_finish = function(data) {
		$.post("<?= $site_prefix ?>/setworkerid.php", { id : data.worker_id });//, function(d) { console.log(d); });
	};
  	timeline.push(workerid_trial);
  */  
    timeline.push(main_instructions_trial);

    var SET_trials = [];
    var SPT_trials = [];
    var SGT_trials = [];

    SET_trials.push(instructions_trial);
    SET_trials.push(start_trial);
    var passed = false;
  	for(var i = 0; i < animdata.length; i++)
	{
        for(var j = 0; j < animdata[i].length; j++)
        {
            SET_trials.push({
                    type: "number-animation",
                    prices: animdata[i][j],
                    phase: 0,
                    continue_message: "Next",
                    repeat_num: i,
                    sequence_num: j,
                    passed: function() { return passed; }
            });

            SET_trials.push({
                type: "training_avg",
                phase: 0,
                sequence_num: j,
                repeat_num: i,
                count: animdata[i][j].length,
                passed: function() { return passed; },
                sequence: animdata[i][j],
                min_val: training_ranges[0][0],
                max_val: training_ranges[0][1]
            });
        }

        SET_trials.push(Object.assign({ repeat_num: i,
                    passed: function() { return passed; },
                    show_wrong_mes: (i == (animdata.length - 1)),
                    on_finish: function(data) {
                        if(data.passed) {
                            passed = true;
	                        $("#wheel").css("display", "block");
                        }
                    }
        }, training_trial));
    }

    SET_trials.push(testing_instructions_trial);

    // example testing sequence
	SET_trials.push({ type: "ticket-choose",
			phase: 0,
            sequence_id: 0,
            num_sequences: 1,
			row: -1,
            max_points: <?= $_SESSION["max_points_main"] ?>,
            showpoints: false,
			prices: [164, 160, 204, 145, 181, 179, 165, 173, 198, 106],
			continue_mesage: "Finish",
			sequence: ""
	});
	SET_trials.push(testing_instructions2_trial);
	for(var i = 0; i < testing_data.length; i++)
	{
        for(var j = 0; j < testing_data[i].length; j++)
        {
        	SET_trials.push({ type: "ticket-choose",
				prices: testing_data[i][j],
                sequence_id: j,
                num_sequences: testing_data[i].length,
                max_points: <?= $_SESSION["max_points_main"] ?>,
				phase: 0,
                group: i,
                       	        continue_message: "Next sequence",
				sequence: "In sequence <span>" + (j + 1) + "</span> out of <span>" + testing_data[i].length + "</span>",
				showpoints: true,
				on_finish: function(data) {
					$.post("<?= $site_prefix ?>/check.php", { phase: 0, group: data.group, sequence: data.sequence, answer: data.result, idx: (data.next_num-1) }, function(r) { console.log(r) });
				}
		    });
        }

//        timeline.push(points_update_trial);
        //timeline.push(training_trial2);
	}
  SET_trials.push(mainchoose_trial);
  /*
	timeline.push(p2_start_trial);

    var passed2 = false;
    for(var i = 0; i < animdata2.length; i++)
	{
        for(var j = 0; j < animdata2[i].length; j++)
        {
            timeline.push({
                    type: "number-animation",
                    prices: animdata2[i][j],
                    phase: 1,
                    continue_message: "Next",
                    repeat_num: i,
                    sequence_num: j,
                    passed: function() { return passed2; }
            });

            timeline.push({
                type: "training_avg",
                phase: 1,
                sequence_num: j,
                repeat_num: i,
                count: animdata2[i][j].length,
                passed: function() { return passed2; },
                sequence: animdata2[i][j],
                min_val: training_ranges[1][0],
                max_val: training_ranges[1][1]
            });
        }

        timeline.push(Object.assign({ repeat_num: i,
                    passed: function() { return passed2; },
                    show_wrong_mes: (i == (animdata2.length - 1)),
                    on_finish: function(data) {
                        if(data.passed) {
                            passed2 = true;
	                        $("#wheel").css("display", "block");
                        }
                    }
        }, p2_training_trial));
    }

    timeline.push(p2_testing_instructions_trial);

    for(var i = 0; i < p2_testing_data.length; i++)
	{
        for(var j = 0; j < p2_testing_data[i].length; j++)
        {
        	timeline.push({ type: "ticket-choose",
				prices: p2_testing_data[i][j],
                sequence_id: j,
                num_sequences: p2_testing_data[i].length,
                max_points: <?= $_SESSION["max_points_per_seq"] ?>,
				phase: 1,
                group: i,
                       	        continue_message: "Next sequence",
				sequence: "In sequence <span>" + (j + 1) + "</span> out of <span>" + p2_testing_data[i].length + "</span>",
			//	points: function() { return points_counter.p[0]; },
				showpoints: true,
				on_finish: function(data) {
					$.post("<?= $site_prefix ?>/check.php", { phase: 1, group: data.group, sequence: data.sequence, answer: data.result, idx: (data.next_num-1) }, function(r) { console.log(r) });
				}
		    });
        }

        timeline.push(p2_points_update_trial);
        //timeline.push(p2_training_trial2);
	}

    timeline.push(closuresurvey_trial);
    timeline.push(risksurvey_trial);
   */ SGT_trials.push(risk3_instructions_trial);
    SGT_trials.push(risk_one_example_trial);
    SGT_trials.push(risk_one_midexample_trial);
    SGT_trials.push(risk_one_trial);
    SGT_trials.push(riskonechoose_trial);

    SPT_trials.push(risk_instructions_trial);
    SPT_trials.push(risk_example_trial);
    SPT_trials.push(risk_midexample_trial);
      for(var j = 0; j < risk_data.length; j++) {
        SPT_trials.push({
            type: "risk",
            one_trial: true,
            spinner: spinner,
            total_trials: risk_data.length,
            trial_idx: j,
            seq_idx: risk_data[j].seq_idx,
            all_choices: risk_data[j].data
        })
      }
    SPT_trials.push(riskchoose_trial);


  if(phase_order == 1) {
    timeline.push(...SET_trials, ...SGT_trials);
  } else {
    timeline.push(...SGT_trials, ...SET_trials);
  }

	//timeline.push(special_sequence_trial);

	timeline.push(final_trial);

	$("#wheel").css("display", "none");

	jsPsych.init({
		timeline: timeline,
		display_element: $("#jspsych-main"),
		on_finish: function(data) {
      console.log(data);
			$("#jspsych-main").empty().load("<?= $site_prefix ?>/confirmation_code.html");
			//console.log(worker_id);
			$.post("<?= $site_prefix ?>/submit.php", { data: JSON.stringify(data), assignment_id: assignment_id }, function(r) {
				console.log(r);
			});
		}
	});

	});
}

function init_preview(tri)
{
	$("#wheel").css("display", "none");

	jsPsych.init({
		timeline: [consent_trial, tri],
		display_element: $("#jspsych-main")
	});
}

var accept_trial = {
	type: "html",
	url: "<?= $site_prefix ?>/utils/accept.html"
};

var outside_trial = {
	type: "html",
	url: "<?= $site_prefix ?>/utils/outside.html"
};

function init()
{
	var prev = <?php if($preview)
				echo('true');
			else
				echo('false'); ?>;

	if(prev)
		init_preview(accept_trial);
	else
		init_exp();
}

</script>

</head>

<body onload="init()">
	<div class="wheel-loader-wrap" id="wheel"><div class="wheel-loader"></div></div>
    <div id="risk-seq" style="opacity: 0;">
        <p id="risk-seq-inner">
            Gamble <span id="risk-seq-num"></span> of <span id="risk-seq-total"></span>
        </p>
    </div>
	<div id="jspsych-main"></div>
</body>

</html>
