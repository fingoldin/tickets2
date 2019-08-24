<?php

require("./includes.php");

if(!session_id())
    session_start();

if(!isset($_SESSION["risk_one_choices"]) || !isset($_SESSION["max_points_risk_one"]) ||
   !isset($_SESSION["points_additional"]) || !isset($_SESSION["num_risk_one"]) || 
   !isset($_SESSION["risk_one_options"])) {
    logging("Something not set in risk_one_choice.php");
} else {
    if(isset($_SESSION["risk_one_paid"])) {
        logging("risk_one_choice.php tried to pay twice with " . $_POST["choice"] . " and " . $_POST["index"]);
    } else {
        $num = count($_SESSION["risk_one_choices"]);
        if($num <= 0 || $num >= $_SESSION["num_risk_one"]) {
            logging("risk_one_choices has bad length " . $num);
        } else {
            $val = $_SESSION["risk_one_choices"][$num - 1]["value"];
            $min = $val;
            $max = $val;

            for($i = 0; $i < count($_SESSION["risk_one_options"]); $i++) {
                $v = (int)$_SESSION["risk_one_options"][$i]["fixed"];
                if($v > $max) {
                    $max = $v;
                }
                if($v < $min) {
                    $min = $v;
                }
                for($j = 0; $j < count($_SESSION["risk_one_options"][$i]["spinner"]); $j++) {
                    $v = (int)$_SESSION["risk_one_options"][$i]["spinner"][$j]["value"];
                    if($v > $max) {
                        $max = $v;
                    }
                    if($v < $min) {
                        $min = $v;
                    }
                }
            }

            $p = (int)($_SESSION["max_points_risk_one"] * ($val - $min) / ($max - $min));
            if($p > $_SESSION["max_points_risk_one"]) {
                $p = $_SESSION["max_points_risk_one"];
            }
            if($p < 0) {
                $p = 0;
            }
            $_SESSION["points_additional"] += $p;

            $_SESSION["risk_one_paid"] = 1;

            echo $p;

            logging("risk_one_choice.php called successfully with " . $_POST["choice"] . " and " . $_POST["index"] . ", earned " . $p . " points");
        }
    }
}

?> 
