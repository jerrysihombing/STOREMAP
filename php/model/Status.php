<?php

/**
 * Class   :  Status
 * Author  :  Jerry Sihombing
 * Created :  2015-01-30
 * Desc    :  Data handler for Status
 */

require_once(dirname(__FILE__) . "/../util/ConfigReader.php");

class Status {
	private $_mysqli;
	private $_host;
	private $_dbname;
	private $_user;
	private $_pass;
	
	private $_id;
	private $_code;
	private $_name;
	private $_description;
	private $_color;
	private $_minValue;
    private $_maxValue;
	private $_minValueWide;
    private $_maxValueWide;
	
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
			$this->_color = $this->_mysqli->real_escape_string($this->_color);
		}
		catch (Exception $e) {
			# do nothing
		}
		
	}	
	
	public function findColor($value, $wide = 0) {
		$this->makeConnection();
		
		$sql = "SELECT status_find_color($value, $wide)";	
			
		$res = $this->_mysqli->query($sql);
		$row = $res->fetch_row(); 
		$res->close();
		$this->closeConnection();
		
		return $row[0];
	}
	
	public function loadAll() {				
		$this->makeConnection();
		
		$sql = "CALL status_load_all()";
		
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
		
		$sql = "CALL status_load($id)";
		
		$res = $this->_mysqli->query($sql);

		if ($row = $res->fetch_assoc()) {
			$this->_id = $id;
			$this->_code = $row["code"];
			$this->_name = $row["name"];
			$this->_description = $row["description"];
			$this->_color = $row["color"];			
			$this->_minValue = $row["min_value"];
            $this->_maxValue = $row["max_value"];
			$this->_minValueWide = $row["min_value_wide"];
            $this->_maxValueWide = $row["max_value_wide"];
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
		
		$sql = "CALL status_remove($id)";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function update($id) {
		$this->makeConnection();		
		$this->makeEscapedString();
		
		$sql = "CALL status_update(" .
					$id . ", '" .
					$this->_code . "', '" .
					$this->_name . "', '" .
					$this->_description . "', '" .
					$this->_color . "', '" .
					$this->_minValue . "', '" .
                    $this->_maxValue . "', '" .
					$this->_lastUser . "', '" .
					$this->_lastUpdate . "', '" .
					$this->_minValueWide . "', '" .
                    $this->_maxValueWide . 
				"')";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function addNew() {
		$this->makeConnection();		
		$this->makeEscapedString();
		
		$sql = "CALL status_add('" . 
					$this->_code . "', '" .
					$this->_name . "', '" .
					$this->_description . "', '" .
					$this->_color . "', '" .
					$this->_minValue . "', '" .
                    $this->_maxValue . "', '" .
					$this->_createdBy . "', '" .
					$this->_createdDate . "', '" .
					$this->_minValueWide . "', '" .
                    $this->_maxValueWide . 
				"')";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function isExist($code) {
		$this->makeConnection();
		$code = $this->_mysqli->real_escape_string($code);
		
		$sql = "SELECT status_count('$code')";	
			
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
	public function getColor() {
		return $this->_color;	
	}
	public function getMinValue() {
		return $this->_minValue;	
	}
    public function getMaxValue() {
		return $this->_maxValue;	
	}
	public function getMinValueWide() {
		return $this->_minValueWide;	
	}
    public function getMaxValueWide() {
		return $this->_maxValueWide;	
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
	public function setColor($v) {
		$this->_color = $v;	
	}
	public function setMinValue($v) {
		$this->_minValue = $v;	
	}
    public function setMaxValue($v) {
		$this->_maxValue = $v;	
	}
	public function setMinValueWide($v) {
		$this->_minValueWide = $v;	
	}
    public function setMaxValueWide($v) {
		$this->_maxValueWide = $v;	
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