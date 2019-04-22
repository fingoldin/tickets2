<?php

session_start();

if(isset($_SESSION["risk_final"])) {
    echo "0";
}
else if(isset($_SESSION["risk_choices"]) && isset($_SESSION["risk_max_points"])) {
    $v = min($_SESSION["max_risk_bonus"], intval($_SESSION["risk_choices"][array_rand($_SESSION["risk_choices"])]));
    $_SESSION["risk_final"] = $v;
    echo $v;
} else {
    echo "0";
}

?>
