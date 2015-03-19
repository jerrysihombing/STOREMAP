<?php	
	
	require_once(dirname(__FILE__) . "/../model/Brand.php");
	
	$ret = false;
	
	$MDL = new Brand();	
		
	$ret = $MDL->remove($_POST["id"]);
	
	if ($ret) { 
		echo "Success";
	}
	else {
		echo $MDL->getError();
	}	
	
?>