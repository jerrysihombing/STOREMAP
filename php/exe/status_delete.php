<?php	
	
	require_once(dirname(__FILE__) . "/../model/Status.php");
	
	$ret = false;
	
	$MDL = new Status();	
		
	$ret = $MDL->remove($_POST["id"]);
	
	if ($ret) { 
		echo "Success";
	}
	else {
		echo $MDL->getError();
	}	
	
?>