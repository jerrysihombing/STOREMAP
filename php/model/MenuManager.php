<?php

/**
 * Class   :  MenuManager
 * Author  :  Jerry Sihombing
 * Created :  2011-10-06
 * Desc    :  Data handler for Menu Management
 */

require_once(dirname(__FILE__) . "/../util/ConfigReader.php");

class MenuManager {
	private $_mysqli;
	private $_host;
	private $_dbname;
	private $_user;
	private $_pass;

	private $_id;
	private $_menu_id;
	private $_title;
	
	private $_createdBy;
	private $_createdDate;
	private $_lastUser;
    private $_lastUpdate;
		
	public function __construct() {
		$CR = new ConfigReader("db.conf.php");

		$this->_host = $CR->get("#host");
		$this->_dbname = $CR->get("#dbname");
		$this->_user = $CR->get("#user");
		$this->_pass = $CR->get("#pass");
	}
	
	public function loadAll() {				
		$this->makeConnection();
		
		$sql = "CALL menu_load_all()";
		
		$res = $this->_mysqli->query($sql);
		
		$data = array();
		while ($row = $res->fetch_assoc()) {
			array_push($data, $row);
		}
		
		$res->close();
		$this->closeConnection();
		
		return $data;
	}
	
	private function generateErrorMessage($success) {
		if ($success) {
			$this->_error = "";
		}
		else {
			$this->_error = "Error (" . $this->_mysqli->errno . "): " . $this->_mysqli->error . ".";
		}
	}
	
	private function closeConnection() {
		$this->_mysqli->close();
	}
		
	private function makeConnection() {
		$this->_mysqli = new mysqli("$this->_host", "$this->_user", "$this->_pass", "$this->_dbname");

		/* check connection */
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}

	}

	# --- accessor functions --- #
	
	public function getId() {
		return $this->_id;
	}
	public function getMenuId() {
		return $this->_menu_id;
	}
	public function getTitle() {
		return $this->_title;
	}
	public function getCreatedDate() {
		return $this->_createdDate;
	}
	public function getCreatedBy() {
		return $this->_createdBy;
	}
	public function getLastUser() {
		return $this->_lastUser;
	}
	public function getLastUpdate() {
		return $this->_lastUpdate;
	}
	public function getError() {
		return $this->_error;
	}
	
	public function setId($v) {
		$this->_id = $v;
	}
	public function setMenuId($v) {
		$this->_menu_id = $v;
	}
	public function setTitle($v) {
		$this->_title = $v;
	}
	public function setCreatedDate($v) {
		$this->_createdDate = $v;
	}
	public function setCreatedBy($v) {
		$this->_createdBy = $v;
	}
	public function setLastUser($v) {
		$this->_lastUser = $v;
	}
	public function setLastUpdate($v) {
		$this->_lastUpdate = $v;
	}
	public function setError($v) {
		$this->_error = $v;
	}
	
	# --- end of accessor --- #

}

?>