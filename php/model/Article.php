<?php

/**
 * Class   :  Article
 * Author  :  Jerry Sihombing
 * Created :  2015-02-18
 * Desc    :  Data handler for Article
 */

require_once(dirname(__FILE__) . "/../util/ConfigReader.php");

class Article {
	private $_mysqli;
	private $_host;
	private $_dbname;
	private $_user;
	private $_pass;
	
	private $_id;
	private $_plu8;
	private $_articleType;
	private $_articleCode;
	private $_description;
	private $_brandName;
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
			$this->plu8 = $this->_mysqli->real_escape_string($this->_plu8);
			$this->_articleCode = $this->_mysqli->real_escape_string($this->_articleCode);
			$this->_description = $this->_mysqli->real_escape_string($this->_description);
			$this->_brandName = $this->_mysqli->real_escape_string($this->_brandName);
			$this->_storeInit = $this->_mysqli->real_escape_string($this->_storeInit);
		}
		catch (Exception $e) {
			# do nothing
		}
		
	}	

	public function loadAll() {				
		$this->makeConnection();
		
		$sql = "CALL article_load_all()";
		
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
		
		$sql = "CALL article_load($id)";
		
		$res = $this->_mysqli->query($sql);

		if ($row = $res->fetch_assoc()) {
			$this->_id = $id;
			$this->_plu8 = $row["plu8"];
			$this->_articleType = $row["article_type"];
			$this->_articleCode = $row["article_code"];
			$this->_description = $row["description"];
			$this->_brandName = $row["brand_name"];
			$this->_storeInit = $row["store_init"];
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
		
		$sql = "CALL article_remove($id)";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function update($id) {
		$this->makeConnection();
		$this->makeEscapedString();
		
		$sql = "CALL article_update(" .
					$id . ", '" .
					$this->_plu8 . "', '" .
					$this->_articleType . "', '" .
					$this->_articleCode . "', '" .
					$this->_description . "', '" .
					$this->_brandName . "', '" .
					$this->_storeInit . "', '" .
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
		
		$sql = "CALL article_add('" . 
					$this->_plu8 . "', '" .
					$this->_articleType . "', '" .
					$this->_articleCode . "', '" .
					$this->_description . "', '" .
					$this->_brandName . "', '" .
					$this->_storeInit . "', '" .
					$this->_createdBy . "', '" .
					$this->_createdDate . 
				"')";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function isExist($plu8) {
		$this->makeConnection();
        $plu8 = $this->_mysqli->real_escape_string($plu8);
		
		$sql = "SELECT article_count('$plu8')";	
			
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
	public function getPlu8() {
		return $this->_plu8;	
	}
	public function getArticleType() {
		return $this->_articleType;	
	}
	public function getArticleCode() {
		return $this->_articleCode;	
	}
	public function getDescription() {
		return $this->_description;	
	}
	public function getBrandName() {
		return $this->_brandName;	
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
	public function setPlu8($v) {
		$this->_plu8 = $v;	
	}
	public function setArticleType($v) {
		$this->_articleType = $v;	
	}
	public function setArticleCode($v) {
		$this->_articleCode = $v;	
	}
	public function setDescription($v) {
		$this->_description = $v;	
	}
	public function setBrandName($v) {
		$this->_brandName = $v;	
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