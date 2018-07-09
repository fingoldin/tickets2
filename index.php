<?php

//error_reporting(E_ALL);

session_start();

require("./includes.php");

startSession();

//echo get_bonus(227);

store_url();

//phpinfo();

grant_bonuses();
//grant_bonus(1, "A3UEJQ4EJB9DBD", "3180JW2OT55Z5VXVLET9XPRFURAJ50");


//startSession();

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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="/christiane/tickets3/jsPsych/jspsych.js"></script>
<script src="/christiane/tickets3/jsPsych/plugins/jspsych-text.js"></script>
<script src="/christiane/tickets3/jsPsych/plugins/jspsych-html.js"></script>
<script src="/christiane/tickets3/jsPsych/plugins/jspsych-animation.js"></script>
<script src="/christiane/tickets3/jsPsych/plugins/jspsych-number-animation.js"></script>
<script src="/christiane/tickets3/jsPsych/plugins/jspsych-html-animation.js"></script>
<script src="/christiane/tickets3/jsPsych/plugins/jspsych-bar-choose.js"></script>
<script src="/christiane/tickets3/jsPsych/plugins/jspsych-ticket-choose.js"></script>
<script src="/christiane/tickets3/jsPsych/plugins/jspsych-final.js"></script>
<script src="/christiane/tickets3/jsPsych/plugins/jspsych-age.js"></script>
<script src="/christiane/tickets3/jsPsych/plugins/jspsych-instructions_check.js"></script>
<script src="/christiane/tickets3/jsPsych/plugins/jspsych-training_avg.js"></script>
<script src="/christiane/tickets3/jsPsych/plugins/jspsych-call-function.js"></script>
<script src="/christiane/tickets3/jsPsych/plugins/jspsych-workerid.js"></script>
<script src="/christiane/tickets3/jsPsych/plugins/jspsych-special_sequence.js"></script>
<script src="/christiane/tickets3/jsPsych/plugins/jspsych-points-update.js"></script>
<script src="/christiane/tickets3/utils/general.js"></script>
<script src="/christiane/tickets3/utils/bar-choose-plugin.js"></script>
<script src="/christiane/tickets3/utils/jquery.transform2d.js"></script>
<script src="/christiane/tickets3/utils/json2.js"></script>
<script src="/christiane/tickets3/utils/browserdetect.js"></script>
<script src="/christiane/tickets3/utils/select2.full.min.js"></script>
<link href="/christiane/tickets3/utils/select2.min.css" rel="stylesheet" type="text/css"></link>
<link href="/christiane/tickets3/jsPsych/css/jspsych.css" rel="stylesheet" type="text/css"></link>
<link href="/christiane/tickets3/utils/general.css" rel="stylesheet" type="text/css"></link>
<link href="/christiane/tickets3/utils/bar-choose-plugin.css" rel="stylesheet" type="text/css"></link>
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<link href="/christiane/tickets3/utils/age.css" rel="stylesheet" type="text/css"></link>
<link href="/christiane/tickets3/utils/consent.css" rel="stylesheet" type="text/css"></link>
<link href="/christiane/tickets3/utils/start.css" rel="stylesheet" type="text/css"></link>
<link href="/christiane/tickets3/utils/points.css" rel="stylesheet" type="text/css"></link>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=0.5, user-scalable=no">

<title>Ticket prices</title>

<script type="text/javascript">

var consent_trial = {
	type: "html",
	url: "/christiane/tickets3/utils/consent.html",
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
	url: "/christiane/tickets3/utils/instructions.html",
	instructions: "id-int",
	check: "id-check",
	right: "c-right",
	wrong: "c-wrong",
	cont_btn: "continue1",
}

var start_trial = {
	type: "html",
	url: "/christiane/tickets3/utils/start.html",
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
	instructions: "Imagine you would see MAXVAL more tickets for your trip to Canada.",
	subtitle: "Please drag the bar or type in the input field to determine the amount of tickets <br> that are in the equivalent price range for this trip. <br><br>Press continue when you are sure of your answers.",
//	categories: ["$135 - $150", "$151 - $165", "$166 - 180", "$181 - $195", "$196 - $210", "$211 - $225", "$226 - $240"],
	min_val: 0,
	max_val: 20,
	phase: 0,
	number: 0
	//answers: animanswers
}

var testing_instructions_trial = {
	type: "html",
        url: "/christiane/tickets3/utils/testing.html",
        cont_btn: "testingstart",
    on_start: function(trial) {
        $("#wheel").css("display", "none");
    }
}

// Second testing instructions (after example sequence)
var testing_instructions2_trial = {
	type: "html",
	url: "/christiane/tickets3/utils/testing_after.html",
	cont_btn: "testingstart"
}

// Second bar graph to see learning
var training_trial2 = {
	type: "bar-choose",
        instructions: "Imagine you would see yet another MAXVAL tickets to Canada.",
        subtitle: "Please drag the bar or type in the input field to determine the amount of tickets <br> that are in the equivalent price range for this trip. Press continue when you are sure of your answers.",
//        categories: ["$135 - $150", "$151 - $165", "$166 - 180", "$181 - $195", "$196 - $210", "$211 - $225", "$226 - $240"],
        min_val: 0,
        max_val: 20,
	phase: 0,
	number: 1
	//answers: animanswers
}

var mid_test_trial = {
    type: "html",
    url: "/christiane/tickets3/utils/mid_test.html",
    cont_btn: "continue"
}

var training_trial3 = {
	type: "bar-choose",
        instructions: "Imagine you would see yet another MAXVAL tickets to Canada.",
        subtitle: "Please drag the bar or type in the input field to determine the amount of tickets <br> that are in the equivalent price range for this trip. Press continue when you are sure of your answers.",
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
	url: "/christiane/tickets3/utils/start2.html",
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
        url: "/christiane/tickets3/utils/testing2.html",
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
    url: "/christiane/tickets3/utils/mid_test.html",
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

var final_trial = {
	type: "final"
}

function preload()
{
	for(var i = 0; i < NUM_TICKETS; i++)
	{
		TICKET_IMAGES[0][i] = new Image();
		TICKET_IMAGES[0][i].src = "/christiane/tickets3/utils/tickets/" + window.ticketprefix + "/ticket" + (i+1) + ".jpg";
		TICKET_IMAGES[0][i].classList += " ticket-img";

		TICKET_IMAGES[1][i] = new Image();
                TICKET_IMAGES[1][i].src = "/christiane/tickets3/utils/tickets/" + window.ticketprefix + "/2ticket" + (i+1) + ".jpg";
                TICKET_IMAGES[1][i].classList += " ticket-img";
	}
}

function init_exp()
{
//	if(Function('/*@cc_on return document.documentMode===10@*/')()){ $("body").addClass("ie10"); }

	if(document.getElementsByTagName("html")[0].classList == "ie-warning" || (BrowserDetect.browser == "Explorer" && BrowserDetect.version <= 7))
	{
		document.getElementsByTagName("body")[0].innerHTML = "<p class='ie-nope'>It's time to upgrade your browser. Seriously, stop using this clunky thing.";
		return;
	}

	BrowserDetect.init();
	if(BrowserDetect.browser == "Explorer" && BrowserDetect.version == 10)
        	$("html").addClass("ie10");

	if(BrowserDetect.browser == "Explorer" && BrowserDetect.version == 8)
		window.ticketprefix = "ie8";
	else if(BrowserDetect.browser == "Explorer" && BrowserDetect.version < 11)
                window.ticketprefix = "ie9";
	else
		window.ticketprefix = "normal";

	preload();

	//window.viewportUnitsBuggyfill.init({refreshDebounceWait: 250});

	var timeline = [];

	$.post("/christiane/tickets3/get.php", { f7g12d: "y" }, function(d) {

	var animdata = [];
	var animanswers = [];
	var animdata2 = [];
	var animanswers2 = [];
	var testing_data = [];
	var p2_testing_data = [];
	//var training_ranges = [];

	var da = JSON.parse(d);
	testing_data = da["testing"][0];
	p2_testing_data = da["testing"][1];
    
	animdata = da["training"][0];
	animdata2 = da["training"][1];
	animanswers = da["answers"][0];
	animanswers2 = da["answers"][1];
	//training_ranges = da["training_ranges"];
    
    var training_sort = parseInt(da["training_sort"]);
    var threshold = parseFloat(da["training_threshold"]);

    console.log(animanswers);

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
	
    training_trial.max_val = training_sort;
	training_trial2.max_val = training_sort;
	training_trial3.max_val = training_sort;
    p2_training_trial.max_val = training_sort;
	p2_training_trial2.max_val = training_sort;
    p2_training_trial3.max_val = training_sort;

    training_trial.pass_threshold = threshold;
    p2_training_trial.pass_threshold = threshold;
	
    var assignment_id = "<?= $_SESSION['assignmentId'] ?>";

	//timeline.push(special_sequence_trial);

	timeline.push(consent_trial);
	timeline.push(age_trial);

	workerid_trial.on_finish = function(data) {
		$.post("/christiane/tickets3/setworkerid.php", { id : data.worker_id });//, function(d) { console.log(d); });
	};
  	timeline.push(workerid_trial);


	timeline.push(instructions_trial);
  	timeline.push(start_trial);

    var passed = false;

	for(var i = 0; i < animdata.length; i++)
	{
        for(var j = 0; j < animdata[i].length; j++)
        {
/*            timeline.push({
                    type: "number-animation",
                    prices: animdata[i][j],
                    phase: 0,
                    continue_message: "Next",
                    repeat_num: i,
                    sequence_num: j,
                    passed: function() { return passed; }
            });

            timeline.push({
                type: "training_avg",
                phase: 0,
                sequence_num: j,
                repeat_num: i,
                passed: function() { return passed; },
                sequence: animdata[i][j],
                min_val: training_ranges[0][0],
                max_val: training_ranges[0][1]
            });*/
        }

    	timeline.push(Object.assign({ repeat_num: i,
                    passed: function() { return passed; },
                    on_finish: function(data) {
                        if(data.passed && data.repeat_num > 0) {
                            passed = true;
	                        $("#wheel").css("display", "block");
                        }
                    }                  
        }, training_trial));
    }

    timeline.push(testing_instructions_trial);
	
    // example testing sequence
	timeline.push({ type: "ticket-choose",
			phase: 0,
			row: -1,
            showpoints: false,
			prices: [184, 180, 224, 165, 181, 199, 185, 193, 218, 126],
			continue_mesage: "Finish",
			sequence: ""
	});

	timeline.push(testing_instructions2_trial);

//    testing_data.length = 4;

	for(var i = 0; i < testing_data.length; i++)
	{
        	timeline.push({ type: "ticket-choose",
				prices: testing_data[i],
				row: i,
				phase: 0,
                       	        continue_message: "Next sequence",
				sequence: "In sequence <span>" + (i + 1) + "</span> out of <span>" + testing_data.length + "</span>",
			//	points: function() { return points_counter.p[0]; },
				showpoints: true,
				on_finish: function(data) {
					$.post("/christiane/tickets3/check.php", { phase: 0, sequence: data.sequence, answer: data.result });
				}
		});

        if(i == Math.floor((testing_data.length - 1) / 2)) {
            timeline.push(points_update_trial);
            timeline.push(training_trial2);
            timeline.push(mid_test_trial);
        }
	}
	timeline[timeline.length-1].continue_message = "Finish";
	timeline[timeline.length-1].on_finish = function(data) {
		$.post("/christiane/tickets3/check.php", { phase: 0, sequence: data.sequence, answer: data.result });
	};

	timeline.push(training_trial3);
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
                passed: function() { return passed2; }
                sequence: animdata2[i][j],
                min_val: training_ranges[1][0],
                max_val: training_ranges[1][1]
            });
        }

    	timeline.push(Object.assign({ repeat_num: i,
                    passed: function() { return passed2; },
                    on_finish: function(data) {
                        if(data.passed) {
                            passed2 = true;  
	                        $("#wheel").css("display", "block");
                        }
                    }                  
        }, p2_training_trial));
    }

        timeline.push(p2_testing_instructions_trial);

    //p2_testing_data.length = 4;

	//for(var i = 0; i < 2; i++)
	for(var i = 0; i < p2_testing_data.length; i++)
        {
                timeline.push({ type: "ticket-choose",
				prices: p2_testing_data[i],
                                row: i,
				phase: 1,
				continue_message: "Next sequence",
                                sequence: "In sequence <span>" + (i + 1) + "</span> out of <span>" + p2_testing_data.length + "</span>",
                        //      points: function() { return points_counter.p[1]; },
                                showpoints: true,
                                on_finish: function(data) {
					$.post("/christiane/tickets3/check.php", { phase: 1, sequence: data.sequence, answer: data.result });
                } });

                if(i == Math.floor((p2_testing_data.length - 1) / 2)) {
                    timeline.push(p2_points_update_trial);
                    timeline.push(p2_training_trial2);
                    timeline.push(p2_mid_test_trial);
                }
        }
        timeline[timeline.length-1].continue_message = "Finish";
        timeline[timeline.length-1].on_finish = function(data) {
                $.post("/christiane/tickets3/check.php", { phase: 1, sequence: data.sequence, answer: data.result });
        };

	timeline.push(p2_training_trial3);
*/
	timeline.push(special_sequence_trial);

	timeline.push(final_trial);

/*	var completion_code = {
		type: 'html',
		url: 'confirmation_code.html'
	};
        timeline.push(completion_code);
*/

//save data



			// function to save data
	/*		function saveData(filename, filedata){
				$.ajax({
					type:'post',
					cache: false,
					url: '/christiane/tickets3/save_data.php', // this is the path to the above PHP script
					data: {filename: filename, filedata: filedata},
				// complete: function() {
				// 				window.location.href = "confirmation_code.html";
				// 			}
				});
			}


	var save_data = {
		type: 'call-function',
		func: function()
		{
			saveData(subject_id + '_output.csv', jsPsych.data.getDataAsCSV())
		}
		,timing_post_trial: 0
	};

	timeline.push(save_data);*/

	// save data end

	//var worker_id = "<?= $_SESSION['workerId']; ?>";
	//var assignment_id = "<?= $_SESSION['assignmentId']; ?>";


	$("#wheel").css("display", "none");

	jsPsych.init({
		timeline: timeline,
		display_element: $("#jspsych-main"),
		on_finish: function(data) {
			$("#jspsych-main").empty().load("/christiane/tickets3/confirmation_code.html");
			//console.log(worker_id);
			$.post("/christiane/tickets3/submit.php", { data: JSON.stringify(data), assignment_id: assignment_id }, function(r) {
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
	url: "/christiane/tickets3/utils/accept.html"
};

var outside_trial = {
	type: "html",
	url: "/christiane/tickets3/utils/outside.html"
};

function init()
{
	/*var tinfo = jsPsych.turk.turkInfo();

	if(tinfo.outsideTurk)
		init_preview(outside_trial);
	else if(tinfo.previewMode)
		init_preview(accept_trial);
	else
		init_exp(tinfo.workerId, tinfo.assignmentId);*/

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
	<div id="jspsych-main"></div>
</body>

</html>
