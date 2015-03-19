<?php	
	
	require_once(dirname(__FILE__) . "/../model/Data.php");
	
	$ret = false;
	
	$MDL = new Data();	
		
	$ret = $MDL->remove($_POST["id"]);
	
	if ($ret) { 
		echo "Success";
	}
	else {
		echo $MDL->getError();
	}	
	
?>