<?php

$niters = 1000000;


session_start();

require("./includes.php");

startSession();

function spinner_weight($v) {
    return $v["fraction"];
}

$results = array();
$weights = $_SESSION["risk_one_options"][0]["spinner"];
$min = $weights[0]["value"];
$weights = array_map("spinner_weight", $weights);

for($i = 0; $i < $niters; $i++) {
    array_push($results, $min + random_weighted($weights));
}

$values = array_count_values($results);
ksort($values);
$values = array_values($values);

echo json_encode($values) . "\n";

?>
