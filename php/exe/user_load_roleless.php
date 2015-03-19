<?php

	require_once(dirname(__FILE__) . "/../model/UserManager.php");
	
	// set the default timezone to use. Available since PHP 5.1
	//date_default_timezone_set('Asia/Jakarta');		
	
	$msg = "";
	
	$MDL = new UserManager();					
	$data = $MDL->loadByRoleName("N/A");
	
	if (!empty($data)) {
		$msg .= '{';
		while(list($k, $v) = each($data)) {		
			$msg .= '"' . $v["id"] . '" : "' . $v["user_id"] . '",';				
		}
		$msg = substr($msg, 0, strlen($msg) - 1);	
		$msg .= '}';
	}
	else {
		# do nothing
	}
	
	echo $msg;
?>