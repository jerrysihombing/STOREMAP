<?php
/**
 * AuthController
 * File   : AuthController.php
 * author : Jerry Sihombing
 * desc   : application controller
 * date	  : 2008-09-07
 */

require_once (dirname(__FILE__) . "/../model/UserManager.php");

class AuthController extends GlobalController {

	function __construct() {

	}

	public function controll ($op) {
		$ac = $this->getCurrAct();

		$UM = new UserManager();
		$msg = "";

		switch ($op)  {
			case "login":								
				$msg = $this->getSessionItem("msg");
				$this->setSessionItem("msg", "");

				include_once("html/login.html");

				break;

			case "chpass":
				$this->checkFirst();
				
				$user_id = $this->getCurrUserId();
				$user_name = $this->getCurrUserName();
				$user_name = !empty($user_name) ? $user_name : $user_id;
				$role = $this->getSessionItem("role");
			
				include_once("html/chpass.html");

				break;

			case "authentication":
				require_once (dirname(__FILE__) . "/../model/RoleManager.php");
				
				$user_id = $this->getPostItem("user_id");
				$passwd = $this->getPostItem("passwd");
				
				if ($UM->isValidUser($user_id, $passwd)) {	
				
					$strdate = date("Y-m-d H:i:s");					
					$UM->loadByUserId($user_id);					
				
					$RM = new RoleManager();
					$a_auth = $RM->loadDetailByName($UM->getRoleName());					
				
					$this->setSessionItem("user_id", $user_id);
					$this->setSessionItem("user_name", $UM->getUserName());
					$this->setSessionItem("branch_code", $UM->getBranchCode());
					$this->setSessionItem("role", $UM->getRoleName());
					$this->setSessionItem("a_auth", $a_auth);
										
					$this->setSessionItem("msg", "");

					# don't forget to log
					try {
						$UM->login($strdate);
					}
					catch (Exception $e) {
						# forget it, huehuehue....
					}

					header("Location: /index.html");
					#header("Location: http://" . $_SERVER["HTTP_HOST"] . "/index.html");
					exit;
				}
				else {
					$msg = "Invalid user id and or password!!";
					$this->setSessionItem("msg", $msg);

					header("Location: /login.html");
					#header("Location: http://" . $_SERVER["HTTP_HOST"] . "/login.html");
					exit;
				}

				break;

			case "unauthorized":
				include_once("html/unauthorized.html");				

				break;

			default:
				break;
		}

	}
}

?>