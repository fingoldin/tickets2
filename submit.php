<?php

if(!session_id())
        session_start();

require("./includes.php");

if(isset($_SESSION["start_time"]) && isset($_SESSION["finished"]) && $_SESSION["finished"] == 0 && isset($_POST["data"]) && isset($_SESSION["points"]) && isset($_SESSION["workerId"]) && isset($_SESSION["assignmentId"]))
{
	logging("Submit.php called and OK");

	$time = get_time();

	$arr = [
		"start_time" => $_SESSION["start_time"],
		"end_time" => $time,
		"points_phase0" => $_SESSION["points"][0],
		"points_phase1" => $_SESSION["points"][1],
		"age" => 0,
		"gender" => "m",
        "tries" => 1,
		"during" => "Nothing",
		"worker_id" => $_SESSION["workerId"],
		"assignment_id" => $_SESSION["assignmentId"],
		"data" => json_decode($_POST["data"], true),
		"bonus" => round(0.1 * $_SESSION["risk_final"]), // in cents
	];

	foreach($arr["data"] as $trial)
	{
		if($trial["trial_type"] == "age")
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
			$arr["bonus"] += $gb;
			if($arr["bonus"] != $b)
			{
				logging("The total points don't match up: the trial says " . $b . " while get_bonus says " . $arr["bonus"]);
			}
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
