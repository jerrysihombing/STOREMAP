<?php

/**
 * Class   :  Data
 * Author  :  Jerry Sihombing
 * Created :  2015-01-30
 * Desc    :  Data handler for Data
 */

require_once(dirname(__FILE__) . "/../util/ConfigReader.php");

class Data {
	private $_mysqli;
	private $_host;
	private $_dbname;
	private $_user;
	private $_pass;
	
	private $_id;
	private $_mapCode;
	private $_storemapCode;
	private $_dataCategory;
	private $_dataValue;
	private $_dataMonth;
	private $_dataYear;
    private $_description;
	
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
			$this->_mapCode = $this->_mysqli->real_escape_string($this->_mapCode);
			$this->_storemapCode = $this->_mysqli->real_escape_string($this->_storemapCode);
			$this->_dataCategory = $this->_mysqli->real_escape_string($this->_dataCategory);
			$this->_description = $this->_mysqli->real_escape_string($this->_description);
		}
		catch (Exception $e) {
			# do nothing
		}
		
	}	
	
	public function findByStoremapAndPeriod($storemapCode, $dataCategory, $dataMonth, $dataYear) {
		$this->makeConnection();
		
		$sql = "SELECT data_find_by_storemap_code_and_period('$storemapCode', '$dataCategory', $dataMonth, $dataYear)";	
			
		$res = $this->_mysqli->query($sql);
		$row = $res->fetch_row(); 
		$res->close();
		$this->closeConnection();
		
		return $row[0];
	}
	
	public function findLastByStoremap($storemapCode, $dataCategory) {
		$this->makeConnection();
		
		$sql = "SELECT data_find_last_by_storemap_code('$storemapCode', '$dataCategory')";	
			
		$res = $this->_mysqli->query($sql);
		$row = $res->fetch_row(); 
		$res->close();
		$this->closeConnection();
		
		return $row[0];
	}
	
	public function loadAll() {				
		$this->makeConnection();
		
		$sql = "CALL data_load_all()";
		
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
		
		$sql = "CALL data_load($id)";
		
		$res = $this->_mysqli->query($sql);

		if ($row = $res->fetch_assoc()) {
			$this->_id = $id;
			$this->_mapCode = $row["map_code"];
			$this->_storemapCode = $row["storemap_code"];
			$this->_dataCategory = $row["data_category"];
			$this->_dataValue = $row["data_value"];
			$this->_dataMonth = $row["data_month"];			
			$this->_dataYear = $row["data_year"];
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
		
		$sql = "CALL data_remove($id)";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function update($id) {
		$this->makeConnection();		
		$this->makeEscapedString();
		
		$sql = "CALL data_update(" .
					$id . ", '" .
					$this->_mapCode . "', '" .
					$this->_storemapCode . "', '" .
					$this->_dataCategory . "', '" .
					$this->_dataValue . "', '" .
					$this->_dataMonth . "', '" .
					$this->_dataYear . "', '" .
                    $this->_description . "', '" .
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
		
		$sql = "CALL data_add('" .
					$this->_mapCode . "', '" .
					$this->_storemapCode . "', '" .
					$this->_dataCategory . "', '" .
					$this->_dataValue . "', '" .
					$this->_dataMonth . "', '" .
					$this->_dataYear . "', '" .
                    $this->_description . "', '" .
					$this->_createdBy . "', '" .
					$this->_createdDate . 
				"')";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function isExist($mapCode, $storemapCode, $dataCategory, $dataMonth, $dataYear) {
		$this->makeConnection();
		$mapCode = $this->_mysqli->real_escape_string($mapCode);
		$storemapCode = $this->_mysqli->real_escape_string($storemapCode);
		$dataCategory = $this->_mysqli->real_escape_string($dataCategory);
		
		$sql = "SELECT data_count('$mapCode', '$storemapCode', '$dataCategory', $dataMonth, $dataYear)";	
			
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
	public function getMapCode() {
		return $this->_mapCode;	
	}
	public function getStoremapCode() {
		return $this->_storemapCode;	
	}
	public function getDataCategory() {
		return $this->_dataCategory;	
	}
	public function getDataValue() {
		return $this->_dataValue;	
	}
	public function getDataMonth() {
		return $this->_dataMonth;	
	}
	public function getDataYear() {
		return $this->_dataYear;	
	}
    public function getDescription() {
		return $this->_description;	
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
	public function setMapCode($v) {
		$this->_mapCode = $v;	
	}
	public function setStoremapCode($v) {
		$this->_storemapCode = $v;	
	}
	public function setDataCategory($v) {
		$this->_dataCategory = $v;	
	}
	public function setDataValue($v) {
		$this->_dataValue = $v;	
	}
	public function setDataMonth($v) {
		$this->_dataMonth = $v;	
	}
	public function setDataYear($v) {
		$this->_dataYear = $v;	
	}
    public function setDescription($v) {
		$this->_description = $v;	
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