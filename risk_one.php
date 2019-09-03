<?php

require("./includes.php");

function spinner_weight($v) {
    return $v["fraction"];
}


if(!session_id())
    session_start();

if(!isset($_SESSION["risk_one_options"]) || !isset($_SESSION["risk_one_choices"]) ||
   !isset($_POST["index"]) || !isset($_SESSION["max_points_risk_one"]) ||
   !isset($_SESSION["points_additional"]) || !isset($_SESSION["num_risk_one"])) {
    logging("Something not set in risk_one.php");
    echo "0";
} else {
    $weights = $_SESSION["risk_one_options"][$_POST["index"]];
    
    $idx = random_weighted(array_map("spinner_weight", $weights));
    $val = (int)$weights[$idx]["value"];

    echo $idx;
    
    $_SESSION["risk_one_choices"][$_POST["index"]] = $val;
    
    logging("risk_one.php called successfully with " . $_POST["index"]);
}

?>
