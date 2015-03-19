<?php	
	
	@session_start();
	
	require_once(dirname(__FILE__) . "/../model/Brand.php");
	
	// set the default timezone to use. Available since PHP 5.1
	date_default_timezone_set('Asia/Jakarta');	
	$strdate = date("Y-m-d H:i:s");	
	$struid = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "system";
	
	$ret = false;
	
	$MDL = new Brand();	
	
	if ($MDL->isExist($_POST["name"])) {
		$MDL->setName($_POST["name"]);
		$MDL->setDivision($_POST["division"]);
		$MDL->setDescription($_POST["description"]);
		$MDL->setStoreInit($_POST["storeInit"]);
		$MDL->setLastUser($struid);
		$MDL->setLastUpdate($strdate);
		
		$ret = $MDL->update($_POST["id"]);
		
		if ($ret) { 
			echo "Success";
		}
		else {
			echo $MDL->getError();
		}	
	}
	else {
		echo "BRAND not found.";
	}
	
?>