<?php
	
	@session_start();
	
	require_once(dirname(__FILE__) . "/../model/UserManager.php");
	
	// set the default timezone to use. Available since PHP 5.1
	date_default_timezone_set('Asia/Jakarta');	
	$strdate = date("Y-m-d H:i:s");	
	$struid = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "system";
				
	$ret = false;				
	$MDL = new UserManager();
	
	if ($MDL->isValidUser($struid, $_POST["old_passwd"])) {			
		$ret = $MDL->modifyPasswdByUserId($struid, $_POST["new_passwd"], $struid, $strdate);			
		
		if ($ret) { 
			echo "Success";
		}
		else {
			echo $MDL->getError();
		}
	}		
	else {
		echo "Error: Unauthorized access.";
	}						
	
?>