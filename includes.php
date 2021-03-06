<?php

require("./repos/mturk-php/mturk.php");

function logging($mes)
{
	$f = fopen("./logging.txt", "a");
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

    $p = intval(round(20 * ($arr[count($arr) - 1] - $answer) / ($arr[count($arr) - 1] - $arr[0])));

	return $p;
}


// returns an integer for the number of cents
function get_bonus($points)
{
	$b = round($points * 0.1);

	if($b > 400)
		return 400;
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

	if($bonus > 4)
		$bonus = 4;
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

	$result = dbQuery($conn, "INSERT INTO responses SET bonus_paid=FALSE, start_time=:start_time, end_time=:end_time, age=:age, gender=:gender, tries=:tries, during=:during, points_phase0=:points_phase0, points_phase1=:points_phase1, worker_id=:worker_id, assignment_id=:assignment_id, bonus=:bonus, training_sort=:training_sort", [
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
            "training_sort" => $arr["training_sort"],
			"bonus" => $arr["bonus"]
	]);

	$id = $conn->lastInsertId();

	foreach($arr["data"] as $trial)
	{
		if($trial["trial_type"] == "bar-choose")
		{
			if(!isset($trial["result"]) || $trial["result"] != "passed")
            {
                foreach($trial["responses"] as $bar_response)
                {
                    dbQuery($conn, "INSERT INTO bar_responses SET response=:value, offby=:offby, category=:category, category_index=:category_index, RID=$id, phase=" . $trial["phase"] . ", number=" . $trial["number"] . ", repeat_num=" . $trial["repeat_num"], $bar_response);
                }
		    }
        }
		else if($trial["trial_type"] == "ticket-choose" && $trial["sequence"] > -1)
		{
//			var_dump($trial);
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
		else if($trial["trial_type"] == "training_avg")
		{
			if(!isset($trial["result"]) || $trial["result"] != "passed")
            {
                dbQuery($conn, "INSERT INTO training_responses SET sequence=:sequence, RID=$id, avg=:avg, response=:response, phase=:phase, repeat_num=:repeat_num", [
                        "sequence" => $trial["sequence"],
                        "avg" => $trial["avg"],
                        "response" => $trial["response"],
                        "phase" => $trial["phase"],
                        "repeat_num" => $trial["repeat_num"]
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

// This function generates a random number within the skewed normal distribution with parameters
// location, scale and shape. **THIS IS THE TICKET GENERATION FUNCTION**
function sn_generate_deviate($location, $scale, $shape)
{
    // Generate two random uniformly distributed numbers between 0 and 1
    $max = mt_getrandmax();
    $x1 = mt_rand() / $max;
    $x2 = mt_rand() / $max;
    
    // Generate two random numbers on the normal distribution using the Box-Muller transform
    $sq = sqrt(-2 * log($x1));
    $y1 = $scale * $sq * cos(2 * M_PI * $x2) + $location;

    if($shape == 0.0)
        return $y1;

    $y2 = $scale * $sq * sin(2 * M_PI * $x2) + $location;
    
    $u = max($y1, $y2);
    $v = min($y1, $y2);

    $sq2 = sqrt(2 * (1 + $shape * $shape));
    $l1 = (1 + $shape) / $sq2;
    $l2 = (1 - $shape) / $sq2;

    // Generate one random number on the skewed normal distribution using the min-max method
    $y = $l1 * $u + $l2 * $v;
    //$y = $scale * ($shape * abs($y1) + $y2) / sqrt(1 + $shape * $shape) + $location;  // Henze's formula

    return $y;
}

// This estimates the error function (erf(x)) with maximum error 1.5 * 10^(-7). This approximation was
// given by Abramowitz and Stegun, and is described on the Wikepedia page for the error function.
function erf($x)
{
    $p = 0.3275911;
    $t = 1 / (1 + $p * $x);
    $a1 = 0.254829592;
    $a2 = -0.284496736;
    $a3 = 1.421413741;
    $a4 = -1.453152027;
    $a5 = 1.061405429;

    $v = $a1*$t + $a2*$t*$t + $a3*$t*$t*$t + $a4*$t*$t*$t*$t + $a5*$t*$t*$t*$t*$t;

    return (1 - $v * exp(-$x*$x));
}

// Estimate an integral by performing a sum from $l to $u of $func, with steps dx = $step, and
// additional parameters passed to $func $params
function integrate($func, $l, $u, $step, $params)
{
    // Holds the total integral
    $sum = 0.0;

    // If u is greater than l, the we are integrating from l to u
    $x = $l;
    $max = $u;

    // If u is less than l, then we are integrating from u to l, but afterwards the value is multiplied
    // by -1
    if($u < $l) {
        $x = $u;
        $max = $l;
    }

    // Integrate, using the function and updating x by step with each iteration
    while($x <= $max) {
        $sum += $func($x, $params) * $step;
        $x += $step;
    }

    // Multiply the integral by -1 if a is smaller than 0
    if($u < $l)
        $sum = -$sum;

    return $sum;
}

// Function within integral of Owen's T function
function t_func_inner($x, $params)
{
    return exp(-0.5 * $params['h'] * $params['h'] * (1 + $x * $x)) / ((1 + $x * $x) * 2 * M_PI);
}

// This estimates Owen's T function by numerically integrating, based on the definition of the function.
function t_func($h, $a)
{
    return integrate('t_func_inner', 0, $a, 0.001, ['h' => $h]);
}

// This function estimates the CDF of the skewed normal distribution evaluated at the given point (x) 
// and with the given properties (location, scale, shape). This formula for the CDF of the skewed normal
// distribution was taken from the Wikipedia page for the skewed normal distribution
function sn_cdf($x, $location, $scale, $shape)
{
    $f = 0.5 * (1 + erf(($x - $location) / ($scale * M_SQRT2)));
    
    $t = t_func(($x - $location) / $scale, $shape);

    return ($f - 2 * $t);
}

// This function generates a number on the log-normal distribution
function ln_generate_deviate($mean, $stddev)
{
    // Generate two random uniformly distributed numbers between 0 and 1
    $max = mt_getrandmax();
    $x1 = mt_rand() / $max;
    $x2 = mt_rand() / $max;
    
    // Generate one random number on the normal distribution using the Box-Muller transform
    $y1 = $stddev * sqrt(-2 * log($x1)) * cos(2 * M_PI * $x2) + $mean;

    // Generate a number on the standard log-normal distribution
    $y = exp($y1);

    return $y;
}

// Estiamte the CDF of the log-normal distribution evaluated at $x
function ln_cdf($x, $mean, $stddev)
{
    return (0.5 + 0.5 * erf((log($x) - $mean) / (M_SQRT2 * $stddev)));
}

// Function within integral of beta function
function beta_inner($x, $params)
{
    return pow($x, $params['a'] - 1) * pow(1 - $x, $params['b'] - 1);
}

// Approximation of incomplete beta function
function betainc($z, $a, $b)
{
    return integrate('beta_inner', 0, $z, 0.00002, ['a' => $a, 'b' => $b]);
}

// Approximates the root of a function $func, with derivative $deriv, with Newton's method, with
// initial guess $guess, performing the loop $iters times, and passing additional parameters
// $params to $func and $deriv
/*function newtons($func, $deriv, $guess, $iters, $params)
{
    $x = $guess;

    for($i = 0; $i < $iters; $i++) {
        $x = $x - $func($x, $params) / $deriv($x, $params);
    }

    return $x;
}*/

// Approximation of regularized incomplete beta function
function betaincreg($x, $params)
{
    $v = betainc($x, $params['a'], $params['b']) / betainc(1, $params['a'], $params['b']);

    /*echo 'B(';
    echo $x;
    echo ', ';
    echo $params['a'];
    echo ', ';
    echo $params['b'];
    echo '): ';
    echo $v;
    echo '   ';
*/
    return $v;
}

// Approximation of the digamma function
/*function digamma($x)
{
    $x2 = $x * $x;
    $x4 = $x2 * x2;
    $x6 = $x4 * $x2;
    $x8 = $x6 * $x2;
    $x10 = $x8 * $x2;
    $x12 = $x10 * $x2;
    $x14 = $x12 * $x2;

    $g = log($x) - 1 / (2 * $x) - 1 / (12 * $x2) + 1 / (120 * $x4) - 1 / (252 * $x6) + 1 / (240 * $x8)
                 - 5 / (660 * $x10) + 691 / (32760 * $x12) - 1 / (12 * $x14);

    return $g;
}

// Approximation of the derivative of the regularized incomplete beta function
function betaincregderiv($x, $params)
{
    $v = pow(1 - $x, $params['b'] - 1) * pow($x, $params['a'] - 1) / betainc(1, $params['a'], $params['b']);

    return $v;
}

// Approximation of inverse of regularized incomplete beta function with respect to $x
function betaincreginv($x, $a, $b)
{
    $v = newtons('betaincreg', 'betaincregderiv', 0.9, 1000, ['a' => $a, 'b' => $b]);

    return $v;
}

// Generate a random number on the PERT distribution
function pert_generate_deviate($min, $max, $mode)
{
    $u = mt_rand() / mt_getrandmax();

    $a1 = (4 * $mode + $max - 5 * $min) / ($max - $min);
    $a2 = (5 * $max - $min - 4 * $mode) / ($max - $min);

    $x = ($max - $min) * betaincreginv($u, $a1, $a2) + $min;
    
    echo $x;
    echo ' ';

    return $x;
}*/

// Approximate the CDF of the PERT distribution at $x
function pert_cdf($x, $min, $max, $mode)
{
    if($x <= $min)
        return 0;
    else if($x >= $max)
        return 1;

    $a1 = (4 * $mode + $max - 5 * $min) / ($max - $min);
    $a2 = (5 * $max - $min - 4 * $mode) / ($max - $min);
    $z = ($x - $min) / ($max - $min);

    //echo 'x: ' . $x . '  max: ' . $max . '  min: ' . $min . "\n";

    $v = betaincreg($z, ['a' => $a1, 'b' => $a2]);

    //echo 'betaincreg(' . $z . ', ' . $a1 . ', ' . $a2 . ') = ' . $v . "\n";

    return $v;
}

function startSession() {
    $_SESSION = array();


    /****                   PARAMETERS                  ****/ 

    // The number of phases
    $nphases = 2;

    // The number of sequences in one training phase
    $ntraining_sequences = 1;

    // The number of tickets in one sequence in the training phase
    $ntraining_tickets = 30;

    // Parameters of skewed normal distribution
    $location = 150;
    $scale = 30;
    $shape = 20;

    // Parameters of log-normal distribution
    $mean = 5.2;
    $stddev = 0.2;

    // Parameters for PERT distribution
    $p_min = 120;
    $p_max = 220;
    $p_mode = 125;

    // Which distribution to use, set to 'pert', 'ln' (log-normal), or 'sn' (skew-normal)
    $dist = 'pert';

    // Minimum and maximum values for the deviates in case we get a really unlikely one
    $min = 120;
    $max = 240;

    $_SESSION["training_max_repeats"] = 3;
    $_SESSION["training_threshold"] = 0.25;
    
    $training_divisions = [110, 120.5, 130.5, 140.5, 150.5, 160.5, 170.5, 180.5, 190.5, 200];

    $_SESSION["training_sort_total"] = 100;
    
    //$_SESSION["training_avg_ranges"] = [[120, 240], [120, 240]];
    
    // Number of sequences in each test phase
    $ntest_sequences = 200;

    // Number of tickets in one test sequence
    $ntest_tickets = 10;

    // The max number of points in a phase
    $_SESSION["max_points"] = 20 * $ntest_sequences;
    
    /****               END PARAMETERS                 ****/

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

    $pert_tickets = [];
    $pert_training = [];
    $ticket_counter = 0;
    if($dist == 'pert') {
        $pert_tickets = file('vec_test_right.csv', FILE_IGNORE_NEW_LINES);
        array_splice($pert_tickets, 0, 1);
        shuffle($pert_tickets);
    
        $training = file('vec_train_right.csv');

        for($i = 1; $i < count($training); $i++)
            $pert_training[$i - 1] = str_getcsv($training[$i]);
    }

    // Generate training data
    $_SESSION["training_data"] = [];
    for($h = 0; $h < $nphases; $h++) {
        $_SESSION["training_data"][$h] = [];
        for($l = 0; $l < $_SESSION["training_max_repeats"]; $l++) {
            $_SESSION["training_data"][$h][$l] = [];
            for($i = 0; $i < $ntraining_sequences; $i++) {
                if($dist == 'pert') {
                    $_SESSION["training_data"][$h][$l][$i] = $pert_training[$l];
                    shuffle($_SESSION["training_data"][$h][$l][$i]);
                }
                else {
                    for($j = 0; $j < $ntraining_tickets; $j++) {
                        $v = 0;
                        if($dist == 'ln')
                            $v = (int)round(ln_generate_deviate($mean, $stddev));
                        else
                            $v = (int)round(sn_generate_deviate($location, $scale, $shape));
                
                        if($v > $max)
                            $v = $max;
                        else if($v < $min)
                            $v = $min;

                        $_SESSION["training_data"][$h][$l][$i][$j] = $v;
                    }
                }
            }
        }
    }

    $_SESSION["training_answers"] = [];
    $_SESSION["training_answers"][0] = [];
    $_SESSION["training_answers"][1] = [];
    $_SESSION["training_categories"] = [];
    $_SESSION["training_categories"][0] = [];
    $_SESSION["training_categories"][1] = [];

    $total_n = 0;

    $prev_cdf = 0;
    if($dist == 'pert')
        $prev_cdf = pert_cdf($training_divisions[0], $p_min, $p_max, $p_mode);
    else if($dist == 'ln')
        $prev_cdf = ln_cdf($training_divisions[0], $mean, $stddev);
    else
        $prev_cdf = sn_cdf($training_divisions[0], $location, $scale, $shape);

    for($i = 1; $i < count($training_divisions); $i++) {
        $cdf = 0;
        if($dist == 'pert')
            $cdf = pert_cdf($training_divisions[$i], $p_min, $p_max, $p_mode);
        else if($dist == 'ln')
            $cdf = ln_cdf($training_divisions[$i], $mean, $stddev);
        else
            $cdf = sn_cdf($training_divisions[$i], $location, $scale, $shape);
        
        $frac = $cdf - $prev_cdf;
        $prev_cdf = $cdf;

        $n = intval(round($_SESSION["training_sort_total"] * $frac));
        $total_n += $n;

        $_SESSION["training_answers"][0][$i - 1] = $_SESSION["training_answers"][1][$i - 1] = $n;

        $_SESSION["training_categories"][0][$i - 1] = $_SESSION["training_categories"][1][$i - 1] = 
            "$" . (int)ceil($training_divisions[$i - 1]) . " - $" . (int)floor($training_divisions[$i]);
    }

    $_SESSION["training_sort_total"] = $total_n;

    // Generate test data
    $_SESSION["testing_data"] = array();
    for($h = 0; $h < $nphases; $h++) {
        $_SESSION["testing_data"][$h] = array();
        for($i = 0; $i < $ntest_sequences; $i++) {
            $_SESSION["testing_data"][$h][$i] = array();
            for($j = 0; $j < $ntest_tickets; $j++) {
                $v = 0;
                if($dist == 'pert')
                    $v = (int)$pert_tickets[$ticket_counter++ % count($pert_tickets)];
                else if($dist == 'ln')
                    $v = (int)round(ln_generate_deviate($mean, $stddev));
                else
                    $v = (int)round(sn_generate_deviate($location, $scale, $shape));
                
                if($v > $max)
                    $v = $max;
                else if($v < $min)
                    $v = $min;

                $_SESSION["testing_data"][$h][$i][$j] = $v;
            }
        }
    }
}

?>
