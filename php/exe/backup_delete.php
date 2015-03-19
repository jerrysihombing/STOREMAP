<?php

	require_once(dirname(__FILE__) . "/../util/ConfigReader.php");
	
	// set the default timezone to use. Available since PHP 5.1
	//date_default_timezone_set('Asia/Jakarta');		
	
	$CR = new ConfigReader("db.conf.php");
	$backupdir = $CR->get("#backupdir");
	
	$msg = "";
	
	$backupname = $backupdir . "/" . $_POST["backup_name"];
	
	if (file_exists($backupname)) {
		if (unlink($backupname)) {
			$msg = "File deleted successfully.";	
		}
		else {
			$msg = "Error: Failed to delete file.";
		}
	} else {
		$msg = "Error: File does not exist.";
	}						
		
	echo $msg;
?>