<?php
/**
 * Reportv2Controller
 * File   : Reportv2Controller.php
 * author : Jerry Sihombing
 * desc   : application controller
 * date	  : 2015-05-04
 */

require_once (dirname(__FILE__) . "/../model/Map.php");
require_once (dirname(__FILE__) . "/../model/StoreMap.php");
require_once (dirname(__FILE__) . "/../model/Sales.php");
require_once (dirname(__FILE__) . "/../model/Status.php");
require_once (dirname(__FILE__) . "/../model/Site.php");

class Reportv2Controller extends GlobalController {

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
				
				$today = date("d-m-Y");
				
				include_once("html/reportv2_list.html");
				
				break;
			
			case "view":
				$this->checkFirst();
				$this->isOperAllowable($a_auth, 711);
				
				$cmd = $this->getItem("cmd");
				$type = $this->getItem("type");
				$articleType = ($type == "obral" ? 1 : ($type == "normal" ? 0 : -1));
				# for ReportController brand & division are aliases for startDate & endDate
				$startDate = $this->getItem("brand");
				$endDate = $this->getItem("division");
				if ($endDate == "-") $endDate = "";
				
				$startDateEn = substr($startDate, 6, 4) . "-" . substr($startDate, 3, 2) . "-" . substr($startDate, 0, 2);
				if ($endDate != "") {
					$endDateEn = substr($endDate, 6, 4) . "-" . substr($endDate, 3, 2) . "-" . substr($endDate, 0, 2);	
				}
				else {
					$endDateEn = "0000-00-00";
				}
				
				# check periodical date
				$daysCount = 0;
				if (($startDateEn != "0000-00-00") && ($endDateEn != "0000-00-00")) {
					$date1 = new DateTime($startDateEn);
					$date2 = new DateTime($endDateEn);
					$interval = $date1->diff($date2, true); # absolute value
					$daysCount = intval($interval->format("%a")) + 1;
				}
				
				$id = $this->getItem("id");
				
				$MDL = new Map();
				$MDL->load($id);
				$storeInit = $MDL->getStoreInit();
				
				$ST = new Site();
				$storeCode = $ST->storeGetCode($storeInit);
				
				$MDL2 = new StoreMap();
				$data = $MDL2->loadByMapId($id);
				$MDL3 = new Sales();
				$MDL4 = new Status();
				
				$hostname = $_SERVER["HTTP_HOST"];
				
				include_once("html/reportv2_view.html");
				
				break;
			
			case "info":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 711);
				
				$cmd = $this->getItem("cmd");
				$type = $this->getItem("type");
				$articleType = ($type == "obral" ? 1 : ($type == "normal" ? 0 : -1));
				$brand = $this->getItem("brand");
				$division = $this->getItem("division");
				$wide = $this->getItem("wide");
				$sDate = $this->getItem("sDate");
				$aDate = explode("~", $sDate);
				$startDateEn = (isset($aDate[0]) ? $aDate[0] : "0000-00-00");
				$endDateEn = (isset($aDate[1]) ? $aDate[1] : "0000-00-00");
				$id = $this->getItem("id");
				
				# check periodical date
				$daysCount = 0;
				if (($startDateEn != "0000-00-00") && ($endDateEn != "0000-00-00")) {
					$date1 = new DateTime($startDateEn);
					$date2 = new DateTime($endDateEn);
					$interval = $date1->diff($date2, true); # absolute value
					$daysCount = intval($interval->format("%a")) + 1;
				}
				
				# replace created "~" char
				$brand = str_replace("~", " ", $brand);
				$division = str_replace("~", " ", $division);
				
				$MDL = new Map();
				$MDL->load($id);
				$storeInit = $MDL->getStoreInit();
				
				$ST = new Site();
				$storeCode = $ST->storeGetCode($storeInit);
				
				$MDL = new Sales();
				/*
				if ($articleType == -1) {
					$value = $MDL->findAmount($brand, $division, $startDateEn, $endDateEn, $storeCode);
				}
				else {
					$value = $MDL->findAmountByType($brand, $division, $startDateEn, $endDateEn, $storeCode, $articleType);
				}
				*/
				$value = $MDL->findAmountPerBrand($brand, $division, $startDateEn, $endDateEn, $storeCode, $articleType);
				if ($cmd == "sales-per-square") {
					if ($wide != 0) {
						if ($daysCount) {
							$value = ($value / $daysCount) / $wide;
						}
						else {
							$value = $value / $wide;	
						}
					}
					else {
						$value = 0;
					}
				}
				
				include_once("html/areav2_info.html");
				
				break;
			
			default:

				break;
		}

	}

}
?>