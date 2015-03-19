<?php
/**
 * MapController
 * File   : MapController.php
 * author : Jerry Sihombing
 * desc   : application controller
 * date	  : 2015-01-27
 */

require_once (dirname(__FILE__) . "/../model/Map.php");
require_once (dirname(__FILE__) . "/../model/StoreMap.php");
require_once (dirname(__FILE__) . "/../model/Status.php");
require_once (dirname(__FILE__) . "/../model/Data.php");

class MapController extends GlobalController {

	function __construct() {

	}
	
	public function controll () {
		$ac = $this->getCurrAct("ac");
		$a_auth = $this->getCurrAuth();
		$user_id = $this->getCurrUserId();
		$user_name = $this->getCurrUserName();
		$user_name = empty($user_name) ? $user_id : $user_name;
		$role = $this->getSessionItem("role");
		$branch_code = $this->getSessionItem("branch_code");
		
		switch ($ac) {			
			
			case "":
			case "list":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 111);
				$createAllowable = $this->isAllow($a_auth, 112);
				
				include_once("html/map_list.html");
				
				break;
			
			case "view":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 115);
				
				$id = $this->getItem("id");
				$MDL = new Map();
				$MDL->load($id);
				
				include_once("html/map_view.html");
				
				break;
			
			case "add":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 112);
				
				include_once("html/map_add.html");
				
				break;
			
			case "edit":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 113);
				
				$id = $this->getItem("id");
				$MDL = new Map();
				$MDL->load($id);
				
				include_once("html/map_edit.html");
				
				break;

			default:

				break;
		}

	}

}
?>