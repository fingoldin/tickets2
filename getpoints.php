<?php

if(!session_id())
	session_start();

if(isset($_SESSION["points"])) {
    $p = array_sum($_SESSION["points"]);

    if(isset($_SESSION["risk_final"])) {
        $p += $_SESSION["risk_final"];
    }

    echo $p;
} else {
    echo "0";
}

?>
