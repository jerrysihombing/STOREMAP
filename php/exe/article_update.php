<?php	
	
	@session_start();
	
	require_once(dirname(__FILE__) . "/../model/Article.php");
	
	// set the default timezone to use. Available since PHP 5.1
	date_default_timezone_set('Asia/Jakarta');	
	$strdate = date("Y-m-d H:i:s");	
	$struid = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "system";
	
	$ret = false;
	
	$MDL = new Article();	
	
	if ($MDL->isExist($_POST["plu8"])) {
		$MDL->setPlu8($_POST["plu8"]);
		$MDL->setArticleType($_POST["articleType"]);
		$MDL->setArticleCode($_POST["articleCode"]);
		$MDL->setDescription($_POST["description"]);
		$MDL->setBrandName($_POST["brandName"]);
		$MDL->setDivision($_POST["division"]);
		$MDL->setStoreInit($_POST["storeInit"]);
		$MDL->setLastUser($struid);
		$MDL->setLastUpdate($strdate);
		
		$ret = $MDL->update($_POST["id"]);
		
		if ($ret) { 
			echo "Success";
		}
		else {
			echo $MDL->getError();
		}	
	}
	else {
		echo "ARTICLE not found.";
	}
	
?>