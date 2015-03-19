<?php	
	
	@session_start();
	
	require_once(dirname(__FILE__) . "/../model/Sales.php");
	
	// set the default timezone to use. Available since PHP 5.1
	date_default_timezone_set('Asia/Jakarta');	
	$strdate = date("Y-m-d H:i:s");	
	$struid = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "system";
	
	$ret = false;
	
	$MDL = new Sales();	
	
	if (!$MDL->isExist($_POST["transDate"], $_POST["brandName"], $_POST["articleType"], $_POST["storeInit"])) {
		$MDL->setTransDate(substr($_POST["transDate"], 6) . "-" . substr($_POST["transDate"], 3, 2) . "-" . substr($_POST["transDate"], 0, 2));
		$MDL->setBrandName($_POST["brandName"]);
		$MDL->setArticleType($_POST["articleType"]);
		$MDL->setQuantity((is_numeric($_POST["quantity"]) ? $_POST["quantity"] : 0));
		$MDL->setAmount((is_numeric($_POST["amount"]) ? $_POST["amount"] : 0));
		$MDL->setStoreInit($_POST["storeInit"]);
		$MDL->setCreatedBy($struid);
		$MDL->setCreatedDate($strdate);
		
		$ret = $MDL->addNew();
		
		if ($ret) { 
			echo "Success";
		}
		else {
			echo $MDL->getError();
		}	
	}
	else {
		echo "SALES already exist.";
	}
	
?>