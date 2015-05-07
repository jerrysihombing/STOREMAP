<?php

/**
 * Class   :  StoreMap
 * Author  :  Jerry Sihombing
 * Created :  2015-01-26
 * Desc    :  Data handler for Store Mapping
 */

require_once(dirname(__FILE__) . "/../util/ConfigReader.php");

class StoreMap {
	private $_mysqli;
	private $_host;
	private $_dbname;
	private $_user;
	private $_pass;
	
	private $_id;
	private $_code;
	private $_name;
	private $_description;
	private $_brandName;
	private $_division;
	private $_shape;
	private $_coordinate;
	private $_initColor;
	private $_mapCode;
	
	private $_createdBy;
	private $_createdDate;
	private $_lastUpdate;
	private $_lastUser;
	
	private $_topLeft;
	private $_bottomRight;
	private $_center;
	private $_radius;
	
	private $_wide;
	
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
			$this->_brandName = $this->_mysqli->real_escape_string($this->_brandName);
			$this->_shape = $this->_mysqli->real_escape_string($this->_shape);
			$this->_coordinate = $this->_mysqli->real_escape_string($this->_coordinate);
			$this->_initColor = $this->_mysqli->real_escape_string($this->_initColor);
			$this->_mapCode = $this->_mysqli->real_escape_string($this->_mapCode);
			$this->_topLeft = $this->_mysqli->real_escape_string($this->_topLeft);
			$this->_bottomRight = $this->_mysqli->real_escape_string($this->_bottomRight);
			$this->_center = $this->_mysqli->real_escape_string($this->_center);
			$this->_radius = $this->_mysqli->real_escape_string($this->_radius);
			$this->_division = $this->_mysqli->real_escape_string($this->_division);
		}
		catch (Exception $e) {
			# do nothing
		}
		
	}	

	public function loadAll() {				
		$this->makeConnection();
		
		$sql = "CALL storemap_load_all()";
		
		$res = $this->_mysqli->query($sql);
		
		$data = array();
		while ($row = $res->fetch_assoc()) {
			array_push($data, $row);
		}
		
		$res->close();
		$this->closeConnection();
		
		return $data;
	}
	
	public function loadByMapId($id) {				
		$this->makeConnection();
		
		$sql = "CALL storemap_load_by_map_id($id)";
		
		$res = $this->_mysqli->query($sql);
		
		$data = array();
		while ($row = $res->fetch_assoc()) {
			array_push($data, $row);
		}
		
		$res->close();
		$this->closeConnection();
		
		return $data;
	}
	
	public function loadByMapCode($mc) {				
		$this->makeConnection();
		
		$sql = "CALL storemap_load_by_map_code('$mc')";
		
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
		
		$sql = "CALL storemap_load($id)";
		
		$res = $this->_mysqli->query($sql);

		if ($row = $res->fetch_assoc()) {
			$this->_id = $id;
			$this->_code = $row["code"];
			$this->_name = $row["name"];
			$this->_description = $row["description"];
			$this->_brandName = $row["brand_name"];
			$this->_shape = $row["shape"];			
			$this->_coordinate = $row["coordinate"];
			$this->_initColor = $row["init_color"];
			$this->_mapCode = $row["map_code"];
			$this->_topLeft = $row["top_left"];
			$this->_bottomRight = $row["bottom_right"];
			$this->_center = $row["center"];
			$this->_radius = $row["radius"];
			$this->_createdBy = $row["created_by"];
			$this->_createdDate = $row["created_date"];
			$this->_lastUser = $row["last_user"];
			$this->_lastUpdate = $row["last_update"];
			$this->_wide = $row["wide"];
			$this->_division = $row["division"];
		}
		
		$res->close();
		$this->closeConnection();				
	}

	public function remove($id) {
		$this->makeConnection();
		
		$sql = "CALL storemap_remove($id)";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function update($id) {
		$this->makeConnection();		
		$this->makeEscapedString();
		
		$sql = "CALL storemap_update(" .
					$id . ", '" .
					$this->_code . "', '" .
					$this->_name . "', '" .
					$this->_description . "', '" .
					$this->_brandName . "', '" .
					$this->_shape . "', '" .
					$this->_coordinate . "', '" .
					$this->_initColor . "', '" .
					$this->_mapCode . "', '" .
					$this->_topLeft . "', '" .
					$this->_bottomRight . "', '" .
					$this->_center . "', '" .
					$this->_radius . "', '" .
					$this->_lastUser . "', '" .
					$this->_lastUpdate . "', '" .
					$this->_wide . "', '" .
					$this->_division . 
				"')";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function addNew() {
		$this->makeConnection();		
		$this->makeEscapedString();
		
		$sql = "CALL storemap_add('" . 
					$this->_code . "', '" .
					$this->_name . "', '" .
					$this->_description . "', '" .
					$this->_brandName . "', '" .
					$this->_shape . "', '" .
					$this->_coordinate . "', '" .
					$this->_initColor . "', '" .
					$this->_mapCode . "', '" .
					$this->_topLeft . "', '" .
					$this->_bottomRight . "', '" .
					$this->_center . "', '" .
					$this->_radius . "', '" .
					$this->_createdBy . "', '" .
					$this->_createdDate . "', '" .
					$this->_wide . "', '" .
					$this->_division . 
				"')";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function isExist($code) {
		$this->makeConnection();
		$code = $this->_mysqli->real_escape_string($code);
		
		$sql = "SELECT storemap_count('$code')";	
			
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
	public function getBrandName() {
		return $this->_brandName;	
	}
	public function getDivision() {
		return $this->_division;	
	}
	public function getShape() {
		return $this->_shape;	
	}
	public function getCoordinate() {
		return $this->_coordinate;	
	}
	public function getInitColor() {
		return $this->_initColor;	
	}
	public function getMapCode() {
		return $this->_mapCode;	
	}
	public function getTopLeft() {
		return $this->_topLeft;	
	}
	public function getBottomRight() {
		return $this->_bottomRight;	
	}
	public function getCenter() {
		return $this->_center;	
	}
	public function getRadius() {
		return $this->_radius;	
	}
	public function getWide() {
		return $this->_wide;	
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
	public function setBrandName($v) {
		$this->_brandName = $v;	
	}
	public function setDivision($v) {
		$this->_division = $v;	
	}
	public function setShape($v) {
		$this->_shape = $v;	
	}
	public function setCoordinate($v) {
		$this->_coordinate = $v;	
	}
	public function setInitColor($v) {
		$this->_initColor = $v;	
	}
	public function setMapCode($v) {
		$this->_mapCode = $v;	
	}
	public function setTopLeft($v) {
		$this->_topLeft = $v;	
	}
	public function setBottomRight($v) {
		$this->_bottomRight = $v;	
	}
	public function setCenter($v) {
		$this->_center = $v;	
	}
	public function setRadius($v) {
		$this->_radius = $v;	
	}
	public function setWide($v) {
		$this->_wide = $v;	
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