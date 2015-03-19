<?php	
	
	@session_start();
	
	require_once(dirname(__FILE__) . "/../model/Map.php");
	
	// set the default timezone to use. Available since PHP 5.1
	date_default_timezone_set('Asia/Jakarta');	
	$strdate = date("Y-m-d H:i:s");	
	$struid = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "system";
	
	$ret = false;
	
	$MDL = new Map();	
	
	if (!$MDL->isExist($_POST["code"])) {
		$MDL->setCode($_POST["code"]);
		$MDL->setName($_POST["name"]);
		$MDL->setDescription($_POST["description"]);
		$MDL->setStoreInit($_POST["storeInit"]);
		$MDL->setMapFile($_POST["map"]);
		$MDL->setCreatedBy($struid);
		$MDL->setCreatedDate($strdate);
		
		$ret = $MDL->addNew();
		
		if ($ret) { 
			echo "Success";
		}
		else {
			echo $MDL->getError();
		}	
	}
	else {
		echo "CODE already exist.";
	}
	
?>