<?php	
	
	require_once(dirname(__FILE__) . "/../model/Sales.php");
	
	$ret = false;
	
	$MDL = new Sales();	
		
	$ret = $MDL->remove($_POST["id"]);
	
	if ($ret) { 
		echo "Success";
	}
	else {
		echo $MDL->getError();
	}	
	
?>