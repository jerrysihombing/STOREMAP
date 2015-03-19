<?php	
	
	@session_start();
	
	require_once(dirname(__FILE__) . "/../model/Data.php");
	
	// set the default timezone to use. Available since PHP 5.1
	date_default_timezone_set('Asia/Jakarta');	
	$strdate = date("Y-m-d H:i:s");	
	$struid = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "system";
	
	$ret = false;
	
	$MDL = new Data();	
	
	if (!$MDL->isExist($_POST["mapCode"], $_POST["storemapCode"], $_POST["dataCategory"], $_POST["dataMonth"], $_POST["dataYear"])) {
		$MDL->setMapCode($_POST["mapCode"]);
		$MDL->setStoremapCode($_POST["storemapCode"]);
		$MDL->setDataCategory($_POST["dataCategory"]);
		$MDL->setDataValue($_POST["dataValue"]);
		$MDL->setDataMonth($_POST["dataMonth"]);
		$MDL->setDataYear($_POST["dataYear"]);
		$MDL->setDescription($_POST["description"]);
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
		echo "Data " . $_POST["mapCode"] . ", " . $_POST["storemapCode"] . ", " . $_POST["dataCategory"] . ", " . $_POST["dataMonth"] . ", " . $_POST["dataYear"] . " already exist.";
	}
	
?>