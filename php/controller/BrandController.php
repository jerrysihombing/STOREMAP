<?php
/**
 * BrandController
 * File   : BrandController.php
 * author : Jerry Sihombing
 * desc   : application controller
 * date	  : 2015-02-18
 */

require_once (dirname(__FILE__) . "/../model/Brand.php");
require_once (dirname(__FILE__) . "/../model/Division.php");

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
		
		$DV = new Division();
		$divisionData = $DV->loadAll();
				
		switch ($ac) {			
			
			case "":
			case "list":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 151);
				$createAllowable = $this->isAllow($a_auth, 152);
				$uploadAllowable = $this->isAllow($a_auth, 155);
				
				$optDivision = "";
				foreach ($divisionData as $divisions) {
					$optDivision .= "<option values='" . $divisions["name"] . "'>" . $divisions["name"] . "</option>";	
				}
				
				include_once("html/brand_list.html");
				
				break;
			
			case "add":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 152);
				
				$optDivision = "";
				foreach ($divisionData as $divisions) {
					$optDivision .= "<option values='" . $divisions["name"] . "'>" . $divisions["name"] . "</option>";	
				}
				
				include_once("html/brand_add.html");
				
				break;
			
			case "upload":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 155);
		
				include_once("html/brand_upload.html");
				
				break;
			
			case "edit":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 153);
				
				$id = $this->getItem("id");
				$MDL = new Brand();
				$MDL->load($id);
				
				include_once("html/brand_edit.html");
				
				break;
			
			case "dwform-brand":
				include_once(dirname(__FILE__) . "/../exe/dw_form.php");
				exit;
				
				break;
			
			case "dwlog":
				$id = $this->getItem("id");
				$id = str_replace("-", "/", $id);
				
				include_once(dirname(__FILE__) . "/../exe/dw_form.php");
				exit;
				
				break;

			default:

				break;
		}

	}

}
?>