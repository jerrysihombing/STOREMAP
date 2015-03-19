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
	$logfile = $logdir . "process_sales_" . $strdate2 . "_" . $struid . ".log";
	
	# create or open log file
	$h = fopen($logfile, "w");
	
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
			echo "Error: cannot connect to database.";
			exit;
		}
		if (!mysql_select_db($jDb)) {
			mysql_close($conn);
			fwrite($h, "Failed, cannot select DB. Leaving procedure" . "." . ENTER);
			# close the log file
			fclose($h);
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
		
		# only if 1st sheet exists
		if ($sheetCnt >= 1) {
			# tell the log size of rows
			fwrite($h, "1st sheet is consist of " . $data->sheets[0]['numRows'] . " rows.\n");
			
			$result = mysql_query("START TRANSACTION");
			
			$storeInit = $data->sheets[0]['cells'][1][2];
			$transDate = $data->sheets[0]['cells'][2][2];
			#$transDate = "24022015" => "2015-02-24";
			$transDate = substr($transDate, 4, 4) . "-" . substr($transDate, 2, 2) . "-" . substr($transDate, 0, 2);
			
			for ($i = 5; $i <= $data->sheets[0]['numRows']; $i++) {	
				
				$brandName = $data->sheets[0]['cells'][$i][1];
				$articleType = $data->sheets[0]['cells'][$i][2];
				$articleType = (strtoupper($articleType) == "OBRAL" ? 1 : 0);
				$quantity = (is_numeric($data->sheets[0]['cells'][$i][3]) ? $data->sheets[0]['cells'][$i][3] : 0);
				$amount = (is_numeric($data->sheets[0]['cells'][$i][4]) ? $data->sheets[0]['cells'][$i][4] : 0);
				
				# entry test
				$check = $brandName . $articleType . $quantity . $amount;
				
				if ($check != "") {
					
					# -- copy existing trans_date + brand_name + article_type sales to history
					$sql =  "insert into trn_sales_hst (id_ori, trans_date, brand_name, article_type, quantity, amount, store_init, created_by, created_date, last_user, last_update) " .
							"select id, trans_date, brand_name, article_type, quantity, amount, store_init, created_by, created_date, last_user, last_update from trn_sales ".
							"where date_format(trans_date, '%Y-%m-%d') = '" . $transDate . "' and brand_name = '" . $brandName . "' and article_type = " . $articleType . " and store_init = '" . $storeInit . "'";
					$result = mysql_query($sql);
					
					# -- delete existing trans_date + brand_name + article_type sales
					$sql = "delete from trn_sales where date_format(trans_date, '%Y-%m-%d') = '" . $transDate . "' and brand_name = '" . $brandName . "' and article_type = " . $articleType . " and store_init = '" . $storeInit . "'";
					$result = mysql_query($sql);
					
					# -- INSERT SALES --
					$sql = "insert into trn_sales (trans_date, brand_name, article_type, quantity, amount, store_init, created_by, created_date) values ('" .
							$transDate . "', '" . $brandName . "', '" . $articleType . "', '" . $quantity . "', '" . $amount . "', '" . $storeInit . "', '" . $struid . "', '" . $strdate . "')";
					$result = mysql_query($sql);
					
					fwrite($h, ".." . $sql . "." . ENTER);
				
				} # end check
				
			}
			
			$result = mysql_query("COMMIT");
			
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
		
		$msg = "Success";
	}

	#else {
	#	$error = "File " . $region . " not found.";
	#}
	
	# close the log file
	fclose($h);
	
	echo $msg;
	
?>