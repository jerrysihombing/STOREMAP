<?php
	
	@session_start();
	
	require_once(dirname(__FILE__) . "/../model/UserManager.php");
	
	// set the default timezone to use. Available since PHP 5.1
	date_default_timezone_set('Asia/Jakarta');	
	$strdate = date("Y-m-d H:i:s");	
	$struid = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "system";		
	
	$ret = false;				
	$MDL = new UserManager();
	
	if ($MDL->isExist($_POST["user_id"])) {			
		$MDL->setUserId($_POST["user_id"]);	
		$MDL->setUserName($_POST["user_name"]);					
		$MDL->setEmail($_POST["email"]);
		$MDL->setDepartement($_POST["departement"]);
		$MDL->setBranchCode($_POST["branch_code"]);
		$MDL->setRoleName($_POST["role_name"]);
		$MDL->setActive($_POST["active"]);
		$MDL->setLastUpdate($strdate);
		$MDL->setLastUser($struid);
		
		if ($_POST["isPasswdReset"] == "true") {
			$MDL->setPasswd($_POST["passwd_reset"]);
			$ret = $MDL->updateWithModifyPasswd($_POST["id"]);
		}
		else {
			$ret = $MDL->update($_POST["id"]);
		}
		
		if ($ret) { 
			echo "Success";
		}
		else {
			echo $MDL->getError();
		}
	}		
	else {
		echo "Error: User ID not found.";
	}

?>