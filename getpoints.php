<?php

require("./includes.php");

if(!session_id())
	session_start();

if(isset($_SESSION["points"])) {
    $p = array_sum($_SESSION["points"]);
    logging("getpoints.php OK, with " . $p);

    if(isset($_SESSION["risk_final"])) {
        $p += $_SESSION["risk_final"];
    }

    echo $p;
} else {
    logging("Something not set in getpoints.php");
    echo "0";
}

?>
