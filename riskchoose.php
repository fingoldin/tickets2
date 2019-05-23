<?php

session_start();

if(isset($_SESSION["risk_final"])) {
    echo "0";
}
else if(isset($_SESSION["risk_choices"]) && isset($_SESSION["max_risk_bonus"])) {
    $idx = mt_rand(1, count($_SESSION["risk_choices"]) - 1);
    $v = min($_SESSION["max_risk_bonus"], intval($_SESSION["risk_choices"][$idx]));
    $_SESSION["risk_final"] = $v;
    echo $v . "\n" . $idx;
} else {
    echo "0";
}

?>
