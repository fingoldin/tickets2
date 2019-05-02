<?php

session_start();

if(isset($_POST["choice"]) && isset($_SESSION["risk_choices"]) && isset($_POST["index"])) {
    $idx = intval($_POST["index"]);
    if($idx < 0 || $idx >= count($_SESSION["risk_options"])) {
        if($_POST["choice"] == "wheel") {
            $n = mt_rand();
            if($n % 2 == 0) {
                $val = $_SESSION["risk_options"][$idx][1];
            } else {
                $val = $_SESSION["risk_options"][$idx][2];
            }
        }
        else if($_POST["choice"] == "fixed") {
            $val = $_SESSION["risk_options"][$idx][0];
        }

        $_SESSION["risk_choices"][$idx] = $val;
        echo $val;
    }
}

?>
