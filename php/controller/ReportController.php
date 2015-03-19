<?php
/**
 * ReportController
 * File   : ReportController.php
 * author : Jerry Sihombing
 * desc   : application controller
 * date	  : 2015-02-24
 */

require_once (dirname(__FILE__) . "/../model/Map.php");
require_once (dirname(__FILE__) . "/../model/StoreMap.php");
require_once (dirname(__FILE__) . "/../model/Sales.php");
require_once (dirname(__FILE__) . "/../model/Status.php");

class ReportController extends GlobalController {

	function __construct() {

	}
	
	public function controll () {
		$ac = $this->getCurrAct("ac");
		$a_auth = $this->getCurrAuth();
		$user_id = $this->getCurrUserId();
		$user_name = $this->getCurrUserName();
		$user_name = empty($user_name) ? $user_name : $user_id;
		$role = $this->getSessionItem("role");
		$branch_code = $this->getSessionItem("branch_code");
		
		switch ($ac) {			
			
			case "":
			case "list":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 711);
				
				$MP = new Map();
				if ($user_id == "admin") {
					$mapData = $MP->loadAll();
				}
				else {
					$mapData = $MP->loadByStore($branch_code);
				}
				
				$optMap = "";
				foreach ($mapData as $map) {
					$optMap .= "<option value='". $map["id"] . "'>" . $map["code"] . " (" . $map["name"] . ")" . "</option>";
				}
				
				include_once("html/report_list.html");
				
				break;
			
			case "view":
				$this->checkFirst();
				$this->isOperAllowable($a_auth, 711);
				
				$cmd = $this->getItem("cmd");
				$type = $this->getItem("type");
				$articleType = ($type == "obral" ? 1 : ($type == "normal" ? 0 : -1));
				$id = $this->getItem("id");
				
				$MDL = new Map();
				$MDL->load($id);
				
				$MDL2 = new StoreMap();
				$data = $MDL2->loadByMapId($id);
				
				$MDL3 = new Sales();
				$MDL4 = new Status();
				
				$hostname = $_SERVER["HTTP_HOST"];
				
				include_once("html/report_view.html");
				
				break;
			
			case "info":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 711);
				
				$cmd = $this->getItem("cmd");
				$type = $this->getItem("type");
				$articleType = ($type == "obral" ? 1 : ($type == "normal" ? 0 : -1));
				$brand = $this->getItem("brand");
				$id = $this->getItem("id");
				
				# repalce created "~" char
				$brand = str_replace("~", " ", $brand);
				
				$MDL = new Sales();
				if ($articleType == -1) {
					$value = $MDL->findAmountByBrandMap($brand, $id);
				}
				else {
					$value = $MDL->findAmountByBrandTypeMap($brand, $articleType, $id);
				}
				
				include_once("html/area_info.html");
				
				break;
			
			default:

				break;
		}

	}

}
?>