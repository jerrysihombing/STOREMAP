<?php	
	
	@session_start();
	
	require_once(dirname(__FILE__) . "/../model/Status.php");
	
	// set the default timezone to use. Available since PHP 5.1
	date_default_timezone_set('Asia/Jakarta');	
	$strdate = date("Y-m-d H:i:s");	
	$struid = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "system";
	
	$ret = false;
	
	$MDL = new Status();	
	
	if ($MDL->isExist($_POST["code"])) {
		$MDL->setCode($_POST["code"]);
		$MDL->setName($_POST["name"]);
		$MDL->setDescription($_POST["description"]);
		$MDL->setColor(strtoupper($_POST["color"]));
		$MDL->setMinValue($_POST["minValue"]);
		$MDL->setMaxValue($_POST["maxValue"]);
		$MDL->setMinValueWide($_POST["minValueWide"]);
		$MDL->setMaxValueWide($_POST["maxValueWide"]);
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
		echo "CODE not found.";
	}
	
?>