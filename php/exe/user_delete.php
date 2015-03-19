<?php

	require_once(dirname(__FILE__) . "/../model/UserManager.php");
	
	// set the default timezone to use. Available since PHP 5.1
	//date_default_timezone_set('Asia/Jakarta');		
	
	$ret = false;				
	$MDL = new UserManager();
					
	$ret = $MDL->remove($_POST["id"]);

	if ($ret) { 
		echo "Success";
	}
	else {
		echo $MDL->getError();
	}
		
?>