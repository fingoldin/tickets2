<?php

if(!session_id())
	session_start();

require("./includes.php");

//require("../data.php");

function checkAnswer($phase, $group, $sequence, $answer)
{
	logging("checkAnswer called with " . $phase . " " . $group . " " . $sequence . " " . $answer);

	if(!isset($_SESSION["points"]) || !isset($_SESSION["checked"]) || !isset($_SESSION["checked_assoc"]) || !isset($_SESSION["testing_data"]))
		logging("Something not set in checkAnswer");
	else if(!in_array($sequence, $_SESSION["checked"][$phase][$group]))
	{
		$p = get_points($phase, $group, $sequence, $answer);
        echo $p;

		$totalp = $_SESSION["points"][$phase] + $p;
		if($totalp > $_SESSION["max_points"])
		{
			logging("checkAnswer has exceeded the max of " . $_SESSION["max_points"] . " points, previously " . $_SESSION["points"][$phase] . " and tried to be " . $data["points"]);

			$_SESSION["points"][$phase] = $_SESSION["max_points"];
		}
		else
			$_SESSION["points"][$phase] += $p;
		//$data["place"] = array_search($a, $arr) + 1;

		logging("checkAnswers called successfully, phase " . $phase . " gained " . $p . " points and now has " . $_SESSION["points"][$phase] . " points");

		array_push($_SESSION["checked"][$phase][$group], $sequence);
		$_SESSION["checked_assoc"][$phase][$group][$sequence] = $p;
	}
}

if(isset($_POST["phase"]) && isset($_POST["group"]) && isset($_POST["sequence"]) && isset($_POST["answer"]))
	checkAnswer(intval($_POST["phase"]), intval($_POST["group"]), intval($_POST["sequence"]), intval($_POST["answer"]));
else
	logging("Something not set in check.php");

?>
