<?php

require("./includes.php");

if(!session_id())
    session_start();

if(!isset($_SESSION["max_points_risk"]) || !isset($_SESSION["points_additional"]) || !isset($_SESSION["risk_choices"])) { 
    logging("Something not set in risk_choice.php");
} else {
    if(isset($_SESSION["risk_paid"])) {
        logging("risk_choice.php tried to pay twice");
    } else {
        $num = array_rand($_SESSION["risk_choices"]);
        if(count($_SESSION["risk_choices"]) < 1 || $num < 0) {
            echo "none";
            logging("risk_choices has bad length " . $num);
        } else {
            $val = $_SESSION["risk_choices"][$num];

            $min = 120;
            $max = 200;
            $p = intval($_SESSION["max_points_risk"] * ($val - $min) / ($max - $min));
            if($p > $_SESSION["max_points_risk"]) {
                $p = $_SESSION["max_points_risk"];
            }
            else if($p < 0) {
                $p = 0;
            }
            $_SESSION["risk_points"] = $p;

            $_SESSION["risk_paid"] = 1;
            
            echo $val . "\n" . ($num + 1) . "\n" . $p;

            logging("risk_choice.php called successfully with " . $val . ", earned " . $p . " points");
        }
    }
}

?> 
