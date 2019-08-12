<?php

require("./includes.php");

if(!session_id())
    session_start();

echo json_encode([ "result" => 150, "fraction" => 0.8 ]);
