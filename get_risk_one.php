<?php

require("./includes.php");


if(!session_id())
    session_start();

if(isset($_SESSION["checked_assoc"]) && isset($_POST["example"])) {
    $spinners = json_decode(file_get_contents("spinners4.json"));
    if($_POST["example"] == "true") {
      echo json_encode([ 
        [ "fixed" => 160, "seq_idx" => 0, "spinner" => $spinners[3], "seq_choice_idx" => 8, "min_tick" => 120 ],
        [ "fixed" => 170, "seq_idx" => 1, "spinner" => $spinners[2], "seq_choice_idx" => 6, "min_tick" => 150 ],
/*        [ "fixed" => 180, "seq_idx" => 2, "spinner" => $spinners[1], "seq_choice_idx" => 4, "min_tick" => 160 ],
        [ "fixed" => 170, "seq_idx" => 3, "spinner" => $spinners[0], "seq_choice_idx" => 2, "min_tick" => 170 ]*/
      ]);
    } else {
      $i_vals = [2, 4, 6, 8];
      $data = [];
      for($seq_idx = 0; $seq_idx < count($_SESSION["testing_data"][0][0]); $seq_idx++) {
        $sequence = $_SESSION["testing_data"][0][0][$seq_idx];
        for($j = 0; $j < count($i_vals); $j++) {
          if(!isset($data[$j])) {
            $data[$j] = [];
          }
          $i = $i_vals[$j];
          $min_tick = -$j * 10 + 170;
          if($j == count($i_vals) - 1) {
            $min_tick = 120;
          }
          array_push($data[$j], [
            "fixed" => $sequence[$i],
            "seq_idx" => $seq_idx,
            "seq_choice_idx" => $i,
            "min_tick" => $min_tick, 
            "spinner" => $spinners[$j]
          ]);
        }
      }
      for($j = 0; $j < count($i_vals); $j++) {
        shuffle($data[$j]);
      }
      shuffle($data);
      echo json_encode(array_merge(...$data));
    }
    logging("get_risk_one.php called successfully");
} else {
    logging("Something not set in get_risk_one.php");
}

?>
