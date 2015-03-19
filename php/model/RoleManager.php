<?php

/**
 * Class   :  RoleManager
 * Author  :  Jerry Sihombing
 * Created :  2011-10-06
 * Desc    :  Data handler for Role Management
 */

require_once(dirname(__FILE__) . "/../util/ConfigReader.php");

class RoleManager {
	private $_mysqli;
	private $_host;
	private $_dbname;
	private $_user;
	private $_pass;

	private $_id;
	private $_role_name;
	private $_description;
	private $_detail = "";	
	
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

	private function makeEscapedString() {
		# -- note: mysqli::real_escape_string needs a valid connection opened -- #
		# -- so, call this function after makeConnection() called -- #
		
		try {
			$this->_role_name = $this->_mysqli->real_escape_string($this->_role_name);
			$this->_description = $this->_mysqli->real_escape_string($this->_description);
		}
		catch (Exception $e) {
			# do nothing
		}
		
	}	
	
	public function loadAll() {	
		$this->makeConnection();
		
		$sql = "CALL role_load_all()";
		
		$res = $this->_mysqli->query($sql);
		
		$data = array();
		while ($row = $res->fetch_assoc()) {
			array_push($data, $row);
		}
		
		$res->close();
		$this->closeConnection();
		
		return $data;
	}
	
	public function loadDetailByName($name) {	
		$this->makeConnection();
		
		$sql = "CALL role_load_dtl_by_name('$name')";
		
		$res = $this->_mysqli->query($sql);
		
		$data = array();
		while ($row = $res->fetch_assoc()) {
			array_push($data, $row);
		}
		
		$res->close();
		$this->closeConnection();
		
		return $data;
	}
	
	public function loadDetail($id) {	
		$this->makeConnection();
		
		$sql = "CALL role_load_dtl($id)";
		
		$res = $this->_mysqli->query($sql);
		
		$data = array();
		while ($row = $res->fetch_assoc()) {
			array_push($data, $row);
		}
		
		$res->close();
		$this->closeConnection();
		
		return $data;
	}
			
	public function load($id) {	
		$this->makeConnection();
		
		$sql = "CALL role_load($id)";
		
		$res = $this->_mysqli->query($sql);

		if ($row = $res->fetch_assoc()) {
			$this->_id = $id;
			$this->_role_name = $row["role_name"];
			$this->_description = $row["description"];
			$this->_createdBy = $row["created_by"];
			$this->_createdDate = $row["created_date"];
			$this->_lastUser = $row["last_user"];
			$this->_lastUpdate = $row["last_update"];
		}
		
		$res->close();
		$this->closeConnection();
	}
	
	public function remove($id) {
		$this->makeConnection();
		
		$sql = "CALL role_remove($id)";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function update($id) {
		$this->makeConnection();
		$this->makeEscapedString();		
		
		$sql = "CALL role_update(" .
					$id . ", '" .
					$this->_role_name . "', '" .
					$this->_description . "', '" .
					$this->_detail . "', '" .
					$this->_lastUser . "', '" .
					$this->_lastUpdate . 
				"')";		
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function addNew() {
		$this->makeConnection();		
		$this->makeEscapedString();
		
		$sql = "CALL role_add('" . 
					$this->_role_name . "', '" .
					$this->_description . "', '" .
					$this->_detail . "', '" .
					$this->_createdBy . "', '" .
					$this->_createdDate . 
				"')";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function isExist($role) {
		$this->makeConnection();
		$role = $this->_mysqli->real_escape_string($role);
		
		$sql = "SELECT role_count('$role')";
			
		$res = $this->_mysqli->query($sql);
		$row = $res->fetch_row(); 
		$res->close();
		$this->closeConnection();
		
		return ($row[0] > 0 ? true : false);
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
	public function getRoleName() {
		return $this->_role_name;
	}
	public function getDescription() {
		return $this->_description;
	}
	public function getDetail() {
		return $this->_detail;
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
	public function setRoleName($v) {
		$this->_role_name = $v;
	}
	public function setDescription($v) {
		$this->_description = $v;
	}
	public function setDetail($v) {
		$this->_detail = $v;
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