<?php

if(!session_id())
	session_start();

if(isset($_SESSION["points"])) {
    $p = array_sum($_SESSION["points"]);

    echo $p;
} else {
    echo "0";
}

?>
