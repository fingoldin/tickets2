<?php


//error_reporting(E_ALL);
//ini_set('display_errors', 'on');

session_start();

require("./includes.php");

startSession();

store_url();

//phpinfo();

//grant_bonuses();

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
<html class="normal">

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
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-ticket-choose.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-final.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-age.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-instructions_check.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-call-function.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-workerid.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-special_sequence.js"></script>
<script src="<?= $site_prefix ?>/jsPsych/plugins/jspsych-points-update.js"></script>
<script src="<?= $site_prefix ?>/utils/general.js"></script>
<script src="<?= $site_prefix ?>/utils/json2.js"></script>
<script src="<?= $site_prefix ?>/utils/select2.full.min.js"></script>
<link href="<?= $site_prefix ?>/utils/select2.min.css" rel="stylesheet" type="text/css"></link>
<link href="<?= $site_prefix ?>/jsPsych/css/jspsych.css" rel="stylesheet" type="text/css"></link>
<link href="<?= $site_prefix ?>/utils/general.css" rel="stylesheet" type="text/css"></link>
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<link href="<?= $site_prefix ?>/utils/age.css" rel="stylesheet" type="text/css"></link>
<link href="<?= $site_prefix ?>/utils/consent.css" rel="stylesheet" type="text/css"></link>
<link href="<?= $site_prefix ?>/utils/points.css" rel="stylesheet" type="text/css"></link>
<link href="<?= $site_prefix ?>/utils/start.css" rel="stylesheet" type="text/css"></link>

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
	url: "<?= $site_prefix ?>/utils/instructions.html",
	instructions: "id-int",
	check: "id-check",
	right: "c-right",
	wrong: "c-wrong",
	cont_btn: "continue1",
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
	cont_btn: "testingstart",
    on_start: function(trial) {
        $("#ticket-choose-seq").css("opacity", "0");
    }
}

var points_update_trial = {
    type: "points-update"
}

var final_trial = {
	type: "final",
    on_start: function(trial) {
        $("#ticket-choose-seq").css("opacity", "0");
    }
}

function registerImage(name)
{
    var src = "<?= $site_prefix ?>/utils/images/" + name;
    image = { src: src, img: null };
    image.img = new Image();
    image.img.alt = "The image couldn't be displayed";
    image.img.classList = "ticket-img";
    image.img.src = image.src;

    //window.registeredImages.unshift(image);

    return image;
}

/*function preloadImage()
{
    if(window.registeredImages && window.registeredImages.length) {
        var image = window.registeredImages.pop();
        console.log("PRELOADING IMAGE " + image.src);

        image.img = new Image();
        image.img.alt = "The image couldn't be displayed";
		image.img.classList = "ticket-img";
        image.img.onload = preloadImage;
        image.img.onerror = preloadImage;
        image.img.src = image.src;
    }
}*/

function init_exp()
{
	var timeline = [];
    //window.registeredImages = [];

	$.post("<?= $site_prefix ?>/get.php", { f7g12d: "y" }, function(d) {
        var testing_data = [];

        console.log(d);
        var da = JSON.parse(d);
        testing_data = da["testing"][0];
        testing_metadata = da["testing_meta"][0];

        var assignment_id = "<?= $_SESSION['assignmentId'] ?>";

        timeline.push(consent_trial);
        timeline.push(age_trial);

        workerid_trial.on_finish = function(data) {
            $.post("<?= $site_prefix ?>/setworkerid.php", { id : data.worker_id });//, function(d) { console.log(d); });
        };
        timeline.push(workerid_trial);

        timeline.push(instructions_trial);

        timeline.push(testing_instructions_trial);

        // example testing sequence
        timeline.push({ type: "ticket-choose",
                phase: 0,
                sequence_id: 0,
                num_sequences: 1,
                row: -1,
                max_points: <?= $_SESSION["max_points_per_seq"] ?>,
                image: registerImage("trial.jpg"),
                name: "Plane Ticket from San Francisco to Vancouver (Economy, One-way)",
                prices: [184, 180, 224, 165, 181, 199, 185, 193, 218, 126],
                continue_mesage: "Finish",
                sequence: ""
        });

        timeline.push(testing_instructions2_trial);

        for(var i = 0; i < testing_data.length; i++)
        {
            for(var j = 0; j < testing_data[i].length; j++)
            {
                timeline.push({ type: "ticket-choose",
                    prices: testing_data[i][j],
                    image: registerImage(testing_metadata[i][j]["img_name"]),
                    name: testing_metadata[i][j]["name"],
                    sequence_id: j,
                    num_sequences: testing_data[i].length,
                    max_points: <?= $_SESSION["max_points_per_seq"] ?>,
                    phase: 0,
                    group: i,
                                    continue_message: "Next sequence",
                    sequence: "In sequence <span>" + (j + 1) + "</span> out of <span>" + testing_data[i].length + "</span>",
                    on_finish: function(data) {
                        $.post("<?= $site_prefix ?>/check.php", { phase: 0, group: data.group, sequence: data.sequence, answer: data.result }, function(r) { console.log(r) });
                    }
                });
            }

    //        timeline.push(points_update_trial);
            //timeline.push(training_trial2);
        }

     //   preloadImage();

        //timeline.push(special_sequence_trial);

        timeline.push(final_trial);

        $("#wheel").css("display", "none");

        jsPsych.init({
            timeline: timeline,
            display_element: $("#jspsych-main"),
            on_finish: function(data) {
                $("#jspsych-main").empty().load("<?= $site_prefix ?>/confirmation_code.html");
                //console.log(worker_id);
                $.post("<?= $site_prefix ?>/submit.php", { data: JSON.stringify(data) }, function(r) {
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
		timeline: [consent_trial],
		display_element: $("#jspsych-main")
	});
}

var accept_trial = {
	type: "html",
	url: "<?= $site_prefix ?>/utils/accept.html"
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
    <div id="ticket-choose-seq" style="opacity: 0">
        <p id="ticket-choose-seq-product">
            Product <span id="ticket-choose-seq-num"></span> of <span id="ticket-choose-seq-total"></span>
        </p>
        <p id="ticket-choose-seq-avg-wrap">
            Average price: <span id="ticket-choose-seq-avg"></span>
        </p>
    </div>
	<div id="jspsych-main"></div>
</body>

</html>
