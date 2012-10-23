<?php

/**
 * This file is part of the Workapp project.
 *
 * Chat Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Chat\Model;

use Engine\Modules\Model;

/**
 * Model\Chat class
 *
 * Класс для записи логов. Увесдомляет пользователей о приглашениях в комнаты
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Logs extends Model {
	
	/**
	 * Отправить всем сообщение
	 * 
	 * @param string $string
	 */
	function addChatMessageAll($string) {		 
		$users = $this->registry["user"]->getUsersList();
		 
		foreach($users as $user) {
			if ($user["id"] != $this->registry["ui"]["id"]) {
				$this->registry["logs"]->uid = $user["id"];
				$this->registry["logs"]->set("service", $string, "");
			}
		}
	}
	
	/**
	 * Отправить группе пользователей сообщение
	 *
	 * @param int $gid
	 * @param string $string
	 */
	function addChatMessageGroup($gid, $string) {
		$users = $this->registry["user"]->getUserInfoFromGroup($gid);
		 
		foreach($users as $user) {
			if ($user["uid"] != $this->registry["ui"]["id"]) {
				$this->registry["logs"]->uid = $user["uid"];
				$this->registry["logs"]->set("service", $string, "");
			}
		}
	}
	
	/**
	 * Отправить пользователю сообщение
	 *
	 * @param int $uid
	 * @param string $string
	 */
	function addChatMessageUser($uid, $string) {
		if ($uid != $this->registry["ui"]["id"]) {
			$this->registry["logs"]->uid = $uid;
			$this->registry["logs"]->set("service", $string, "");
		}
	}
}