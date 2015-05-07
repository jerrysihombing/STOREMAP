<?php
/**
 * SalesController
 * File   : SalesController.php
 * author : Jerry Sihombing
 * desc   : application controller
 * date	  : 2015-02-23
 */

require_once (dirname(__FILE__) . "/../model/Brand.php");
require_once (dirname(__FILE__) . "/../model/Sales.php");
require_once (dirname(__FILE__) . "/../model/Division.php");

class SalesController extends GlobalController {

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
		$store_code = $this->getSessionItem("store_code");
		
		$DV = new Division();
		$divisionData = $DV->loadAll();
			
		switch ($ac) {			
			
			case "":
			case "list":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 141);
				$createAllowable = $this->isAllow($a_auth, 142);
				$uploadAllowable = $this->isAllow($a_auth, 145);
				
				$this->setSessionItem("salesListInit", 1);
				
				$BR = new Brand();
				$brandData = $BR->loadDistinctName();
				$optBrand = "";
				foreach ($brandData as $brands) {
					$optBrand .= "<option values='" . $brands["name"] . "'>" . $brands["name"] . "</option>";	
				}
				
				$optDivision = "";
				foreach ($divisionData as $divisions) {
					$optDivision .= "<option values='" . $divisions["name"] . "'>" . $divisions["name"] . "</option>";	
				}
				
				$today = date("d-m-Y");
				
				include_once("html/sales_list.html");
				
				break;
			
			case "add":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 142);
				
				$BR = new Brand();
				$brandData = $BR->loadDistinctName();
				$optBrand = "";
				foreach ($brandData as $brands) {
					$optBrand .= "<option values='" . $brands["name"] . "'>" . $brands["name"] . "</option>";	
				}
				
				$optDivision = "";
				foreach ($divisionData as $divisions) {
					$optDivision .= "<option values='" . $divisions["name"] . "'>" . $divisions["name"] . "</option>";	
				}
				
				include_once("html/sales_add.html");
				
				break;
			
			case "upload":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 145);
		
				include_once("html/sales_upload.html");
				
				break;
			
			case "edit":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 143);
				
				$id = $this->getItem("id");
				$MDL = new Sales();
				$MDL->load($id);
				
				include_once("html/sales_edit.html");
				
				break;
			
			case "dwform-sales":
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