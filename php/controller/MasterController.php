<?php
/**
 * MasterController
 * File   : MasterController.php
 * author : Jerry Ch.
 * desc   : application controller
 * date	  : 2013-07-01
 */

require_once (dirname(__FILE__) . "/../model/StoreMap.php");

class MasterController extends GlobalController {

	function __construct() {

	}
	
	public function controll () {
		$ac = $this->getItem("ac");
		$a_auth = $this->getCurrAuth();
		$user_id = $this->getCurrUserId();
		$user_name = $this->getCurrUserName();
		$user_name = empty($user_name) ? $user_id : $user_name;
		$role = $this->getSessionItem("role");			
		
		switch ($ac) {			
			
			case "":
			case "index":
				#$this->checkFirst();			
				#$this->isOperAllowable($a_auth, 111);
				
				include_once("html/master.html");
				
				break;
			
			case "add":
				#$this->checkFirst();			
				#$this->isOperAllowable($a_auth, 111);
				
				include_once("html/master_add.html");
				
				break;

			default:

				break;
		}

	}

}
?>