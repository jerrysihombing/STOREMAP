<?php
/**
 * ArticleController
 * File   : ArticleController.php
 * author : Jerry Sihombing
 * desc   : application controller
 * date	  : 2015-02-18
 */
 
require_once (dirname(__FILE__) . "/../model/Brand.php");
require_once (dirname(__FILE__) . "/../model/Article.php");
require_once (dirname(__FILE__) . "/../model/Division.php");

class ArticleController extends GlobalController {

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
		
		$BR = new Brand();
		$data = $BR->loadDistinctName();
		
		$DV = new Division();
		$divisionData = $DV->loadAll();
		
		switch ($ac) {			
			
			case "":
			case "list":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 161);
				$createAllowable = $this->isAllow($a_auth, 162);
				$uploadAllowable = $this->isAllow($a_auth, 165);
				
				$this->setSessionItem("articleListInit", 1);
				
				$optBrand = "";
				foreach ($data as $brands) {
					$optBrand .= "<option values='" . $brands["name"] . "'>" . $brands["name"] . "</option>";	
				}
				
				$optDivision = "";
				foreach ($divisionData as $divisions) {
					$optDivision .= "<option values='" . $divisions["name"] . "'>" . $divisions["name"] . "</option>";	
				}
				
				include_once("html/article_list.html");
				
				break;
			
			case "add":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 162);
		
				$optBrand = "";
				foreach ($data as $brands) {
					$optBrand .= "<option values='" . $brands["name"] . "'>" . $brands["name"] . "</option>";	
				}		
				
				$optDivision = "";
				foreach ($divisionData as $divisions) {
					$optDivision .= "<option values='" . $divisions["name"] . "'>" . $divisions["name"] . "</option>";	
				}
				
				include_once("html/article_add.html");
				
				break;
			
			case "upload":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 165);
		
				include_once("html/article_upload.html");
				
				break;
			
			case "edit":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 163);
				
				$id = $this->getItem("id");
				$MDL = new Article();
				$MDL->load($id);
				
				$optBrand = "";
				foreach ($data as $brands) {
					$sel = ($MDL->getBrandName() == $brands["name"] ? "selected='selected'" : "");
					$optBrand .= "<option values='" . $brands["name"] . "' " . $sel . ">" . $brands["name"] . "</option>";	
				}
				
				$optDivision = "";
				foreach ($divisionData as $divisions) {
					$sel = ($MDL->getDivision() == $divisions["name"] ? "selected='selected'" : "");
					$optDivision .= "<option values='" . $divisions["name"] . "' " . $sel . ">" . $divisions["name"] . "</option>";	
				}
				
				include_once("html/article_edit.html");
				
				break;
			
			case "dwform":
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