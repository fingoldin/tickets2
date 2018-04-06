<?php

require("./includes.php");

if(!session_id())
	session_start();

if(isset($_POST["f7g12d"]) && isset($_SESSION["testing_data"]) && isset($_SESSION["got_data"]) && isset($_SESSION["training_data"]) && isset($_SESSION["training_answers"]) && $_SESSION["got_data"] == 0 && isset($_SESSION["training_categories"]) && isset($_SESSION["testing_data_order"]))
{
	logging("Get.php OK");

	$arr = [
		"testing" => $_SESSION["testing_data"],
		"training" => $_SESSION["training_data"],
		"training_ranges" => $_SESSION["training_avg_ranges"],
		"answers" => $_SESSION["training_answers"],
		"categories" => $_SESSION["training_categories"],
		"orders" => $_SESSION["testing_data_order"]
	];

	echo json_encode($arr);
	$_SESSION["got_data"] = 1;
}
else
	logging("Something not set in get.php");
?>
