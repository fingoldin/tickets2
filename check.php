<?php

if(!session_id())
	session_start();

require("./includes.php");

//require("../data.php");

function checkAnswer($phase, $sequence, $answer)
{
	logging("checkAnswer called with " . $phase . " " . $sequence . " " . $answer);

	if(!isset($_SESSION["points"]) || !isset($_SESSION["checked"]) || !isset($_SESSION["checked_assoc"]) || !isset($_SESSION["testing_data"]))
		logging("Something not set in checkAnswer");
	else if(!in_array($sequence, $_SESSION["checked"][$phase]))
	{
		$data = [
			"points" => 0
		//	"place" => 1
		];

		$p = get_points($phase, $sequence, $answer);

		$data["points"] = $_SESSION["points"][$phase] + $p;
		if($data["points"] > 180)
		{
			logging("checkAnswer has exceeded the max of 180 points, previously " . $_SESSION["points"][$phase] . " and tried to be " . $data["points"]);

			$data["points"] = 180;
			$_SESSION["points"][$phase] = 180;
		}
		else
			$_SESSION["points"][$phase] += $p;
		//$data["place"] = array_search($a, $arr) + 1;

		logging("checkAnswers called successfully, phase " . $phase . " gained " . $p . " points and now has " . $_SESSION["points"][$phase] . " points");

		array_push($_SESSION["checked"][$phase], $sequence);
		$_SESSION["checked_assoc"][$phase][$sequence] = $p;

		echo json_encode($data);
	}
}

if(isset($_POST["phase"]) && isset($_POST["sequence"]) && $_POST["sequence"] > -1 && isset($_POST["answer"]))
	checkAnswer($_POST["phase"], $_POST["sequence"], $_POST["answer"]);
else
	logging("Something not set in check.php");

?>
