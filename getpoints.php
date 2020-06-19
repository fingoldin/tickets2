<?php

require("./includes.php");

if(!session_id())
	session_start();


$p = [ $_SESSION["main_points"], $_SESSION["risk_one_points"], $_SESSION["risk_points"] ];

logging("getpoints.php OK, with " . json_encode($p));

echo json_encode($p);

?>
