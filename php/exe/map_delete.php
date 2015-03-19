<?php	
	
	require_once(dirname(__FILE__) . "/../model/Map.php");
	
	$ret = false;
	
	$MDL = new Map();	
		
	$ret = $MDL->remove($_POST["id"]);
	
	if ($ret) { 
		echo "Success";
	}
	else {
		echo $MDL->getError();
	}	
	
?>