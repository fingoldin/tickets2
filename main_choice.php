<?php

require("./includes.php");

if(!session_id())
    session_start();

if(!isset($_SESSION["max_points_main"]) || !isset($_SESSION["points_additional"]) || !isset($_SESSION["checked_assoc"]) || !isset($_SESSION["testing_data"])) { 
    logging("Something not set in main_choice.php");
} else {
    if(isset($_SESSION["main_paid"])) {
        logging("main_choice.php tried to pay twice");
    } else {
        $num = array_rand($_SESSION["checked_assoc"][0][0]);
        if(count($_SESSION["checked_assoc"][0][0]) < 1 || $num < 0) {
            echo "none";
            logging("main_choices has bad length " . $num);
        } else {
            $val = $_SESSION["testing_data"][0][0][$num][(int)$_SESSION["checked_assoc"][0][0][$num]["idx"]];

            $min = 125;
            $max = 195;
            $p = $_SESSION["max_points_main"] * ($val - $min) / ($max - $min);
            if($p > $_SESSION["max_points_main"]) {
                $p = $_SESSION["max_points_main"];
            }
            else if($p < 0) {
                $p = 0;
            }
            $_SESSION["main_points"] = $p;

            $_SESSION["main_paid"] = 1;
            
            echo $val . "\n" . ($num + 1) . "\n" . $p;
            logging("main_choice.php called successfully with " . $val . ", earned " . $p . " points");
        }
    }
}

?> 
