<?php	
	
	@session_start();
	
	require_once(dirname(__FILE__) . "/../model/Brand.php");
	
	// set the default timezone to use. Available since PHP 5.1
	date_default_timezone_set('Asia/Jakarta');	
	$strdate = date("Y-m-d H:i:s");	
	$struid = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "system";
	
	$ret = false;
	
	$MDL = new Brand();	
	
	#if (!$MDL->isExist($_POST["name"])) {
	if (!$MDL->isBrandDivisionExist($_POST["name"], $_POST["division"])) {
		$MDL->setCode($_POST["code"]);
		$MDL->setName($_POST["name"]);
		$MDL->setDivision($_POST["division"]);
		$MDL->setDescription($_POST["description"]);
		$MDL->setStoreInit($_POST["storeInit"]);
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
		echo "BRAND at DIVISION already exist.";
	}
	
?>