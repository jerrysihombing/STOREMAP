<?php

/**
 * Class   :  UserManager
 * Author  :  Jerry Sihombing
 * Created :  2011-10-06
 * Desc    :  Data handler for User Management
 */

define("PRE_PASSWD", "d0d0l");
define("POST_PASSWD", "p3uy3um");

require_once(dirname(__FILE__) . "/../util/ConfigReader.php");

class UserManager {
	private $_mysqli;
	private $_host;
	private $_dbname;
	private $_user;
	private $_pass;

	private $_id;
	private $_user_id;
	private $_user_name;
	private $_passwd;
	private $_email;
	private $_branch_code;
	private $_departement;
	private $_role_name = "N/A";
	private $_active = 0;
	
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
			$this->_user_id = $this->_mysqli->real_escape_string($this->_user_id);
			$this->_passwd = $this->_mysqli->real_escape_string($this->_passwd);
			$this->_user_name = $this->_mysqli->real_escape_string($this->_user_name);
			$this->_branch_code = $this->_mysqli->real_escape_string($this->_branch_code);
			$this->_departement = $this->_mysqli->real_escape_string($this->_departement);
			$this->_role_name = $this->_mysqli->real_escape_string($this->_role_name);
		}
		catch (Exception $e) {
			# do nothing
		}
		
	}	
	
	public function login($logtime) {
		$this->makeConnection();
		
		$sql = "CALL user_login('$this->_user_id', '$logtime')";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function modifyActive($id, $act, $usr, $upd) {
		$this->makeConnection();
		
		$sql = "CALL user_set_active($id, $act, '$usr', '$upd')";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function assignRole($id, $role, $usr, $upd) {
		$this->makeConnection();
		$role = $this->_mysqli->real_escape_string($role);
		
		$sql = "CALL user_set_role($id, '$role', '$usr', '$upd')";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function modifyPasswdByUserId($user_id, $pass, $usr, $upd) {
		$this->makeConnection();
		$user_id = $this->_mysqli->real_escape_string($user_id);
		$pass = $this->_mysqli->real_escape_string($pass);
		$pass = sha1(PRE_PASSWD . $pass . POST_PASSWD);
		
		$sql = "CALL user_set_passwd_by_user_id('$user_id', '$pass', '$usr', '$upd')";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function modifyPasswd($id, $pass, $usr, $upd) {
		$this->makeConnection();
		$pass = $this->_mysqli->real_escape_string($pass);
		$pass = sha1(PRE_PASSWD . $pass . POST_PASSWD);
		
		$sql = "CALL user_set_passwd($id, '$pass', '$usr', '$upd')";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function isValidUser($usr, $pass) {	
		$this->makeConnection();
		$usr = $this->_mysqli->real_escape_string($usr);		
		$pass = $this->_mysqli->real_escape_string($pass);
		
		#$pass = md5(PRE_PASSWD . $pass . POST_PASSWD);
		$pass = sha1(PRE_PASSWD . $pass . POST_PASSWD);
		
		$sql = "SELECT user_is_valid('$usr', '$pass')";	
		
		$res = $this->_mysqli->query($sql);
		$row = $res->fetch_row(); 
		$res->close();
		$this->closeConnection();

		return ($row[0] > 0 ? true : false);
	}
	
	public function loadByRoleName($role) {	
		$this->makeConnection();
		$role = $this->_mysqli->real_escape_string($role);
		
		$sql = "CALL user_load_by_role_name('$role')";
		
		$res = $this->_mysqli->query($sql);
		
		$data = array();
		while ($row = $res->fetch_assoc()) {
			array_push($data, $row);
		}
		
		$res->close();
		$this->closeConnection();
		
		return $data;
	}
	
	public function loadByActive($act) {	
		$this->makeConnection();
		
		$sql = "CALL user_load_by_active($act)";
		
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
		
		$sql = "CALL user_load_all()";
		
		$res = $this->_mysqli->query($sql);
		
		$data = array();
		while ($row = $res->fetch_assoc()) {
			array_push($data, $row);
		}
		
		$res->close();
		$this->closeConnection();
		
		return $data;
	}
	
	public function loadByUserId($usr) {	
		$this->makeConnection();
		$usr = $this->_mysqli->real_escape_string($usr);
		
		$sql = "CALL user_load_by_user_id('$usr')";
		
		$res = $this->_mysqli->query($sql);

		if ($row = $res->fetch_assoc()) {
			$this->_id =  $row["id"];
			$this->_user_id = $row["user_id"];
			$this->_user_name = $row["user_name"];
			$this->_passwd = $row["passwd"];
			$this->_email = $row["email"];
			$this->_branch_code = $row["branch_code"];
			$this->_departement = $row["departement"];
			$this->_role_name = $row["role_name"];
			$this->_active = $row["active"];
			$this->_createdBy = $row["created_by"];
			$this->_createdDate = $row["created_date"];
			$this->_lastUser = $row["last_user"];
			$this->_lastUpdate = $row["last_update"];
		}
		
		$res->close();
		$this->closeConnection();		
	}
		
	public function load($id) {	
		$this->makeConnection();
		
		$sql = "CALL user_load($id)";
		
		$res = $this->_mysqli->query($sql);

		if ($row = $res->fetch_assoc()) {
			$this->_id = $id;
			$this->_user_id = $row["user_id"];
			$this->_user_name = $row["user_name"];
			$this->_passwd = $row["passwd"];
			$this->_email = $row["email"];
			$this->_branch_code = $row["branch_code"];
			$this->_departement = $row["departement"];
			$this->_role_name = $row["role_name"];
			$this->_active = $row["active"];
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
		
		$sql = "CALL user_remove($id)";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function updateWithModifyPasswd($id) {
		$this->makeConnection();
		$this->makeEscapedString();		
		
		$sql = "CALL user_update_with_modify_passwd(" .
					$id . ", '" .
					$this->_user_id . "', '" .
					$this->_user_name . "', '" .
					$this->_email . "', '" .
					$this->_branch_code . "', '" .
					$this->_departement . "', '" .
					$this->_role_name . "', " .
					$this->_active . ", '" .
					#md5(PRE_PASSWD . $this->_passwd . POST_PASSWD) . "', '" .
					sha1(PRE_PASSWD . $this->_passwd . POST_PASSWD) . "', '" .
					$this->_lastUser . "', '" .
					$this->_lastUpdate . 
				"')";		
				
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function update($id) {
		$this->makeConnection();
		$this->makeEscapedString();		
		
		$sql = "CALL user_update(" .
					$id . ", '" .
					$this->_user_id . "', '" .
					$this->_user_name . "', '" .
					$this->_email . "', '" .
					$this->_branch_code . "', '" .
					$this->_departement . "', '" .
					$this->_role_name . "', " .
					$this->_active . ", '" .
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
		
		$sql = "CALL user_add('" . 
					$this->_user_id . "', '" .
					$this->_user_name . "', '" .
					#md5(PRE_PASSWD . $this->_passwd . POST_PASSWD) . "', '" .
					sha1(PRE_PASSWD . $this->_passwd . POST_PASSWD) . "', '" .
					$this->_email . "', '" .
					$this->_branch_code . "', '" .
					$this->_departement . "', '" .
					$this->_createdBy . "', '" .
					$this->_createdDate . 
				"')";
		
		$ret = $this->_mysqli->query($sql);
		$this->generateErrorMessage($ret);
		$this->closeConnection();

		return $ret;
	}
	
	public function isExist($usr) {	
		$this->makeConnection();
		$usr = $this->_mysqli->real_escape_string($usr);
		
		$sql = "SELECT user_count('$usr')";	
			
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
	public function getUserId() {
		return $this->_user_id;
	}
	public function getUserName() {
		return $this->_user_name;
	}
	public function getPasswd() {
		return $this->_passwd;
	}
	public function getEmail() {
		return $this->_email;
	}
	public function getBranchCode() {
		return $this->_branch_code;
	}
	public function getDepartement() {
		return $this->_departement;
	}
	public function getRoleName() {
		return $this->_role_name;
	}
	public function getActive() {
		return $this->_active;
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
	public function setUserId($v) {
		$this->_user_id = $v;
	}
	public function setUserName($v) {
		$this->_user_name = $v;
	}
	public function setPasswd($v) {
		$this->_passwd = $v;
	}
	public function setEmail($v) {
		$this->_email = $v;
	}
	public function setBranchCode($v) {
		$this->_branch_code = $v;
	}
	public function setDepartement($v) {
		$this->_departement = $v;
	}
	public function setRoleName($v) {
		$this->_role_name = $v;
	}
	public function setActive($v) {
		$this->_active = $v;
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