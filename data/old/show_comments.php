<?php

$dir = new DirectoryIterator(dirname(__FILE__));

foreach($dir as $file)
{
	if($file->getExtension() == "json")
	{
		$f = fopen($file->getPathname(), "r");

		$data = json_decode(fread($f, filesize($file->getPathname())), true);

		foreach($data["data"] as $trial)
		{
			if($trial["trial_type"] == "special_sequence")
			{
				printf("%s\n", $trial["special_comment"]);
				break;
			}
		}

		fclose($f);
	}
}

?>
