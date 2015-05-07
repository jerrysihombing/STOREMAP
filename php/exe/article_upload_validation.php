<?php	
	
	@session_start();
	
    require_once(dirname(__FILE__) . "/../util/ConfigReader.php");
	require_once dirname(__FILE__) . '/../util/ExcelReader.php';
	
	// set the default timezone to use. Available since PHP 5.1
	date_default_timezone_set("Asia/Jakarta");	
	$strdate = date("Y-m-d H:i:s");
	$strdate2 = date("YmdHis");	
	$struid = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "system";
	
	# use this to prevent script from stopping premature
	#set_time_limit(300); # 5 minutes
	#set_time_limit(600); # 10 minutes
	set_time_limit(900); # 15 minutes
	#set_time_limit(1200); # 20 minutes
	#set_time_limit(1500); # 25 minutes
	#set_time_limit(1800); # 30 minutes
	
	# do not display errors
	ini_set("display_errors", "0");
	
    $CR = new ConfigReader("db.conf.php");
	
	$jUser = $CR->get("#user");
	$jPass = $CR->get("#pass");
	$jDb = $CR->get("#dbname");
	$jHost = $CR->get("#host");
	$uploaddir = $CR->get("#uploaddir2") . "/";
	$logdir = $CR->get("#logdir") . "/";
	
	# change this to target location
	define("UPLOAD_DIR", $uploaddir);
    define("LOG_DIR", $logdir);
    define("ENTER", "\n"); # unix
	#define("ENTER", "\r\n"); # windows
	
	$filename = UPLOAD_DIR . $_POST["fileToExe"];
	
	$msg = "";
		
	# preparing log file
	$logfile = $logdir . "validate_article_" . $strdate2 . "_" . $struid . ".log";
	$invalidfile = $logdir . "invalid_article_" . $strdate2 . "_" . $struid . ".log";
	
	# create or open log file
	$h = fopen($logfile, "w");
	$h2 = fopen($invalidfile, "w");
		
	if (!empty($_POST["fileToExe"]) && file_exists($filename)) {
		
		# -- file processing -- #
		
		# write to log	
		$start_date = date("M j, Y, g:i a"); # -> Jan 10, 2001, 5:16 pm
		fwrite($h, "Trying to read excel file by " . $struid . " at " . $start_date . "." . ENTER);
		fwrite($h, "Trying to make database connection" . "." . ENTER);
		
		# make connection
		$conn = mysql_connect($jHost, $jUser, $jPass);
		if (!$conn) {
			fwrite($h, "Failed, cannot connect. Leaving procedure" . "." . ENTER);
			# close the log file
			fclose($h);
			fclose($h2);
			echo "Error: cannot connect to database.";
			exit;
		}
		if (!mysql_select_db($jDb)) {
			mysql_close($conn);
			fwrite($h, "Failed, cannot select DB. Leaving procedure" . "." . ENTER);
			# close the log file
			fclose($h);
			fclose($h2);
			echo "Error: cannot use database.";
			exit;
		}	
		# eo make connection
		
		fwrite($h, "..connected" . ".\n\n");
		
		// ExcelFile($filename, $encoding);
		$data = new Spreadsheet_Excel_Reader();
	
		// Set output Encoding.
		$data->setOutputEncoding('CP1251');
		
		/***
		* By default rows & cols indeces start with 1
		* For change initial index use:
		* $data->setRowColOffset(0);
		*
		**/

		$data->read($filename);	
		$sheetCnt = count($data->sheets);
		$invalidFound = 0;
		
		# only if 1st sheet exists
		if ($sheetCnt >= 1) {
			# tell the log size of rows
			fwrite($h, "1st sheet is consist of " . $data->sheets[0]['numRows'] . " rows.\n");
			
			for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {	
				
				$division = $data->sheets[0]['cells'][$i][1];
				$brandName = $data->sheets[0]['cells'][$i][2];
				$articleType = $data->sheets[0]['cells'][$i][3];
				$plu8 = $data->sheets[0]['cells'][$i][4];
				$description = $data->sheets[0]['cells'][$i][5];
				
				# entry test
				$check = $division . $brandName . $articleType . $plu8 . $description;
				
				if ($check != "") {
					
					# check division
					$sql = "select count(code) CNT from mst_division where upper(name) = upper('" . mysql_real_escape_string($division) . "')";
					$result = mysql_query($sql);
					if (!$result) {
						mysql_close($conn);
						fwrite($h, "Failed, cannot do a 'division' query. Leaving procedure" . "." . ENTER);
						# close the log file
						fclose($h);
						fclose($h2);
						echo "Error: cannot do a query.";
						exit;
					}
					$found = 0;
					if ($row = mysql_fetch_assoc($result)) {
						$found = $row["CNT"];
					}
					if (!$found) {
						$invalidFound++;
						fwrite($h2, "Invalid division found at row " . $i . "." . ENTER);
					}
					mysql_free_result($result);
					
					# check brand
					$sql = "select count(id) CNT from mst_brand where upper(name) = upper('" . mysql_real_escape_string($brandName) . "')";
					$result = mysql_query($sql);
					if (!$result) {
						mysql_close($conn);
						fwrite($h, "Failed, cannot do a 'brand' query. Leaving procedure" . "." . ENTER);
						# close the log file
						fclose($h);
						fclose($h2);
						echo "Error: cannot do a query.";
						exit;
					}
					$found = 0;
					if ($row = mysql_fetch_assoc($result)) {
						$found = $row["CNT"];
					}
					if (!$found) {
						$invalidFound++;
						fwrite($h2, "Invalid brand found at row " . $i . "." . ENTER);
					}
					mysql_free_result($result);
					
					if (empty($articleType)) {
						$invalidFound++;
						fwrite($h2, "Empty article type found at row " . $i . "." . ENTER);
					}
					
					if (empty($plu8)) {
						$invalidFound++;
						fwrite($h2, "Empty plu found at row " . $i . "." . ENTER);
					}
					
					# check article exist
					$sql = "select count(id) CNT from mst_article where upper(plu8) = upper('" . mysql_real_escape_string($plu8) . "')";
					$result = mysql_query($sql);
					if (!$result) {
						mysql_close($conn);
						fwrite($h, "Failed, cannot do a 'plu' query. Leaving procedure" . "." . ENTER);
						# close the log file
						fclose($h);
						fclose($h2);
						echo "Error: cannot do a query.";
						exit;
					}
					$found = 0;
					if ($row = mysql_fetch_assoc($result)) {
						$found = $row["CNT"];
					}
					if ($found) {
						$invalidFound++;
						fwrite($h2, "Plu exists found at row " . $i . "." . ENTER);
					}
					mysql_free_result($result);
					
				}
				# end check
			
			}
			
			fwrite($h, "read 1st sheet done" . "." . ENTER);
			
		}
		
		# write to log	
		fwrite($h, "\n" . "Done.\n");
		$end_date = date("M j, Y, g:i a"); # -> Jan 10, 2001, 5:16 pm
		fwrite($h, "Finish the job at " . $end_date . ".\n\n");
		
		fwrite($h, "Close database connection" . "." . ENTER);
		# close connection
		mysql_close($conn);
		
		fwrite($h, "Leave procedure" . "." . ENTER);
		
		# -- ends file processing -- #
		
		if ($invalidFound) {
			$msg = "Invalid found. " . $invalidfile;
		}
		else {
			$msg = "Success";	
		}
		
	}

	#else {
	#	$error = "File " . $region . " not found.";
	#}
	
	# close the log file
	fclose($h2);
	fclose($h);
	
	echo $msg;
	
?>