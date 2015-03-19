<?php

	require_once(dirname(__FILE__) . "/../model/RoleManager.php");
	
	// set the default timezone to use. Available since PHP 5.1
	//date_default_timezone_set('Asia/Jakarta');		
	
	$msg = "";
	
	$MDL = new RoleManager();
	$data = $MDL->loadDetailByName($_POST["role_name"]);			
	
	if (!empty($data)) {
		$cnt = 0;	
		foreach ($data as $v) {
			$cnt++;												
			$msg .= "<tr>" .
						"<td>" .
							"&nbsp;&nbsp;" . $cnt . ". &nbsp;&nbsp;" . $v["title"] . 
						"</td>" .
					"</tr>";				
		}
	}
	else {
		$msg = "<tr>" .
					"<td>No data found.</td>" .
				"</tr>";
	}
	
	echo $msg;
?>