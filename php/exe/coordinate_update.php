<?php	
	
	@session_start();
	
	require_once(dirname(__FILE__) . "/../model/StoreMap.php");
	
	// set the default timezone to use. Available since PHP 5.1
	date_default_timezone_set('Asia/Jakarta');	
	$strdate = date("Y-m-d H:i:s");	
	$struid = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "system";
	
	$ret = false;
	
	$MDL = new StoreMap();	
	
	if ($MDL->isExist($_POST["code"])) {
		$MDL->setCode($_POST["code"]);
		$MDL->setName($_POST["name"]);
		$MDL->setDescription($_POST["description"]);
		$MDL->setBrandName($_POST["brandName"]);
		$MDL->setInitColor("FAFAD7"); # default
		$MDL->setMapCode($_POST["mapCode"]);
		
		$shape = $_POST["shape"];
		$coordinate = $_POST["coordinate"];
		$topLeft = $_POST["topLeft"];
		$bottomRight = $_POST["bottomRight"];
		$center = $_POST["center"];
		$radius = $_POST["radius"];
		
		if ($shape == "rect") {
			$coordinate = "";
			$center = "";
			$radius = "";
		}
		else if ($shape == "circle") {
			$coordinate = "";
			$topLeft = "";
			$bottomRight = "";
		}
		else if ($shape == "poly") {
			$center = "";
			$radius = "";
			$topLeft = "";
			$bottomRight = "";
		}
		
		$MDL->setShape($shape);
		$MDL->setCoordinate($coordinate);
		$MDL->setTopLeft($topLeft);
		$MDL->setBottomRight($bottomRight);
		$MDL->setCenter($center);
		$MDL->setRadius($radius);
		
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
		echo "CODE not found.";
	}
	
?>