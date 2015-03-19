<?php

/**
 * Class   :  Map
 * Author  :  Jerry Sihombing
 * Created :  2015-01-27
 * Desc    :  Data handler for Map
 */

require_once(dirname(__FILE__) . "/../util/ConfigReader.php");

class Map {
	private $_mysqli;
	private $_host;
	private $_dbname;
	private $_user;
	private $_pass;
	
	private $_id;
	private $_code;
	private $_name;
	private $_description;
	private $_storeInit;
	private $_mapFile;
	
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
			$this->_code = $this->_mysqli->real_escape_string($this->_code);
			$this->_name = $this->_mysqli->real_escape_string($this->_name);
			$this->_description = $this->_mysqli->real_escape_string($this->_description);
			$this->_storeInit = $this->_mysqli->real_escape_string($this->_storeInit);
			$this->_mapFile = $this->_mysqli->real_escape_string($this->_mapFile);
		}
		catch (Exception $e) {
			# do nothing
		}
		
	}	

	public function loadAll() {				
		$this->makeConnection();
		
		$sql = "CALL map_load_all()";
		
		$res = $this->_mysqli->query($sql);
		
		$data = array();
		while ($row = $res->fetch_assoc()) {
			array_push($data, $row);
		}
		
		$res->close();
		$this->closeConnection();
		
		return $data;
	}
	
	public function loadByStore($store) {				
		$this->makeConnection();
		
		$sql = "CALL map_load_by_store('$store')";
		
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
		
		$sql = "CALL map_load($id)";
		
		$res = $this->_mysqli->query($sql);

		if ($row = $res->fetch_assoc()) {
			$this->_id = $id;
			$this->_code = $row["code"];
			$this->_name = $row["name"];
			$this->_description = $row["description"];
			$this->_storeInit = $row["store_init"];			
			$this->_mapFile = $row["map_file"];
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
		
		$sql = "CALL map_remove($id)";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function update($id) {
		$this->makeConnection();		
		$this->makeEscapedString();
		
		$sql = "CALL map_update(" .
					$id . ", '" .
					$this->_code . "', '" .
					$this->_name . "', '" .
					$this->_description . "', '" .
					$this->_storeInit . "', '" .
					$this->_mapFile . "', '" .
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
		
		$sql = "CALL map_add('" . 
					$this->_code . "', '" .
					$this->_name . "', '" .
					$this->_description . "', '" .
					$this->_storeInit . "', '" .
					$this->_mapFile . "', '" .
					$this->_createdBy . "', '" .
					$this->_createdDate . 
				"')";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function isExist($code) {
		$this->makeConnection();
        $code = $this->_mysqli->real_escape_string($code);
		
		$sql = "SELECT map_count('$code')";	
			
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
	public function getStoreInit() {
		return $this->_storeInit;	
	}
	public function getMapFile() {
		return $this->_mapFile;	
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
	public function setStoreInit($v) {
		$this->_storeInit = $v;	
	}
	public function setMapFile($v) {
		$this->_mapFile = $v;	
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