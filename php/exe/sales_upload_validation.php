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
	$logfile = $logdir . "validate_sales_" . $strdate2 . "_" . $struid . ".log";
	$invalidfile = $logdir . "invalid_sales_" . $strdate2 . "_" . $struid . ".log";
	
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
			
			$storeInit = $data->sheets[0]['cells'][1][2];
			$transDate = $data->sheets[0]['cells'][2][2];
			#$transDate = "24022015";
			
			if (empty($storeInit)) {
				$invalidFound++;
				fwrite($h2, "Empty store initial found at row 1." . ENTER);
			}
			
			if (empty($transDate)) {
				$invalidFound++;
				fwrite($h2, "Empty trans date found at row 2." . ENTER);
			}
			else {
				if (!checkdate(substr($transDate, 2, 2), substr($transDate, 0, 2), substr($transDate, 4))) {
					$invalidFound++;
					fwrite($h2, "Invalid date format found at row 2, please use ddmmyyyy." . ENTER);
				}
			}
			
			for ($i = 5; $i <= $data->sheets[0]['numRows']; $i++) {						
				
				$brandName = $data->sheets[0]['cells'][$i][1];
				$articleType = $data->sheets[0]['cells'][$i][2];
				$quantity = $data->sheets[0]['cells'][$i][3];
				$amount = $data->sheets[0]['cells'][$i][4];
				
				# entry test
				$check = $brandName . $articleType . $quantity . $amount;
				
				if ($check != "") {
					
					if (empty($brandName)) {
						$invalidFound++;
						fwrite($h2, "Empty brand name found at row " . $i . "." . ENTER);
					}
					else {
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
							fwrite($h2, "Invalid brand name found at row " . $i . "." . ENTER);
						}
						mysql_free_result($result);
					}
					
					if (!($articleType == "NORMAL" || $articleType == "OBRAL")) {
						$invalidFound++;
						fwrite($h2, "Invalid article type found at row " . $i . ". Only NORMAL or OBRAL allowed." . ENTER);
					}
					
					if ($quantity == "") {
						$invalidFound++;
						fwrite($h2, "Empty quantity found at row " . $i . ". Please enter 0 for no quantity." . ENTER);
					}
					
					if ($amount == "") {
						$invalidFound++;
						fwrite($h2, "Empty sales found at row " . $i . ". Please enter 0 for no sales." . ENTER);
					}
				
				} # end check
				
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