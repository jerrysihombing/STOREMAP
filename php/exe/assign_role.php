<?php
	
	@session_start();
	
	require_once(dirname(__FILE__) . "/../model/UserManager.php");
	
	// set the default timezone to use. Available since PHP 5.1
	date_default_timezone_set('Asia/Jakarta');	
	$strdate = date("Y-m-d H:i:s");	
	$struid = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "system";
				
	$ret = false;		
		
	$MDL = new UserManager();
	$ret = $MDL->assignRole($_POST["user_id"], $_POST["role_name"], $struid, $strdate);			
	
	if ($ret) { 
		echo "Success";
	}
	else {
		echo $MDL->getError();
	}
			
?>