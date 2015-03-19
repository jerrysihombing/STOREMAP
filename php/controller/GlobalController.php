<?php

/** 
 * GlobalController
 * File   : GlobalController.php 
 * author : Jerry Sihombing 
 * desc   : controll application globaly 
 * date	  : 2006-12-09
 */
 
class GlobalController {
	public function __construct() {		
		session_start();
	}
	
	protected function checkFirst() {		
		if ($this->getCurrUserId() == "") {
			header("Location: /login.html");
			//header("Location: http://" . $_SERVER["HTTP_HOST"] . "/login.html");
			exit;
		}
	}

	protected function isOperAllowable($a_auth, $id_menu) {		
		$allow = false;		
		foreach($a_auth as $arr) {			
			if ($id_menu == $arr["id_menu"]) {				
				$allow = true;
				break;
			}
		}
		if (!$allow) {
			header("Location: /unauthorized.html");
			#header("Location: http://" . $_SERVER["HTTP_HOST"] . "/unauthorized.html");
			exit;
		}		
	}				
	
	protected function isAllow($a_auth, $id_menu) {				
		$allow = false;		
		foreach($a_auth as $arr) {			
			if ($id_menu == $arr["id_menu"]) {				
				$allow = true;
				break;
			}
		}
		return $allow;
	}
	
	protected function unsetSessionItem($key) {
		if (isset($_SESSION[$key])) {
			unset($_SESSION[$key]);
		}
	}
	
	protected function setSessionItem($key, $val) {
		$_SESSION[$key] = $val;
	}
	
	protected function getSessionItem($key) {
		if (isset($_SESSION[$key])) {
			return $_SESSION[$key];
		}
		return;
	}
			
	protected function getCurrUserId() {
		if (isset($_SESSION["user_id"])) {
			return $_SESSION["user_id"];
		} 
		return "";
	}
	
	protected function getCurrUserName() {		
		if (isset($_SESSION["user_name"])) {
			return $_SESSION["user_name"];
		} 
		return "";
	}
	
	protected function getCurrAuth() {		
		if (isset($_SESSION["a_auth"])) {
			return $_SESSION["a_auth"];
		} 
		return array();
	}
		
	protected function getCurrOper() {
		if (isset($_GET["op"])) {
			return $_GET["op"];
		} 
		return;	
	}
	
	protected function getCurrAct() {
		if (isset($_GET["ac"])) {
			return $_GET["ac"];
		}
		return;
	}
	
	protected function getItem($itm) {
		if (isset($_GET[$itm])) {
			return $_GET[$itm];
		}
		return;
	}
	
	protected function getPostItem($itm) {
		if (isset($_POST[$itm])) {
			return $_POST[$itm];
		}
		return;
	}
		
	protected function getQueryString() {
		$qs = "";
		if (!empty($_GET)) {
			while(list($k, $v) = each($_GET)) {
				$qs .= "&" . $k . "=" . $v;
			}
		}
		$qs = "?" . substr($qs, 1);
		
		return $qs;
	}
	
	# 31-07-2008 -> 2008-07-31
	protected function toYmdDate($v) {
		if (empty($v)) {
			return "";
		}
		return substr($v, 6, 4) . "-" . substr($v, 3, 2)  . "-" . substr($v, 0, 2);
	}

	# 2008-07-31 -> 31-07-2008
	protected function todmYDate($v) {
		if (empty($v)) {
			return "";
		}
		return substr($v, 8, 2) . "-" . substr($v, 5, 2)  . "-" . substr($v, 0, 4);
	}		
	
}
?>