<?php
/**
 * BrandController
 * File   : BrandController.php
 * author : Jerry Sihombing
 * desc   : application controller
 * date	  : 2015-02-18
 */

require_once (dirname(__FILE__) . "/../model/Brand.php");

class BrandController extends GlobalController {

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
				$this->isOperAllowable($a_auth, 151);
				$createAllowable = $this->isAllow($a_auth, 152);
				
				include_once("html/brand_list.html");
				
				break;
			
			case "add":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 152);
				
				include_once("html/brand_add.html");
				
				break;
			
			case "edit":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 153);
				
				$id = $this->getItem("id");
				$MDL = new Brand();
				$MDL->load($id);
				
				include_once("html/brand_edit.html");
				
				break;

			default:

				break;
		}

	}

}
?>