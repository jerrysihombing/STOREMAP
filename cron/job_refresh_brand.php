<?php	
	
	// set the default timezone to use. Available since PHP 5.1
	date_default_timezone_set('Asia/Jakarta');
	
	require_once(dirname(__FILE__) . "/../php/util/ConfigReader.php");
	
	$CR = new ConfigReader("db.conf.php");
	
	$jUser = $CR->get("#user");
	$jPass = $CR->get("#pass");
	$jDb = $CR->get("#dbname");
	$jHost = $CR->get("#host");
	$webService = $CR->get("#webservice");
	
	echo "start job at " . date("Y-m-d H:i:s") . "\n";
	
	echo "set web service..\n";
	$url = $webService . "/ws_brand.php";
	
	echo "read content from web service.. ";
	$json = @file_get_contents($url);
	if ($json) {
		$obj = json_decode($json);
		if ($obj->brands) {
			
			#print_r($obj->brands);exit;
			
			echo "got.\n";
			echo "make connection to database.. ";
			# make connection
			$conn = mysql_connect($jHost, $jUser, $jPass);
			if (!$conn) {
				echo "Error: cannot connect to database. Leave job.\n";
				exit;
			}
			echo "connected.\n";
			echo "select database.. ";
			if (!mysql_select_db($jDb)) {
				mysql_close($conn);
				echo "Error: cannot use database.";
				exit;
			}
			echo "got.\n";
			# eo make connection

			$result = mysql_query("START TRANSACTION");
			
			echo "emptying table.. ";
			# -- empty first
			$sql = "truncate table mst_brand";
			$result = mysql_query($sql);
			echo "ok.\n";
			
			echo "start loop.\n";
			foreach ($obj->brands as $row) {
				# -- INSERT BRAND --
				$sql = "insert into mst_brand (code, name, division, description, last_user, last_update) values ('" .
						mysql_real_escape_string($row->brand_code) . "', '" . mysql_real_escape_string($row->brancd_desc) . "', '" .
						mysql_real_escape_string($row->division) . "', '" . mysql_real_escape_string($row->division_code) . "', 'system', sysdate())";
				
				$result = mysql_query($sql);
			}
			echo "end loop.\n";
			
			$result = mysql_query("COMMIT");
			
			echo "close connection.\n";			
			# close connection
			mysql_close($conn);
		
		}
	}
	
	echo "job done.. leave job.\n";
	echo "finished job at " . date("Y-m-d H:i:s") . "\n\n";
	
?>