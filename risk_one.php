<?php

require("./includes.php");

function spinner_weight($v) {
    return $v->fraction;
}


if(!session_id())
    session_start();

if(!isset($_POST["seq_idx"]) || !isset($_POST["index"]) || !isset($_SESSION["max_points_risk_one"]) || !isset($_POST["choice_idx"]) ||
   !isset($_SESSION["points_additional"]) || !isset($_SESSION["checked_assoc"]) || !isset($_POST["choice"]) || !isset($_SESSION["risk_one_choices"])) {
    logging("Something not set in risk_one.php");
    echo "Not set";
} else {
    // Fixed value
    $val = $_SESSION["testing_data"][0][0][intval($_POST["seq_idx"])][intval($_POST["choice_idx"])];

    if($_POST["choice"] == "wheel") {
      $spinners = json_decode(file_get_contents("spinners3.json"));
      $weights = $spinners[8 - intval($_POST["choice_idx"])];
      
      $idx = random_weighted(array_map("spinner_weight", $weights));
      $val = $weights[$idx]->value;

      echo $idx;
    }
   
    $_SESSION["risk_one_choices"][$_POST["index"]] = [ "seq_idx" => $_POST["seq_idx"], "choice_idx" => $_POST["choice_idx"], "val" => $val ];
    
    logging("risk_one.php called successfully with " . $_POST["index"]);
}

?>
