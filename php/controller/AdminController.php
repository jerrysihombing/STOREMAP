<?php
/**
 * AdminController
 * File   : AdminController.php
 * author : Jerry Sihombing
 * desc   : application controller
 * date	  : 2011-10-06
 */

require_once (dirname(__FILE__) . "/../model/RoleManager.php");
require_once (dirname(__FILE__) . "/../model/MenuManager.php");
require_once (dirname(__FILE__) . "/../model/UserManager.php");
require_once (dirname(__FILE__) . "/../model/Site.php");

class AdminController extends GlobalController {

	function __construct() {

	}
	
	public function controll () {		
		$ac = $this->getItem("ac");
		$a_auth = $this->getCurrAuth();
		$user_id = $this->getCurrUserId();
		$user_name = $this->getCurrUserName();
		$user_name = empty($user_name) ? $user_name : $user_id;
		$role = $this->getSessionItem("role");	

		switch ($ac) {

			case "":
			case "list":
				$this->checkFirst();
				$this->isOperAllowable($a_auth, 999);
				
				$RM = new RoleManager();
				$role_data = $RM->loadAll();		
				
				$MM  = new MenuManager();
				$menu_data = $MM->loadAll();
				
				$UM = new UserManager();
				$user_roleless_data	= $UM->loadByRoleName("N/A");
				
				$ST = new Site();
				$store_data = $ST->storeLoadAll();
				$optStore = "";
				foreach($store_data as $data) {
					$optStore .= "<option value='" . $data["store_init"] . "'>" . $data["store_init"] . "</option>";
				}
				
				#print_r($role_data);
					
				include_once("html/admin_list.html");
				
				break;						
				
			case "getbackup":
				require_once(dirname(__FILE__) . "/../util/ConfigReader.php");
				$CR = new ConfigReader("db.conf.php");
				$backupdir = $CR->get("#backupdir");	
				
				$file = $backupdir . "/" . $this->getItem("filename");

				if (file_exists($file)) {
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename=' . basename($file));
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file));
					ob_clean();
					flush();
					readfile($file);
					exit;
				}
	
				break;

			default:

				break;
		}

	}

}
?>