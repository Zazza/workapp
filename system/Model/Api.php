<?php
class Model_Api extends Engine_Model {
	private $_login = null;
	private $_password = null;
	
	function login() {
		if (isset($_REQUEST["login"])) {
			$this->_login = $_REQUEST["login"];
		} else {
			return false;
		}

		if (isset($_REQUEST["password"])) {
			$this->_password = $_REQUEST["password"];
		} else {
			return false;
		}

		 $sql = "SELECT u.id, p.group
                FROM users AS u
                LEFT JOIN users_priv AS p ON (p.id = u.id)
                WHERE u.login = :login AND u.pass = :password
                LIMIT 1";

		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":login" => $this->_login, ":password" => md5($this->_password));
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($data) == 1) {
			$this->registry["ui"] = $data[0];
			return true;
		} else {
			return false;
		}
	}
}
?>