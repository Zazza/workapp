<?php

/**
 * This file is part of the Workapp project.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\System\Model;

use Engine\Model;
use PDO;

/**
 * API Model class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */

class Api extends Model {

	/**
	 * Обеспечивает авторизацию клиентов.
	 *
	 * @param string $_REQUEST["login"]
	 * @param string $_REQUEST["password"]
	 *
	 * @return True or False. В случае успшно выполненной авторизации заполняет массив $this->registry["ui"]
	 */
	
	function login() {
		$login = NULL;
		$password = NULL;
		
		if (isset($_REQUEST["login"])) {
			$login = $_REQUEST["login"];
		} else {
			return false;
		}

		if (isset($_REQUEST["password"])) {
			$password = $_REQUEST["password"];
		} else {
			return false;
		}

		 $sql = "SELECT u.id, p.group
                FROM users AS u
                LEFT JOIN users_priv AS p ON (p.id = u.id)
                WHERE u.login = :login AND u.pass = :password
                LIMIT 1";

		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":login" => $login, ":password" => md5($password));
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