<?php

require("../aws/aws-autoloader.php");

function f_logging($mes, $fname)
{
	$f = fopen($fname, "a");
	fwrite($f, "[" . get_time() . "] " . $mes . "\n");
	fclose($f);
}

function logging($mes)
{
    f_logging($mes, "./logging.txt");
}

function bonus_log($mes, $opt)
{
    f_logging($mes . $opt . "\n\n", "../bonus_log_long.txt");
    f_logging($mes, "../bonus_log.txt");
}

function store_url()
{
	$log = fopen("./urls/" . get_time() . ".json", "w");
	fwrite($log, json_encode($_SERVER) . "\n\n" . json_encode($_GET) . "\n\n" . json_encode($_POST));
	fclose($log);
}

function get_time()
{
    return date("Y-m-d H:i:s");
}

function get_points($phase, $group, $sequence, $answer)
{
	if(!session_id())
		session_start();

	if(!isset($_SESSION["testing_data"]) || !isset($_SESSION["max_points_per_seq"]))
		return 0;

	$arr = $_SESSION["testing_data"][$phase][$group][$sequence];
    sort($arr);

    $p = intval(round($_SESSION["max_points_per_seq"] * ($arr[count($arr) - 1] - $answer) / ($arr[count($arr) - 1] - $arr[0])));

	return $p;
}


// returns an integer for the number of cents
function get_bonus($p)
{
    if(!session_id())
        session_start();

	if($p > $_SESSION["max_points"])
		$p = $_SESSION["max_points"];
	else if($p < 0)
		$p = 0;

    return round($p * 0.1);
}

function get_mturk_credentials() {
    $data = json_decode(file_get_contents("../mturk_credentials.json"), true);
    
    return new Aws\Credentials\Credentials($data["key"], $data["secret"]);
}

function grant_bonuses()
{
	$c = dbConnect();

	$r = dbQuery($c, "SELECT * FROM responses WHERE bonus_paid=FALSE AND end_time < TIMESTAMPADD(MINUTE, -10, NOW()) AND CHAR_LENGTH(assignment_id) > 10");

	if(!empty($r))
	{
        $m = false;
        try {
            $m = new Aws\MTurk\MTurkClient([
                "credentials" => get_mturk_credentials(),
                "version" => "2017-01-17",
                "endpoint" => "https://mturk-requester.us-east-1.amazonaws.com",
                "region" => "us-east-1"
            ]);
        } catch (Exception $e) {
            bonus_log("ERROR: Could not create mturk client", ": " . $e->getMessage());
            $m = false;
        }

        if($m !== false)
        {
            foreach($r as $row)
            {
                if(grant_bonus($row["bonus"], $row["worker_id"], $row["assignment_id"], $m))
                {
                    dbQuery($c, "UPDATE responses SET bonus_paid=TRUE WHERE RID=:rid", ["rid" => $row["RID"]]);
                }
            }
        }
	}
}

function grant_bonus($b, $worker_id, $assignment_id, $mturk)
{
    if(!session_id())
        session_start();

    if($b > 0.1 * $_SESSION["max_points"])
		$b = round(0.1 * $_SESSION["max_points"]);
	else if($b < 0)
		$b = 0;

	// b is inputted as an int of cents
	$bonus = $b / 100;
    
    $bonus_str = sprintf("%.2f", $bonus);
    $success = false;
    $balance = "unk";
    $opt = "";

    try {
        $b = $mturk->getAccountBalance();
        $balance = $b["AvailableBalance"];
        if($b["OnHoldBalance"] != "") {
            $balance = $balance . ", " . $b["OnHoldBalance"];
        }
    } catch (Exception $e) {
        $opt = "Balance_Error(" . $e->getMessage() . ")";
        $balance = "unk";
    }
    
    $info = "WID: [" . $worker_id . "], AID: [" . $assignment_id . "], BAL: [" . $balance . "], BON: [" . $bonus_str . "]";

    try {
        $r = $mturk->sendBonus([
            "WorkerId" => $worker_id,
            "AssignmentId" => $assignment_id,
            "BonusAmount" => $bonus_str,
            "Reason" => "Thank you for taking our psychological study!",
            "UniqueRequestToken" => ($worker_id . $assignment_id)
        ]);
        
        $r_json = json_encode($r);
        if($r_json == "{}") {
            $r_json = "";
        }

        if($r_json !== "" || $opt !== "") {
            bonus_log($info . "    SUCCEEDED", ": " . json_encode($r) . "    " . $opt);
        } else {
            bonus_log($info . "    SUCCEEDED", "");
        }
        $success = true;
    } catch (Exception $e) {
        bonus_log($info . "    FAILED", ": " . $e->getMessage() . "    " . $opt);
    }

    return $success;
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

	$result = dbQuery($conn, "INSERT INTO responses SET bonus_paid=FALSE, start_time=:start_time, end_time=:end_time, age=:age, gender=:gender, during=:during, points_phase0=:points_phase0, points_phase1=:points_phase1, worker_id=:worker_id, assignment_id=:assignment_id, bonus=:bonus, tries=:tries", [
			"start_time" => $arr["start_time"],
			"end_time" => $arr["end_time"],
                	"age" => $arr["age"],
                	"gender" => $arr["gender"],
                	"during" => $arr["during"],
                    "tries" => $arr["tries"],
			"points_phase0" => $arr["points_phase0"],
			"points_phase1" => $arr["points_phase1"],
			"worker_id" => $arr["worker_id"],
			"assignment_id" => $arr["assignment_id"],
			"bonus" => $arr["bonus"]
	]);

	$id = $conn->lastInsertId();

	foreach($arr["data"] as $trial)
	{
		if($trial["trial_type"] == "ticket-choose" && $trial["sequence"] > -1)
		{
			dbQuery($conn, "INSERT INTO test_responses SET response=:result, points=:points, phase=:phase, sequence=:sequence, place=:place, RID=$id, next_num=:next_num, prices=:prices", [
					"result" => $trial["result"],
					"points" => $trial["points"],
					"phase" => $trial["phase"],
					"sequence" => $trial["sequence"],
					"place" => $trial["place"],
					"next_num" => $trial["next_num"],
                    "prices" => json_encode($trial["prices"])
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
	}
}

function dbConnect() {
    $dsn = "mysql:host=localhost;dbname=tickets_responses;charset=utf8";
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

function startSession() {
    $_SESSION = array();


    /****                   PARAMETERS                  ****/

    // The number of phases
    $nphases = 1;

    // Number of tickets in each sequence in each test block. Will be shuffled
    $test_blocks = [10];

    // Number of sequences in each block
    $ntest_sequences = 6;

    // The max number of points in a sequence
    $_SESSION["max_points_per_seq"] = 40; // in tenths of a cent

    $_SESSION["site_prefix"] = "/christiane/realistic";

    /****               END PARAMETERS                 ****/

    $_SESSION["max_points"] = $_SESSION["max_points_per_seq"] * $ntest_sequences * count($test_blocks) * $nphases; // in tenths of a cent

    $_SESSION["points"] = [];
    $_SESSION["checked"] = [];
    $_SESSION["got_data"] = 0;
    $_SESSION["finished"] = 0;
    $_SESSION["checked_assoc"] = [];
    for($i = 0; $i < $nphases; $i++) {
        $_SESSION["points"][$i] = 0;
        $_SESSION["checked"][$i] = [];
        $_SESSION["checked_assoc"][$i] = [];

        for($j = 0; $j < count($test_blocks); $j++) {
            $_SESSION["checked"][$i][$j] = [];
            $_SESSION["checked_assoc"][$i][$j] = [];
        }
    }

    $_SESSION["start_time"] = get_time();

    $products = json_decode(file_get_contents("products.json"), true);
    shuffle($products);

    // Generate test data
    $_SESSION["testing_data"] = array();
    $_SESSION["testing_metadata"] = array();
    for($p = 0; $p < $nphases; $p++) {
        $_SESSION["testing_data"][$p] = array();
        $_SESSION["testing_metadata"][$p] = array();
        for($h = 0; $h < count($test_blocks); $h++) {
            $_SESSION["testing_data"][$p][$h] = array();
            $_SESSION["testing_metadata"][$p][$h] = array();
            for($i = 0; $i < $ntest_sequences; $i++) {
                $product = array_pop($products);
                shuffle($product["prices"]);
                $prices = array_slice($product["prices"], 0, $test_blocks[$h]);

                $_SESSION["testing_data"][$p][$h][$i] = $prices;
                unset($product["prices"]);
                $_SESSION["testing_metadata"][$p][$h][$i] = $product;
            }
        }
    }
}

?>
