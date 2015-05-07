<?php

/**
 * Class   :  Brand
 * Author  :  Jerry Sihombing
 * Created :  2015-02-18
 * Desc    :  Data handler for Brand
 */

require_once(dirname(__FILE__) . "/../util/ConfigReader.php");

class Brand {
	private $_mysqli;
	private $_host;
	private $_dbname;
	private $_user;
	private $_pass;
	
	private $_id;
	private $_code;
	private $_name;
	private $_description;
	private $_division;
	private $_storeInit;
	
	private $_createdBy;
	private $_createdDate;
	private $_lastUser;
    private $_lastUpdate;
	
	private $_error;

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
			$this->_name = $this->_mysqli->real_escape_string($this->_name);
			$this->_description = $this->_mysqli->real_escape_string($this->_description);
			$this->_division = $this->_mysqli->real_escape_string($this->_division);
			$this->_storeInit = $this->_mysqli->real_escape_string($this->_storeInit);
			$this->_code = $this->_mysqli->real_escape_string($this->_code);
		}
		catch (Exception $e) {
			# do nothing
		}
		
	}	
	
	public function loadDistinctName() {				
		$this->makeConnection();
		
		$sql = "CALL brand_load_distinct_name()";
		
		$res = $this->_mysqli->query($sql);
		
		$data = array();
		while ($row = $res->fetch_assoc()) {
			array_push($data, $row);
		}
		
		$res->close();
		$this->closeConnection();
		
		return $data;
	}
	
	public function loadAll() {				
		$this->makeConnection();
		
		$sql = "CALL brand_load_all()";
		
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
		
		$sql = "CALL brand_load($id)";
		
		$res = $this->_mysqli->query($sql);

		if ($row = $res->fetch_assoc()) {
			$this->_id = $id;
			$this->_name = $row["name"];
			$this->_description = $row["description"];
			$this->_division = $row["division"];
			$this->_storeInit = $row["store_init"];
			$this->_createdBy = $row["created_by"];
			$this->_createdDate = $row["created_date"];
			$this->_lastUser = $row["last_user"];
			$this->_lastUpdate = $row["last_update"];
			$this->_code = $row["code"];
		}
		
		$res->close();
		$this->closeConnection();				
	}

	public function remove($id) {
		$this->makeConnection();
		
		$sql = "CALL brand_remove($id)";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function update($id) {
		$this->makeConnection();
		$this->makeEscapedString();
		
		$sql = "CALL brand_update(" .
					$id . ", '" .
					$this->_name . "', '" .
					$this->_description . "', '" .
					$this->_division . "', '" .
					$this->_storeInit . "', '" .
					$this->_lastUser . "', '" .
					$this->_lastUpdate . "', '" .
					$this->_code . 
				"')";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function addNew() {
		$this->makeConnection();		
		$this->makeEscapedString();
		
		$sql = "CALL brand_add('" . 
					$this->_name . "', '" .
					$this->_description . "', '" .
					$this->_division . "', '" .
					$this->_storeInit . "', '" .
					$this->_createdBy . "', '" .
					$this->_createdDate . "', '" .
					$this->_code . 
				"')";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function isBrandDivisionExist($name, $division) {
		$this->makeConnection();
        $name = $this->_mysqli->real_escape_string($name);
		$division = $this->_mysqli->real_escape_string($division);
		
		$sql = "SELECT brand_division_count('$name', '$division')";	
			
		$res = $this->_mysqli->query($sql);
		$row = $res->fetch_row(); 
		$res->close();
		$this->closeConnection();
		
		return ($row[0] > 0 ? true : false);
	}
	
	public function isExist($name) {
		$this->makeConnection();
        $name = $this->_mysqli->real_escape_string($name);
		
		$sql = "SELECT brand_count('$name')";	
			
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
	public function getCode() {
		return $this->_code;	
	}
	public function getName() {
		return $this->_name;	
	}
	public function getDescription() {
		return $this->_description;	
	}
	public function getDivision() {
		return $this->_division;	
	}
	public function getStoreInit() {
		return $this->_storeInit;	
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
	public function setCode($v) {
		$this->_code = $v;	
	}
	public function setName($v) {
		$this->_name = $v;	
	}
	public function setDescription($v) {
		$this->_description = $v;	
	}
	public function setDivision($v) {
		$this->_division = $v;	
	}
	public function setStoreInit($v) {
		$this->_storeInit = $v;	
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