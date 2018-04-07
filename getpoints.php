<?php

if(!session_id())
	session_start();

if(isset($_SESSION["points"]))
    echo array_sum($_SESSION["points"]);

?>
