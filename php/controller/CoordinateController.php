<?php
/**
 * CoordinateController
 * File   : CoordinateController.php
 * author : Jerry Sihombing
 * desc   : application controller
 * date	  : 2015-01-28
 */

require_once (dirname(__FILE__) . "/../model/Brand.php");
require_once (dirname(__FILE__) . "/../model/Map.php");
require_once (dirname(__FILE__) . "/../model/StoreMap.php");
require_once (dirname(__FILE__) . "/../model/Data.php");
require_once (dirname(__FILE__) . "/../model/Division.php");

class CoordinateController extends GlobalController {

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
				$this->isOperAllowable($a_auth, 121);
				$createAllowable = $this->isAllow($a_auth, 122);
				
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
				
				include_once("html/coordinate_list.html");
				
				break;
			
			case "view":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 125);
				
				$id = $this->getItem("id");
				$MDL = new StoreMap();
				$MDL->load($id);
				
				$sInfo = "";
				$shape = $MDL->getShape();
				switch($shape) {
					case "rect":
						$sInfo = "Name: " . $MDL->getName() . "<br>";
						$sInfo .= "Type: Rectangular<br>";
						$sInfo .= "Top Left: " . $MDL->getTopLeft() . "<br>";
						$sInfo .= "Bottom Right: " . $MDL->getBottomRight() . "<br>";
						break;
					case "poly":
						$sInfo = "Name: " . $MDL->getName() . "<br>";
						$sInfo .= "Type: Polygon<br>";
						$sInfo .= "Coordinates: " . $MDL->getCoordinate() . "<br>";
						break;
					case "circle":
						$sInfo = "Name: " . $MDL->getName() . "<br>";
						$sInfo .= "Type: Circle<br>";
						$sInfo .= "Radius: " . $MDL->getRadius() . "<br>";
						$sInfo .= "Center: " . $MDL->getCenter() . "<br>";
						break;
				}
				
				include_once("html/coordinate_view.html");
				
				break;
			
			case "add":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 122);
				
				$BR = new Brand();
				$brandData = $BR->loadDistinctName();
				$optBrand = "";
				foreach ($brandData as $brands) {
					$optBrand .= "<option values='" . $brands["name"] . "'>" . $brands["name"] . "</option>";	
				}
				
				$MDL = new Map();
				if ($user_id == "admin") {
					$data = $MDL->loadAll();
				}
				else {
					$data = $MDL->loadByStore($branch_code);
				}
				
				$optMapCode = "";
				foreach($data as $v) {
					$optMapCode .= "<option value='" . $v["id"] . "#" . $v["code"] . "'>" . $v["name"] . "</option>";
				}
				
				$optDivision = "";
				foreach ($divisionData as $divisions) {
					$optDivision .= "<option values='" . $divisions["name"] . "'>" . $divisions["name"] . "</option>";	
				}
				
				include_once("html/coordinate_add.html");
				
				break;
			
			case "edit":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 123);
				
				$id = $this->getItem("id");
				$MDL = new StoreMap();
				$MDL->load($id);
				$mapCode = $MDL->getMapCode();
				$shape = $MDL->getShape();
				$brandName = $MDL->getBrandName();
				
				$BR = new Brand();
				$brandData = $BR->loadDistinctName();
				$optBrand = "";
				foreach ($brandData as $brands) {
					$sel = ($brands["name"] == $brandName ? "selected='selected'" : "");
					$optBrand .= "<option values='" . $brands["name"] . "' " . $sel . ">" . $brands["name"] . "</option>";	
				}
				
				$MDL2 = new Map();
				if ($user_id == "admin") {
					$data = $MDL2->loadAll();
				}
				else {
					$data = $MDL2->loadByStore($branch_code);
				}
				
				$optMapCode = "";
				foreach($data as $v) {
					$sel = ($mapCode == $v["code"] ? " selected='selected' " : "");
					$optMapCode .= "<option value='" . $v["id"] . "#" . $v["code"] . "' " . $sel . ">" . $v["name"] . "</option>";
				}
				
				$optDivision = "";
				foreach ($divisionData as $divisions) {
					$sel = ($MDL->getDivision() == $divisions["name"] ? "selected='selected'" : "");
					$optDivision .= "<option values='" . $divisions["name"] . "' " . $sel . ">" . $divisions["name"] . "</option>";	
				}
				
				include_once("html/coordinate_edit.html");
				
				break;
			
			case "map":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 122);
				
				$id = $this->getItem("id");
				
				$MDL = new StoreMap();
				$data = $MDL->loadByMapId($id);
				
				#print_r($data);
				
				$MDL2 = new Map();
				$MDL2->load($id);
				
				include_once("html/coordinate_map_view.html");
				
				break;
			
			/*
			case "info":
				$this->checkFirst();			
				$this->isOperAllowable($a_auth, 115);
				
				$id = $this->getItem("id");
				
				$MDL = new Data();
				$value = $MDL->findLastByStoremap($id, "Sales");
				
				include_once("html/area_info.html");
				
				break;
			*/
			
			default:

				break;
		}

	}

}
?>