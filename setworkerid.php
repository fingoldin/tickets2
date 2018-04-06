<?php

require("./includes.php");

if(!session_id())
	session_start();

if(isset($_POST["id"]))
{
	$_SESSION["workerId"] = $_POST["id"];
	logging("setworkerid.php called with " . $_POST["id"]);
}
else
	logging("id not set in setworkerid.php");

?>
