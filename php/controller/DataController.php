<?php
/**
 * DataController
 * File   : DataController.php
 * author : Jerry Sihombing
 * desc   : application controller
 * date	  : 2015-01-30
 */

require_once (dirname(__FILE__) . "/../model/Map.php");
require_once (dirname(__FILE__) . "/../model/StoreMap.php");
require_once (dirname(__FILE__) . "/../model/Data.php");

class DataController extends GlobalController {

	function __construct() {

	}
	
	public function controll () {
		$ac = $this->getCurrAct("ac");
		$a_auth = $this->getCurrAuth();
		$user_id = $this->getCurrUserId();
		$user_name = $this->getCurrUserName();
		$user_name = empty($user_name) ? $user_id : $user_name;
		$role = $this->getSessionItem("role");			
		
		switch ($ac) {			
			
			case "":
			case "list":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 141);
				$createAllowable = $this->isAllow($a_auth, 142);
				
				include_once("html/data_list.html");
				
				break;
			
			case "add":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 142);
				
				$MDL = new Map();
				$data = $MDL->loadAll();
				
				$optMapCode = "";
				foreach($data as $v) {
					$optMapCode .= "<option value='" . $v["code"] . "'>" . $v["name"] . "</option>";
				}
				
				$optYear = "";
				$year = date("Y");
				for ($i = $year; $i >= $year-4; $i--) {
					$optYear .= "<option value='" . $i . "'>" . $i . "</option>";
				}
				
				include_once("html/data_add.html");
				
				break;
			
			case "edit":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 143);
				
				$id = $this->getItem("id");
				$MDL = new Data();
				$MDL->load($id);
				
				include_once("html/data_edit.html");
				
				break;

			default:

				break;
		}

	}

}
?>