<?php
/**
 * StatusController
 * File   : StatusController.php
 * author : Jerry Sihombing
 * desc   : application controller
 * date	  : 2015-01-30
 */

require_once (dirname(__FILE__) . "/../model/Status.php");

class StatusController extends GlobalController {

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
				$this->isOperAllowable($a_auth, 131);
				$createAllowable = $this->isAllow($a_auth, 132);
				
				include_once("html/status_list.html");
				
				break;
			
			case "add":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 132);
				
				include_once("html/status_add.html");
				
				break;
			
			case "edit":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 133);
				
				$id = $this->getItem("id");
				$MDL = new Status();
				$MDL->load($id);
				
				include_once("html/status_edit.html");
				
				break;

			default:

				break;
		}

	}

}
?>