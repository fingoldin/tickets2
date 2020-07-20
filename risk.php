<?php

require("./includes.php");

function spinner_weight($v) {
    return $v["fraction"];
}


if(!session_id())
    session_start();

if(!isset($_SESSION["risk_data"]) ||
   !isset($_POST["ticket"]) || !isset($_POST["index"]) ||
   !isset($_SESSION["points_additional"])) {
    logging("Something not set in risk.php");
    echo "0";
} else if((int)$_POST["index"] < count($_SESSION["risk_data"])) {
//    $fixed = (int)$_SESSION["testing_data"][0][0][(int)$_POST["index"]][0];
//    $val = $fixed;
    
//    if($_POST["ticket"] != "fixed") {
    $val = $_SESSION["risk_data"][(int)$_POST["index"]]["data"][(int)$_POST["ticket"]];
//    }
    
    $_SESSION["risk_choices"][$_POST["index"]] = $val;
    
//    $p = get_risk_points(0, 0, (int)$_POST["index"], $val);
//    $_SESSION["points_additional"] += $p;

//    echo $p;

    logging("risk.php called successfully with " . $_POST["ticket"] . " and " . $_POST["index"]);
} else {
    logging("risk.php received wonky index: " . $_POST["index"]);
}

?>
