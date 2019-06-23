<?php

require("./includes.php");

if(!session_id())
	session_start();

if(isset($_POST["f7g12d"]) && isset($_SESSION["testing_data"]) && isset($_SESSION["testing_metadata"]) && isset($_SESSION["got_data"]) && $_SESSION["got_data"] == 0)
{
	logging("Get.php OK");

	$arr = [
		"testing" => $_SESSION["testing_data"],
		"testing_meta" => $_SESSION["testing_metadata"]
    ];

	echo json_encode($arr);
	$_SESSION["got_data"] = 1;
}
else
	logging("Something not set in get.php");
?>
