<?php	
	
	require_once(dirname(__FILE__) . "/../model/StoreMap.php");
	
	$MDL = new StoreMap();	
	$data = $MDL->loadByMapCode($_POST["mapCode"]);
	
	$optCoordinate = "<option value=''>Select Section</option>";
	foreach($data as $v) {
		$optCoordinate .= "<option value='" . $v["code"] . "'>" . $v["name"] . "</option>";	
	}
	
	echo $optCoordinate;
?>