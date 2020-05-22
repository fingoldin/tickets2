<?php

require("./includes.php");

if(!session_id())
	session_start();

if(isset($_POST["f7g12d"]) && isset($_SESSION["testing_data"]) && isset($_SESSION["got_data"]) && isset($_SESSION["training_data"]) && isset($_SESSION["training_answers"]) && $_SESSION["got_data"] == 0 && isset($_SESSION["training_categories"]) && isset($_SESSION["training_avg_ranges"]) && isset($_SESSION["training_sort_total"]) && isset($_SESSION["training_threshold"]) && isset($_SESSION["risk_options"]))
{
	logging("Get.php OK");

  $risk_data = $_SESSION["testing_data"][0][0];
  shuffle($risk_data);

	$arr = [
		"testing" => $_SESSION["testing_data"],
    "risk_data" => $risk_data,
		"training" => $_SESSION["training_data"],
		"training_ranges" => $_SESSION["training_avg_ranges"],
		"answers" => $_SESSION["training_answers"],
		"categories" => $_SESSION["training_categories"],
        "training_sort" => $_SESSION["training_sort_total"],
	    "training_threshold" => $_SESSION["training_threshold"],
        "risk_options" => $_SESSION["risk_options"]
    ];

	echo json_encode($arr);
	$_SESSION["got_data"] = 1;
}
else
	logging("Something not set in get.php");
?>
