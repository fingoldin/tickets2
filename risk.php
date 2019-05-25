<?php

if(!session_id())
    session_start();

if(isset($_POST["choice"]) && isset($_SESSION["risk_choices"]) && isset($_POST["index"])) {
    $idx = intval($_POST["index"]);
    if($idx >= 0 && $idx < count($_SESSION["risk_options"])) {
        if($_POST["choice"] == "wheel") {
            $n = mt_rand();
            if($n % 2 == 0) {
                $val = $_SESSION["risk_options"][$idx][1];
                echo "0";
            } else {
                $val = $_SESSION["risk_options"][$idx][2];
                echo "1";
            }
        }
        else if($_POST["choice"] == "fixed") {
            $val = $_SESSION["risk_options"][$idx][0];
        }

        $_SESSION["risk_choices"][$idx] = $val;
    }
}
else {
    logging("Something not set in risk.php");
}
