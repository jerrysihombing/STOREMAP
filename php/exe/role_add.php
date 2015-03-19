<?php
	
	@session_start();
	
	require_once(dirname(__FILE__) . "/../model/RoleManager.php");
	
	// set the default timezone to use. Available since PHP 5.1
	date_default_timezone_set('Asia/Jakarta');	
	$strdate = date("Y-m-d H:i:s");	
	$struid = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "system";
		
	$ret = false;				
	$MDL = new RoleManager();
						
	if (!$MDL->isExist($_POST["role_name"])) {			
		$MDL->setRoleName($_POST["role_name"]);
		$MDL->setDescription($_POST["description"]);
		$MDL->setDetail($_POST["menu_items"]);
		$MDL->setCreatedDate($strdate);
		$MDL->setCreatedBy($struid);
		
		$ret = $MDL->addNew();
		
		if ($ret) { 
			echo "Success";
		}
		else {
			echo $MDL->getError();
		}
	}		
	else {
		echo "Error: Role name already exist.";
	}	
?>