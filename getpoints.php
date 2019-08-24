<?php

require("./includes.php");

if(!session_id())
	session_start();

if(isset($_SESSION["points"]) && isset($_SESSION["points_additional"])) {
    $p = array_sum($_SESSION["points"]) + $_SESSION["points_additional"];

    logging("getpoints.php OK, with " . $p);

    echo $p;
} else {
    logging("Something not set in getpoints.php");
    echo "0";
}

?>
