<?php
	
	require_once(dirname(__FILE__) . "/../util/ConfigReader.php");
	
	// set the default timezone to use. Available since PHP 5.1
	//date_default_timezone_set('Asia/Jakarta');		
	
	$CR = new ConfigReader("db.conf.php");
	$backupdir = $CR->get("#backupdir");	
	
	# from php manual
	function human_filesize($bytes, $decimals = 2) {
	  	$sz = 'BKMGTP';
	  	$factor = floor((strlen($bytes) - 1) / 3);
	  	
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
	}

	$msg = "";
		
	if ($handle = opendir($backupdir)) {
		/* This is the correct way to loop over the directory. */
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				$msg .= "<tr>" .
							"<td align=\"left\"><span onclick=\"doDownload('" . $file . "')\" title=\"Click to download\" style=\"color:#660033;cursor:pointer;\">" . $file  . "</span></td>" .
							"<td align=\"right\">" . human_filesize(filesize($backupdir . "/" .$file))  . "</td>" .
							"<td align=\"left\">backup</td>" .
							"<td><img src=\"../images/delete_24.png\" border=\"0\" title=\"Delete\" style=\"cursor:pointer;\" onclick=\"goDeleteBackup('" . $file . "')\" ></td>" .
						"</tr>";					
			}												
		}		
		closedir($handle);
		
		if ($msg == "") {
			$msg = "<tr>" .
						"<td colspan=\"4\" align=\"left\">Backup file not found.</td>" .
					"</tr>";
		}
	}
	
	else {			
		$msg = "<tr>" .
					"<td colspan=\"4\" align=\"left\">Failed to read backup file.</td>" .
				"</tr>";			
	}
	
	echo $msg;
?>