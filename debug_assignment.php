<?php

require("includes.php");

$m = new Aws\MTurk\MTurkClient([
	"credentials" => get_mturk_credentials(),
	"version" => "2017-01-17",
	"endpoint" => "https://mturk-requester.us-east-1.amazonaws.com",
	"region" => "us-east-1"
]);

$result = $m->getAssignment([
    'AssignmentId' => '32EYX73OY12XOY6NUKAQ7S5PPEUURO'
]);

var_dump($result);

?>
