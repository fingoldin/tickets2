<?php

session_start();

if(isset($_SESSION["risk_choice"]))
    echo "0";
else if(isset($_POST["choice"])) {
    $_SESSION["risk_choice"] = $_POST["choice"];
    if($_POST["choice"] == "wheel") {
        $max = mt_getrandmax();
        $r = mt_rand() / $max;

        $_SESSION["risk_payoff"] = $r > 0.5 ? 220 : 140;
    
        echo $r;
    }
    else if($_POST["choice"] == "fixed") {
        $_SESSION["risk_payoff"] = 180;
    }
}

?>
