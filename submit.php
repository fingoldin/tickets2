<?php

if(!session_id())
        session_start();

require("./includes.php");

if(isset($_SESSION["start_time"]) && isset($_SESSION["finished"]) && $_SESSION["finished"] == 0 && isset($_POST["data"]) && isset($_SESSION["points"]) && isset($_SESSION["phase_order"]) && isset($_POST["worker_id"]) && isset($_POST["assignment_id"]))
{
	logging("Submit.php called and OK");

	$time = get_time();

	echo "got here";

	$arr = [
		"start_time" => $_SESSION["start_time"],
		"end_time" => $time,
		"points_phase0" => $_SESSION["points"][0],
		"points_phase1" => $_SESSION["points"][1],
		"phase_order" => $_SESSION["phase_order"],
		"age" => 0,
		"gender" => "m",
		"tries" => 1,
		"during" => "Nothing",
		"worker_id" => $_POST["worker_id"],
		"assignment_id" => $_POST["assignment_id"],
		"data" => json_decode($_POST["data"], true),
		"bonus" => 0
	];

	foreach($arr["data"] as $trial)
	{
		if($trial["trial_type"] == "ticket-choose" && $trial["sequence"] > -1)
		{
//			var_dump($trial);
			$arr2 = $_SESSION["testing_data"][$trial["phase"]][$trial["sequence"]];
			sort($arr2);

			//echo "tp: " . $trial["points"] . " arr: " . $_SESSION["checked_assoc"][$trial["phase"]][$trial["sequence"]];
			if($trial["points"] !== $_SESSION["checked_assoc"][$trial["phase"]][$trial["sequence"]] ||
			   $trial["place"] !== array_search($trial["result"], $arr2))
			{
				logging("The trial with sequence " . $trial["sequence"] . " in phase " . $trial["phase"] . " doesn't have the correct points");
				exit;
			}
		}
		else if($trial["trial_type"] == "age")
		{
			$arr["age"] = $trial["age"];
			$arr["gender"] = $trial["gender"];
		}
		else if($trial["trial_type"] == "instructions_check")
			$arr["tries"] = $trial["tries"];
		else if($trial["trial_type"] == "final")
		{
			$arr["during"] = $trial["during"];

			$b = intval($trial["bonus"]);
			$gb = get_bonus(intval($arr["points_phase0"]) + intval($arr["points_phase1"]));
			if($gb !== $b)
			{
				logging("The total points don't match up: the trial says " . $b . " while get_bonus says " . $gb);
				exit;
			}
			$arr["bonus"] = $b;
		}
	}

	subject_save_response($arr);
	mysql_save_response($arr);
	log_save_response($arr);
	//grant_bonus($arr["bonus"], $arr["worker_id"], $arr["assignment_id"]);
	//subject_save_response($arr);

	$_SESSION["finished"] = 1;

	logging("Finished submitting");

       	$_SESSION = array();
       	session_destroy();
}
else
	logging("Something not set in submit.php");

?>
