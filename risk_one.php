<?php

require("./includes.php");

function spinner_weight($v) {
    return $v["fraction"];
}

if(!session_id())
    session_start();

if(!isset($_SESSION["risk_one_options"]) || !isset($_SESSION["risk_one_choices"]) || !isset($_POST["choice"]) || !isset($_POST["index"])) {
    logging("Something not set in risk_one.php");
    echo "0";
} else {
    if($_POST["choice"] == "wheel") {
        $weights = $_SESSION["risk_one_options"][$_POST["index"]]["spinner"];

        $idx = random_weighted(array_map("spinner_weight", $weights));

        $_SESSION["risk_one_choices"][$_POST["index"]] = [ "wheel" => $weights[$idx]["value"] ];

        echo $idx;
    } else {
        $v = $_SESSION["risk_one_options"][$_POST["index"]]["fixed"];
        $_SESSION["risk_one_choices"][$_POST["index"]] = [ "fixed" => $v ];
    }
    logging("risk_one.php called successfully with " . $_POST["choice"] . " and " . $_POST["index"]);
}
