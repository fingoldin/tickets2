<?php

require("./includes.php");

if(!session_id())
    session_start();

if(!isset($_SESSION["max_points_risk_one"]) || !isset($_SESSION["points_additional"]) || !isset($_SESSION["risk_one_choices"])) { 
    logging("Something not set in risk_one_choice.php");
} else {
    if(isset($_SESSION["risk_one_paid"])) {
        logging("risk_one_choice.php tried to pay twice");
    } else {
        $num = array_rand($_SESSION["risk_one_choices"]);
        if(count($_SESSION["risk_one_choices"]) < 1 || $num < 0) {
            echo "none";
            logging("risk_one_choices has bad length " . $num);
        } else {
            $val = $_SESSION["risk_one_choices"][$num]["val"];

            $min = 120;
            $max = 200;
            //$p = $_SESSION["max_points_risk_one"] * ($val - $min) / ($max - $min);
            $p = intval($val * 10);
            if($p > $_SESSION["max_points_risk_one"]) {
                $p = $_SESSION["max_points_risk_one"];
            }
            else if($p < 0) {
                $p = 0;
            }
            $_SESSION["risk_one_points"] = $p;

            $_SESSION["risk_one_paid"] = 1;
            
            echo $val . "\n" . ($num + 1) . "\n" . $p;

            logging("risk_one_choice.php called successfully with " . $val . ", earned " . $p . " points");
        }
    }
}

?> 
