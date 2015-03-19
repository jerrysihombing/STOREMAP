<?php
/**
 * Class   :  ConfigReader
 * Author  :  Jerry Sihombing
 * Created :  2007-01-20
 * Desc    :  Utility to read configuration file
 */

class ConfigReader {
	
	private $_filePath;
	
	function __construct($filename) {
		$this->_filePath = dirname(__FILE__) . "/../conf/" . $filename;
	}
	
	public function get($itm) {	
		$lines = file($this->_filePath);
		$res = "";
	
		# Loop through our array
		while(list($k, $v) = each($lines)) {			
			$data = explode("=", $v);
			if ($data[0] == $itm) {
				$res = trim($data[1]);
				break;
			}
		}
		
		return $res;
	}
	
	public function is_oper_allowable($id_menu, $a_auth) {
		$allow = false;	
		foreach($a_auth as $arr) {			
			if ($id_menu == $arr["id_menu"]) {				
				$allow = true;
				break;
			}
		}	
		return $allow;
	}

}


?>