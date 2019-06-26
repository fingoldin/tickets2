<?php

require("./includes.php");

if(!session_id())
    session_start();

if(isset($_SESSION["risk_final"])) {
    logging("risk_final already set in riskchoose.php");
    echo "0";
}
else if(isset($_SESSION["risk_choices"]) && isset($_SESSION["max_risk_bonus"]) && isset($_SESSION["risk_options"])) {
    $idx = mt_rand(1, count($_SESSION["risk_choices"]) - 1);
    $v = intval($_SESSION["risk_choices"][$idx]);

    $tmp = [];
    for($i = 1; $i < count($_SESSION["risk_options"]); $i++) {
        for($j = 0; $j < 3; $j++) {
            array_push($tmp, $_SESSION["risk_options"][$i][$j]);
        }
    }
    $maximum = max($tmp);
    $minimum = min($tmp);

    $_SESSION["risk_final"] = min($_SESSION["max_risk_bonus"], round($_SESSION["max_risk_bonus"] * ($v - $minimum) / ($maximum - $minimum)));
    
    echo $v . "\n" . $idx . "\n" . $_SESSION["risk_final"];

    logging("riskchoose.php OK, with (" . $v . ", " . $idx . ", " . $_SESSION["risk_final"] . ")");
} else {
    logging("Something not set in riskchoose.php");
    echo "0";
}

?>
