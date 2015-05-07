<?php

/**
 * FrontController
 * File   : FrontController.php
 * author : Jerry Sihombing
 * desc   : application controller
 * date	  : 2006-12-09
 */

require_once ("GlobalController.php");

class FrontController extends GlobalController  {	
	
	public function __construct() {
		parent::__construct();
		
		// set the default timezone to use. Available since PHP 5.1
		date_default_timezone_set('Asia/Jakarta');
	}

	private function greeting() {		
		$chkTime = date("G");

		if ($chkTime > 3 && $chkTime <= 11) {
			#return "Good morning ";
			return "Selamat pagi ";
		}
		else if ($chkTime > 11 && $chkTime <= 18) {
			#return "Good afternoon ";
			return "Selamat siang ";
		}
		else if ($chkTime > 18 && $chkTime <= 24) {
			#return "Good evening ";
			return "Selamat petang ";
		}
		else if ($chkTime > 0 && $chkTime <= 3) {
			#return "Good evening ";
			return "Selamat malam ";
		}
		else {
			#return "Good morning ";
			return "Selamat pagi ";
		}
	}		
		
	public function execute() {
		
		$op = $this->getCurrOper();	
		$a_auth = $this->getCurrAuth();	
		$user_id = $this->getCurrUserId();
		$user_name = $this->getCurrUserName();
		$user_name = empty($user_name) ? $user_id : $user_name;
		$role = $this->getSessionItem("role");			
		
		#print_r($a_auth);
		
		switch ($op) {
			
			case "":
			
			# --- home --- #
			case "index":
				$this->checkFirst();				
				#$greeting = $this->greeting();
				
				#require_once (dirname(__FILE__) . "/../model/StoreMap.php");
				#$MDL = new StoreMap();
				#$data = $MDL->loadAll();
				#include_once("html/index_map.html");
				
				include_once("html/index.html");

				break;
			# --- eo home --- #
			
			# --- brand --- #
			case "brand":
				#$this->checkFirst();
				#$this->isOperAllowable($a_auth, 100);
				
				require_once ("BrandController.php");
				
				$CTR = new BrandController();
				$CTR->controll();

				break;
			# --- eo brand --- #
			
			# --- article --- #
			case "article":
				#$this->checkFirst();
				#$this->isOperAllowable($a_auth, 100);
				
				require_once ("ArticleController.php");
				
				$CTR = new ArticleController();
				$CTR->controll();

				break;
			# --- eo article --- #
			
			# --- map --- #
			case "map":
				#$this->checkFirst();
				#$this->isOperAllowable($a_auth, 100);
				
				require_once ("MapController.php");
				
				$CTR = new MapController();
				$CTR->controll();

				break;
			# --- eo map --- #
			
			# --- section --- #
			case "section":
				#$this->checkFirst();
				#$this->isOperAllowable($a_auth, 100);
				
				require_once ("CoordinateController.php");
				
				$CTR = new CoordinateController();
				$CTR->controll();

				break;
			# --- eo section --- #
			
			# --- status --- #
			case "status":
				#$this->checkFirst();
				#$this->isOperAllowable($a_auth, 100);
				
				require_once ("StatusController.php");
				
				$CTR = new StatusController();
				$CTR->controll();

				break;
			# --- eo status --- #
			
			# --- data --- #
			case "data":
				#$this->checkFirst();
				#$this->isOperAllowable($a_auth, 100);
				
				require_once ("DataController.php");
				
				$CTR = new DataController();
				$CTR->controll();

				break;
			# --- eo data --- #
			
			# --- sales --- #
			case "sales":
				#$this->checkFirst();
				#$this->isOperAllowable($a_auth, 100);
				
				require_once ("SalesController.php");
				
				$CTR = new SalesController();
				$CTR->controll();

				break;
			# --- eo sales --- #
			
			# --- report --- #
			case "report":
				#$this->checkFirst();
				#$this->isOperAllowable($a_auth, 100);
				
				require_once ("ReportController.php");
				
				$CTR = new ReportController();
				$CTR->controll();

				break;
			# --- eo report --- #
			
			# --- reportv2 --- #
			case "reportv2":
				#$this->checkFirst();
				#$this->isOperAllowable($a_auth, 100);
				
				require_once ("Reportv2Controller.php");
				
				$CTR = new Reportv2Controller();
				$CTR->controll();

				break;
			# --- eo reportv2 --- #
			
			
			# --- system --- #
			
			# --- admin --- #
			case "admin":
				#$this->checkFirst();
				#$this->isOperAllowable($a_auth, 999);
				
				require_once ("AdminController.php");
				
				$CTR = new AdminController();
				$CTR->controll();

				break;
			# --- eo admin --- #

			# --- logout --- #
			case "logout":
				session_unset();
				session_destroy();

				$this->checkFirst();

				break;
			# --- eo logout --- #


			# --- login --- #
			case "login":				
				require_once ("AuthController.php");
				
				$CTR = new AuthController();
				$CTR->controll($op);								
				
				break;
			# --- eo login --- #


			# --- unauthorized --- #
			case "unauthorized":
				$this->checkFirst();
				
				require_once ("AuthController.php");

				$CTR = new AuthController();
				$CTR->controll($op);

				break;
			# --- eo unauthorized --- #


			# --- authentication --- #
			case "authentication":
				require_once ("AuthController.php");
								
				$CTR = new AuthController();
				$CTR->controll($op);

				break;
			# --- authentication --- #


			# --- chpass --- #
			case "chpass":
				#$this->checkFirst();
				
				require_once ("AuthController.php");

				$CTR = new AuthController();
				$CTR->controll($op);

				break;
			# --- eo chpass --- #

			default:
				# do nothing
				
				break;
				
		}																			
	
	}

}

?>
