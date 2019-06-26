<?php

require("./includes.php");

if(!session_id())
	session_start();

if(isset($_SESSION["points"])) {
    $p = array_sum($_SESSION["points"]);

    if(isset($_SESSION["risk_final"])) {
        $p += $_SESSION["risk_final"];
    }
    
    logging("getpoints.php OK, with " . $p);

    echo $p;
} else {
    logging("Something not set in getpoints.php");
    echo "0";
}

?>
