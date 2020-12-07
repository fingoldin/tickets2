<?php

require("./includes.php");

function spinner_weight($v) {
    return $v->fraction;
}


if(!session_id())
    session_start();

if(!isset($_POST["seq_idx"]) || !isset($_POST["index"]) || !isset($_SESSION["max_points_risk_one"]) || !isset($_SESSION["max_points_risk_one_fixed"]) || !isset($_POST["seq_choice_idx"]) ||
   !isset($_SESSION["points_additional"]) || !isset($_SESSION["checked_assoc"]) || !isset($_POST["choice"]) || !isset($_SESSION["risk_one_choices"]) || !isset($_SESSION["risk_one_fixed_choices"])) {
    logging("Something not set in risk_one.php");
    echo "Not set";
} else {
    // Fixed value
    $val = $_SESSION["testing_data"][0][0][intval($_POST["seq_idx"])][intval($_POST["seq_choice_idx"])];
    $store_array = "risk_one_choices";

    if($_POST["fixed_order"] == "true") {
      $fixed_outcomes = [];
      $fixed_fp = fopen("fixed_outcome.csv", "r");
      while(($row = fgetcsv($fixed_fp, 1000, ",")) !== FALSE) {
        array_push($fixed_outcomes, $row);
      }
      fclose($fixed_fp);
      $i_vals = [2, 4, 6, 8];
      $val = $fixed_outcomes[array_search(intval($_POST["seq_choice_idx"]), $i_vals) + 1][intval($_POST["seq_idx"]) + 2];
      $store_array = "risk_one_fixed_choices";
    }

    if($_POST["choice"] == "wheel") {
      $spinners = json_decode(file_get_contents("spinners5.json"));
      $weights = $spinners[intval(intval($_POST["seq_choice_idx"]) / 2) - 1];
      
      $idx = random_weighted(array_map("spinner_weight", $weights));
      $val = $weights[$idx]->value;

      echo $idx;
    }
   
    $_SESSION[$store_array][$_POST["index"]] = [ "seq_idx" => $_POST["seq_idx"], "seq_choice_idx" => $_POST["seq_choice_idx"], "val" => $val ];
    
    logging("risk_one.php called successfully with " . $_POST["index"]);
}

?>
