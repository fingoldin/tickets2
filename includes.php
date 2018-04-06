<?php

require("./repos/mturk-php/mturk.php");

function logging($mes)
{
	$f = fopen("./logging.txt", "w");
	fwrite($f, "[" . date("Y-m-d H:i:s") . "] " . $mes . "\n");
	fclose($f);
}

function store_url()
{
	$log = fopen("./urls/" . get_time() . ".json", "w");
	fwrite($log, json_encode($_SERVER) . "\n\n" . json_encode($_GET) . "\n\n" . json_encode($_POST));
	fclose($log);
}

function get_time()
{
	date_default_timezone_set("America/New_York");
        return date("Y-m-d H:i:s");
}

function httpPost($url, $data)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

function get_points($phase, $sequence, $answer)
{
	if(!session_id())
		session_start();

	if(!isset($_SESSION["testing_data"]))
		return 0;

	$arr = $_SESSION["testing_data"][$phase][$sequence];
        sort($arr);

    $p = intval(25 * round(($arr[count($arr) - 1] - $answer) / ($arr[count($arr - 1] - $arr[0])));

	return $p;
}


// returns an integer for the number of cents
function get_bonus($points)
{
	$b = round($points * 0.1);

	if($b > 1000)
		return 1000;
	else if($b < 0)
		return 0;
	else
		return $b;
}

function grant_bonuses()
{
	$c = dbConnect();

	$r = dbQuery($c, "SELECT * FROM responses WHERE bonus_paid=FALSE AND end_time < (NOW() - INTERVAL 10 MINUTE)");

	if(!empty($r))
	{
		foreach($r as $row)
		{
			grant_bonus($row["bonus"], $row["worker_id"], $row["assignment_id"]);

			dbQuery($c, "UPDATE responses SET bonus_paid=TRUE WHERE RID=:rid", ["rid" => $row["RID"]]);
		}
	}
}

function grant_bonus($b, $worker_id, $assignment_id)
{
	//$b = get_bonus(intval($arr["points_phase0"]) + intval($arr["points_phase1"])) / 100;

	// b is inputted as an int of cents
	$bonus = $b / 100;

	if($bonus > 10)
		$bonus = 10;
	else if($bonus < 0)
		$bonus = 0;

	//echo "bonus: " . $bonus;

	$m = new MechanicalTurk();
	$r = $m->request('GrantBonus', array(
		"WorkerId" => $worker_id,
		"AssignmentId" => $assignment_id,
		"BonusAmount" => array(array("Amount" => $bonus, "CurrencyCode" => "USD")),
		"Reason" => "Thanks!"
	));

	$f = fopen("./bonus/" . $worker_id . ".json", "w");
	fwrite($f, json_encode([ "bonus" => $bonus, "worker_id" => $worker_id, "assignment_id" => $assignment_id, "result" => $r]));
	fclose($f);

	//var_dump($r);

	//httpPost("https://www.mturk.com/mturk/externalSubmit", [ "assignmentId" => $_SESSION["assignmentId"] ]);
}

function log_save_response($arr)
{
	$log = fopen("./log.txt", "a");
        fwrite($log, json_encode($arr) . "\n\n");
        fclose($log);
}

function subject_save_response($arr)
{
	$filename = "./data/" . $arr["worker_id"] . "_output.json";

        file_put_contents($filename, json_encode($arr));
}

function mysql_save_response($arr)
{
	$conn = dbConnect();

	$result = dbQuery($conn, "INSERT INTO responses SET bonus_paid=FALSE, start_time=:start_time, end_time=:end_time, age=:age, gender=:gender, tries=:tries, during=:during, points_phase0=:points_phase0, points_phase1=:points_phase1, worker_id=:worker_id, assignment_id=:assignment_id, bonus=:bonus", [
			"start_time" => $arr["start_time"],
			"end_time" => $arr["end_time"],
                	"age" => $arr["age"],
                	"gender" => $arr["gender"],
                	"tries" => $arr["tries"],
                	"during" => $arr["during"],
			"points_phase0" => $arr["points_phase0"],
			"points_phase1" => $arr["points_phase1"],
			"worker_id" => $arr["worker_id"],
			"assignment_id" => $arr["assignment_id"],
			"bonus" => $arr["bonus"]
	]);

	$id = $conn->lastInsertId();

	foreach($arr["data"] as $trial)
	{
		if($trial["trial_type"] == "bar-choose")
		{
			foreach($trial["responses"] as $bar_response)
			{
				dbQuery($conn, "INSERT INTO bar_responses SET response=:value, offby=:offby, category=:category, category_index=:category_index, RID=$id, phase=" . $trial["phase"] . ", number=" . $trial["number"], $bar_response);
			}
		}
		else if($trial["trial_type"] == "ticket-choose" && $trial["sequence"] > -1)
		{
//			var_dump($trial);
			dbQuery($conn, "INSERT INTO test_responses SET response=:result, points=:points, phase=:phase, sequence=:sequence, place=:place, RID=$id, next_num=:next_num", [
					"result" => $trial["result"],
					"points" => $trial["points"],
					"phase" => $trial["phase"],
					"sequence" => $trial["sequence"],
					"place" => $trial["place"],
					"next_num" => $trial["next_num"]
			]);

			for($i = 0; $i < count($trial["times"]); $i++)
			{
				dbQuery($conn, "INSERT INTO testing_times SET sequence=:sequence, RID=$id, place=:place, phase=:phase, time=:time", [
					"sequence" => $trial["sequence"],
					"place" => $i,
					"phase" => $trial["phase"],
					"time" => $trial["times"][$i]
				]);
			}
		}
		else if($trial["trial_type"] == "training_avg")
		{
			dbQuery($conn, "INSERT INTO training_responses SET sequence=:sequence, RID=$id, avg=:avg, response=:response, phase=:phase", [
					"sequence" => $trial["sequence"],
					"avg" => $trial["avg"],
					"response" => $trial["response"],
					"phase" => $trial["phase"]
			]);
		}
	}
}

function dbConnect() {
    $dsn = "mysql:host=localhost;dbname=tickets2_responses;charset=utf8";
    $opts = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ERRMODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $conn = new PDO($dsn, "tickets_user", "hats6789", $opts);
    return $conn;
}
function dbQuery($conn, $query, $values = array()) {
    if (isset($values)) {
        $stmt = $conn->prepare($query);
        $stmt->execute($values);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        $stmt = $conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

function generate_deviate($mean, $stddev)
{
    return $mean;
}

function startSession() {
    $_SESSION = array();

    $_SESSION["points"] = [];
    $_SESSION["points"][0] = 0;
    $_SESSION["points"][1] = 0;
    $_SESSION["checked"] = [];
    $_SESSION["checked"][0] = $_SESSION["checked"][1] = [];
    $_SESSION["got_data"] = 0;
    $_SESSION["finished"] = 0;
    $_SESSION["checked_assoc"] = [];
    $_SESSION["checked_assoc"][0] = $_SESSION["checked_assoc"][1] = [];

    $_SESSION["start_time"] = get_time();

    // The number of phases
    $nphases = 2;

    // The number of sequences in one training phase
    $ntraining_sequences = 5;

    // The number of tickets in one sequence in the training phase
    $ntraining_tickets = 10;

    // Parameters of normal distribution
    $mean = 180;
    $stddev = 20;

    // Minimum and maximum values for the deviates in case we get a really unlikely one
    $min = 120;
    $max = 240;

    // Generate training data
    $_SESSION["training_data"] = array();
    for($h = 0; $h < $nphases; $h++) {
        $_SESSION["training_data"][$h] = array();
        for($i = 0; $i < $ntraining_sequences; $i++) {
            $_SESSION["trainin_data"][$h][$i] = array();
            for($j = 0; $j < $ntraining_tickets; $j++) {
                $v = (int)round(generate_deviate($mean, $stddev));
        
                if($v > $max)
                    $v = $max)
                else if($v < $min)
                    $v = $min;

                $_SESSION["training_data"][$h][$i][$j] = $v;
            }
        }
    }

    $_SESSION["training_answers"] = [
    // First training phase
    [2, 8, 23, 34, 23, 8, 2],
    // Second training phase
    [2, 8, 23, 34, 23, 8, 2]
    ];

    $_SESSION["training_categories"] = [
    ["$120 - $137","$138 - $154","$155 - $171", "$172 - $188", "$189 - $205", "$206 - $222", "$223 - $240"],
    ["$120 - $137","$138 - $154","$155 - $171", "$172 - $188", "$189 - $205", "$206 - $222", "$223 - $240"]
    ];

    // Number of sequences in each test phase
    $ntest_sequences = 200;

    // Number of tickets in one test sequence
    $ntest_tickets = 10;

    // Generate test data
    $_SESSION["testing_data"] = array();
    for($h = 0; $h < $nphases; $h++) {
        $_SESSION["testing_data"][$h] = array();
        for($i = 0; $i < $ntest_sequences; $i++) {
            $_SESSION["testing_data"][$h][$i] = array();
            for($j = 0; $j < $ntest_tickets; $j++) {
                $v = (int)round(generate_deviate($mean, $stddev));
        
                if($v > $max)
                    $v = $max)
                else if($v < $min)
                    $v = $min;

                $_SESSION["testing_data"][$h][$i][$j] = $v;
            }
        }
    }
}

?>
