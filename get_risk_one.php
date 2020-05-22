<?php

require("./includes.php");


if(!session_id())
    session_start();

if(isset($_SESSION["checked_assoc"]) && isset($_POST["example"])) {
    $spinners = json_decode(file_get_contents("spinners3.json"));
    if($_POST["example"] == "true") {
      echo json_encode([ 
        [ "fixed" => 180, "seq_idx" => 0, "spinner" => $spinners[2] ],
        [ "fixed" => 190, "seq_idx" => 1, "spinner" => $spinners[5] ]
      ]);
    } else {
      $data = [];
      foreach($_SESSION["checked_assoc"][0][0] as $seq_idx => $sequence) {
        if($sequence["idx"] < 9) {
          array_push($data, [
            "fixed" => $_SESSION["testing_data"][0][0][intval($seq_idx)][intval($sequence["idx"])],
            "seq_idx" => $seq_idx,
            "spinner" => $spinners[8 - intval($sequence["idx"])]
          ]);
        }
      }
      echo json_encode($data);
    }
    logging("get_risk_one.php called successfully");
} else {
    logging("Something not set in get_risk_one.php");
}

?>
