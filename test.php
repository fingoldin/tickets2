<?php

$fixed_outcomes = [];
$fixed_fp = fopen("fixed_outcome.csv", "r");
while(($row = fgetcsv($fixed_fp, 1000, ",")) !== FALSE) {
  array_push($fixed_outcomes, $row);
}
fclose($fixed_fp);
var_dump($fixed_outcomes);

?>
