<?php

require("./includes.php");

$weights = [0.1, 0.8, 0.06, 0.04];

$niters = 10000;

$results = array();

for($i = 0; $i < $niters; $i++) {
    array_push($results, random_weighted($weights));
}

$values = array_count_values($results);
ksort($values);
print_r($values);

?>
