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
	$url = $webService . "/ws_load_sites.php?hash=vendit0re";
	
	echo "read content from web service.. ";
	$json = @file_get_contents($url);
	if ($json) {
		$obj = json_decode($json);
		if ($obj->detail) {
			
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
			$sql = "truncate table mst_site";
			$result = mysql_query($sql);
			echo "ok.\n";
			
			echo "start loop.\n";
			foreach ($obj->detail as $row) {
				# -- INSERT SITE --
				$sql = "insert into mst_site (site, store_code, store_init, store_name, regional_code, regional_init, regional_name) values ('" .
						$row->site . "', '" . $row->store_code . "', '" . $row->store_init . "', '" . $row->store_name . "', '" . $row->regional_code . "', '" . $row->regional_init . "', '" . $row->regional_name . "')";
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