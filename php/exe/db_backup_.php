<?php
	
	require_once(dirname(__FILE__) . "/../util/ConfigReader.php");
	
	// set the default timezone to use. Available since PHP 5.1
	date_default_timezone_set('Asia/Jakarta');		
	
	$CR = new ConfigReader("db.conf.php");

	$host = $CR->get("#host");
	$dbname = $CR->get("#dbname");
	$user = $CR->get("#user");
	$pass = $CR->get("#pass");
	$backupdir = $CR->get("#backupdir");	
	$backup_file = $backupdir . "/" . $dbname . "-" . date("YmdHis") . ".sql";
					
	$msg = "";
	$res = 0;
	$doit = true;
						
	if ($doit) {					
		$command = "mysqldump -u $user -p$pass $dbname --routines > $backup_file";
		try {
			exec($command);
			#system($command);
			$res = 1;
		}
		catch (Exception $e) {
			$res = 0;
		}
		
	}
	
	if ($res == 1) {		
		$msg = "Database has been backup successfull. ";
	}
	else {
		$msg = "Error: Failed to backup database.";
	}
		
	echo $msg;
		
?>