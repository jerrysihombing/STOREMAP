<?php	
	
	require_once(dirname(__FILE__) . "/../model/Article.php");
	
	$ret = false;
	
	$MDL = new Article();	
		
	$ret = $MDL->remove($_POST["id"]);
	
	if ($ret) { 
		echo "Success";
	}
	else {
		echo $MDL->getError();
	}	
	
?>