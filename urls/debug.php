<?php

require(dirname(__FILE__) . "/../includes.php");

$path = "./";
$conn = dbConnect();

if($handle = opendir($path))
{
	while (false !== ($file = readdir($handle))) {
        	if ('.' === $file) continue;
        	if ('..' === $file) continue;
		if ('debug.php' === $file) continue;

		$f = fopen($file, "r");
		$l = stream_get_line($f, 4096, "\n\n");
		$data = json_decode($l, true);

		if($data == NULL)
			print("Could not load " . $file);
		else if($data["argc"] > 0)
		{
			/*$pos = strpos($data["argv"][0], "assignmentID=");

			if($pos == FALSE) {
				print("[ " . $file . " ]  Could not find position\n");
				continue;
			}*/

			$id = substr($data["argv"][0], 13);

			//print($data["argv"][0] . "\n");

			$r = dbQuery($conn, "SELECT * FROM responses WHERE assignment_id=:id", ["id" => $id]);
			if(empty($r))
				print("[ " . $file . " ]  Could not find assignment id " . $data["argv"][0] . "\n");
		}

		fclose($f);
    	}
    	closedir($handle);
}

?>
