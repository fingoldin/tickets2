<?php

require("./includes.php");


if(!session_id())
    session_start();

if(isset($_SESSION["checked_assoc"]) && isset($_POST["example"])) {
    $spinners = json_decode(file_get_contents("spinners5.json"));
    $fixed_outcomes = [];
    $fixed_fp = fopen("fixed_outcome.csv", "r");
    while(($row = fgetcsv($fixed_fp, 1000, ",")) !== FALSE) {
      array_push($fixed_outcomes, $row);
    }
    fclose($fixed_fp);
    $min_ticks = [160, 160, 150, 120];
    if($_POST["example"] == "true") {
      echo json_encode([ 
        [ "fixed" => 160, "seq_idx" => 0, "spinner" => $spinners[3], "seq_choice_idx" => 8, "min_tick" => $min_ticks[3] ],
        [ "fixed" => 170, "seq_idx" => 1, "spinner" => $spinners[2], "seq_choice_idx" => 6, "min_tick" => $min_ticks[2] ],
/*        [ "fixed" => 180, "seq_idx" => 2, "spinner" => $spinners[1], "seq_choice_idx" => 4, "min_tick" => 160 ],
        [ "fixed" => 170, "seq_idx" => 3, "spinner" => $spinners[0], "seq_choice_idx" => 2, "min_tick" => 170 ]*/
      ]);
    } else {
      $i_vals = [2, 4, 6, 8];
      $data = [];
      $ntrials = count($_SESSION["testing_data"][0][0]);
      if($_POST["fixed_order"] == "true") {
        $ntrials = count($fixed_outcomes[0]) - 2;
      }
      for($seq_idx = 0; $seq_idx < $ntrials; $seq_idx++) {
        $sequence = $_SESSION["testing_data"][0][0][$seq_idx];
        for($j = 0; $j < count($i_vals); $j++) {
          if(!isset($data[$j])) {
            $data[$j] = [];
          }
          $i = $i_vals[$j];
          $min_tick = $min_ticks[$j];
          $fixed = $sequence[$i];
          if($_POST["fixed_order"] == "true") {
            $fixed = $fixed_outcomes[$j + 1][$seq_idx + 2];
          }
          array_push($data[$j], [
            "fixed" => $fixed,
            "seq_idx" => $seq_idx,
            "seq_choice_idx" => $i,
            "min_tick" => $min_tick, 
            "spinner" => $spinners[$j]
          ]);
        }
      }
      if($_POST["fixed_order"] != "true") {
        for($j = 0; $j < count($i_vals); $j++) {
          shuffle($data[$j]);
        }
        shuffle($data);
      }
      echo json_encode(array_merge(...$data));
    }
    logging("get_risk_one.php called successfully");
} else {
    logging("Something not set in get_risk_one.php");
}

?>
