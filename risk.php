<?php

session_start();

if(isset($_POST["choice"]) && isset($_SESSION["risk_choices"])) {
    if($_POST["choice"] == "wheel") {
        $r = mt_rand();
        if($r % 2 == 0) {
            echo "220";
            array_push($_SESSION["risk_choices"], 220);
        } else {
            echo "140";
            array_push($_SESSION["risk_choices"], 140);
        }
    }
    else if($_POST["choice"] == "fixed") {
        array_push($_SESSION["risk_choices"], 180);
        echo "180";
    }
}

?>
