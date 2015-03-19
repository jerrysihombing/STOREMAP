<?php	
	
	require_once(dirname(__FILE__) . "/../model/StoreMap.php");
	
	$ret = false;
	
	$MDL = new StoreMap();	
		
	$ret = $MDL->remove($_POST["id"]);
	
	if ($ret) { 
		echo "Success";
	}
	else {
		echo $MDL->getError();
	}	
	
?>