<?php

require("./includes.php");

$mean = 160;
$stddev = 20;
$min = 120;
$max = 200;
$data = [];

$slices = [];
$prev_cdf = normal_cdf($min - 1, $mean, $stddev);
$firstshow = true;
foreach(range($min, $max) as $v) {
    $cdf = normal_cdf($v, $mean, $stddev);
    $slice = $cdf - $prev_cdf;
    $prev_cdf = $cdf;
    
    array_push($slices, $slice);
    $show = ($v - $min) % 10 == 0;
    if($show && $firstshow) {
        $show = false;
        $firstshow = false;
    }
    array_push($data, [ "fraction" => 0.0, "value" => $v, "show" => $show ]);
}

$total = array_sum($slices);

for($i = 0; $i < count($slices); $i++) {
    $frac = $slices[$i] / $total;
    $data[$i]["fraction"] = $frac;
}

echo json_encode($data);

?>
