<?php
// the $_POST[] array will contain the passed in filename and data
// the directory "data" is writable by the server (chmod 777)

if(isset($_POST['filename']) && isset($_POST['filedata']))
{
	$filename = "./data/" . $_POST['filename'];
	$data = $_POST['filedata'];
	// write the file to disk
	file_put_contents($filename, $data);
}

?>
