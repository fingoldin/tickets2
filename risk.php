<?php

require("./includes.php");

function spinner_weight($v) {
    return $v["fraction"];
}


if(!session_id())
    session_start();

if(!isset($_SESSION["risk_options"]) || !isset($_SESSION["risk_choices"]) ||
   !isset($_POST["choice"]) || !isset($_POST["index"]) || !isset($_SESSION["max_points_risk"]) ||
   !isset($_SESSION["points_additional"])) {
    logging("Something not set in risk.php");
    echo "0";
} else if((int)$_POST["index"] < count($_SESSION["risk_options"]) && !isset($_SESSION["risk_choices"][$_POST["index"]])) {
    $fixed = (int)$_SESSION["risk_options"][$_POST["index"]]["fixed"];
    $val = $fixed;
    
    $weights = $_SESSION["risk_options"][$_POST["index"]]["spinner"];
    
    if($_POST["choice"] == "wheel") {
        $idx = random_weighted(array_map("spinner_weight", $weights));
        $val = (int)$weights[$idx]["value"];

        echo $idx . ":";
    }
    
    $_SESSION["risk_choices"][$_POST["index"]] = [ $_POST["choice"] => $val ];
    
    $min = $fixed;
    $max = $fixed;

    for($i = 0; $i < count($weights); $i++) {
        $v = (int)$weights[$i]["value"];
        if($v > $max) {
            $max = $v;
        }
        if($v < $min) {
            $min = $v;
        }
    }

    $p = (int)($_SESSION["max_points_risk"] * ($val - $min) / ($max - $min));
    if($p > $_SESSION["max_points_risk"]) {
        $p = $_SESSION["max_points_risk"];
    }
    if($p < 0) {
        $p = 0;
    }
    $_SESSION["points_additional"] += $p;

    echo $p;

    logging("risk.php called successfully with " . $_POST["choice"] . " and " . $_POST["index"] . ", earned " . $p . " points");
} else {
    logging("risk.php received wonky index: " . $_POST["index"]);
}

?>
