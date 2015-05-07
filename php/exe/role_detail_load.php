<?php

	require_once(dirname(__FILE__) . "/../model/RoleManager.php");
	require_once(dirname(__FILE__) . "/../model/MenuManager.php");
	
	// set the default timezone to use. Available since PHP 5.1
	//date_default_timezone_set('Asia/Jakarta');		
	
	$msg = "";
	
	$MM = new MenuManager();
	$menu_data = $MM->loadAll();		
	$MDL = new RoleManager();
	$data = $MDL->loadDetail($_POST["id_role"]);			
	
	if (!empty($menu_data)) {		
		$cnt = sizeof($menu_data);
		$cnt1 = ($cnt > 12) ? 12 : $cnt;
		$cnt2 = ($cnt > 12) ? $cnt : 0;
		
		$no = 0;
							
		$msg .= "<tr>";
		$msg .= 	"<td style=\"border-bottom:none;\" align=\"left\" valign=\"top\">";					
						for ($i = 0; $i < $cnt1; $i++) {
							$no++;
							$check = "";
							while(list($key, $value) = each($data)) {
								if ($value["id_menu"] == $menu_data[$i]["id_menu"]) {
									$check = "checked=\"checked\"";
								}
							}
							reset($data);
		$msg .= 			"<input class=\"menu_item_e\" value=\"" . $menu_data[$i]["id_menu"] . "\" type=\"checkbox\" " . $check . " name=\"menu_e-" . $no . "\" id=\"menu_e-" . $no . "\" />&nbsp;<label for=\"menu_e-" . $no . "\">" .  $menu_data[$i]["title"] . "</label><br />";
						}												
		$msg .= 	"</td>";
		$msg .= 	"<td style=\"border-bottom:none;\" align=\"left\" valign=\"top\">";					
						for ($i = $cnt1; $i < $cnt2; $i++) {
							$no++;
							$check = "";
							while(list($key, $value) = each($data)) {
								if ($value["id_menu"] == $menu_data[$i]["id_menu"]) {
									$check = "checked=\"checked\"";
								}
							}
							reset($data);
		$msg .= 			"<input class=\"menu_item_e\" value=\"" . $menu_data[$i]["id_menu"] . "\" type=\"checkbox\" " . $check . " name=\"menu_e-" . $no . "\" id=\"menu_e-" . $no . "\" />&nbsp;<label for=\"menu_e-" . $no . "\">" .  $menu_data[$i]["title"] . "</label><br />";
						}
		$msg .= 		"&nbsp;";									
		$msg .= 	"</td>";									
		$msg .= "</tr>";							
		
	}
	else {		
		$msg .= "<tr>" . 
				"<td style=\"border-bottom:none;\" align=\"left\" valign=\"top\">&nbsp;</td>" . 
			"</tr>";		
	}
	
	echo $msg;
?>