<?php

	require_once(dirname(__FILE__) . "/../model/RoleManager.php");
	
	// set the default timezone to use. Available since PHP 5.1
	//date_default_timezone_set('Asia/Jakarta');		
	
	$msg = "";
	
	$MDL = new RoleManager();
	$data = $MDL->loadDetail($_POST["id_role"]);			
	
	if (!empty($data)) {		
		$total = sizeof($data);
		$cnt = 0;		
		$msg .= "<tr>";
		$msg .= 	"<td style=\"border-bottom:none;\" align=\"left\" valign=\"top\">";
				foreach ($data as $v) {									
					$cnt++;	
					if ($cnt != $total)	{
						$space = "&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;";
					}
					else {
						$space = "";
					}
					$msg .=	$cnt . ".&nbsp;" . $v["title"] . $space;
				}
		$msg .=		"</td>";
		$msg .=	"</tr>";		

	}
	else {		
		$msg .= "<tr>" . 
					"<td style=\"border-bottom:none;\" align=\"left\" valign=\"top\">&nbsp;</td>" . 					
				"</tr>";		
	}
	
	echo $msg;
?>