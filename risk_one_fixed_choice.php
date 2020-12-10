<?php

require("./includes.php");

if(!session_id())
    session_start();

if(!isset($_SESSION["max_points_risk_one_fixed"]) || !isset($_SESSION["points_additional"]) || !isset($_SESSION["risk_one_fixed_choices"])) { 
    logging("Something not set in risk_one_fixed_choice.php");
} else {
    if(isset($_SESSION["risk_one_fixed_paid"])) {
        logging("risk_one_fixed_choice.php tried to pay twice");
    } else {
        $num = array_rand($_SESSION["risk_one_fixed_choices"]);
        if(count($_SESSION["risk_one_fixed_choices"]) < 1 || $num < 0) {
            echo "none";
            logging("risk_one_fixed_choices has bad length " . $num);
        } else {
            $val = $_SESSION["risk_one_fixed_choices"][$num]["val"];

            $min = 120;
            $max = 200;
            //$p = $_SESSION["max_points_risk_one"] * ($val - $min) / ($max - $min);
            $p = intval($val * 10);
            if($p > $_SESSION["max_points_risk_one_fixed"]) {
                $p = $_SESSION["max_points_risk_one_fixed"];
            }
            else if($p < 0) {
                $p = 0;
            }
            $_SESSION["risk_one_fixed_points"] = $p;

            $_SESSION["risk_one_fixed_paid"] = 1;
            
            echo $val . "\n" . ($num + 1) . "\n" . $p;
            var_dump($_SESSION["risk_one_fixed_choices"]);

            logging("risk_one_fixed_choice.php called successfully with " . $val . ", earned " . $p . " points");
        }
    }
}

?> 
