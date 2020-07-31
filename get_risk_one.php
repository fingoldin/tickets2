<?php

require("./includes.php");


if(!session_id())
    session_start();

if(isset($_SESSION["checked_assoc"]) && isset($_POST["example"])) {
    $spinners = json_decode(file_get_contents("spinners3.json"));
    if($_POST["example"] == "true") {
      echo json_encode([ 
        [ "fixed" => 160, "seq_idx" => 0, "spinner" => $spinners[2], "seq_choice_idx" => 1 ],
        [ "fixed" => 170, "seq_idx" => 1, "spinner" => $spinners[5], "seq_choice_idx" => 2 ]
      ]);
    } else {
      $main_data = [];
      foreach($_SESSION["checked_assoc"][0][0] as $seq_idx => $sequence) {
        array_push($main_data, [
          "data" => array_slice($_SESSION["testing_data"][0][0][intval($seq_idx)], 0, intval($sequence["idx"]) + 1),
          "seq_idx" => $seq_idx
        ]);
      }
      shuffle($main_data);
      $added = 0;
      $data = [];
      foreach($main_data as $sequence) {
        if($added < 30) {
          for($i = 0; $i < count($sequence["data"]) && $i < 9; $i++) {
            array_push($data, [
              "fixed" => $sequence["data"][$i],
              "seq_idx" => $sequence["seq_idx"],
              "seq_choice_idx" => $i,
              "spinner" => $spinners[8 - $i]
            ]);
          }
          $added += 1;
        }
      }
      shuffle($data);
      echo json_encode($data);
    }
    logging("get_risk_one.php called successfully");
} else {
    logging("Something not set in get_risk_one.php");
}

?>
