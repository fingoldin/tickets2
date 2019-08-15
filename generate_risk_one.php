<?php

require("./includes.php");

$mean = 180;
$stddev = 20;
$min = 145;
$max = 215;
$data = [ "fixed" => 170, "spinner" => [] ];

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
    array_push($data["spinner"], [ "fraction" => 0.0, "value" => $v, "show" => $show ]);
}

$total = array_sum($slices);

for($i = 0; $i < count($slices); $i++) {
    $frac = $slices[$i] / $total;
    $data["spinner"][$i]["fraction"] = $frac;
}

echo json_encode($data);

?>
