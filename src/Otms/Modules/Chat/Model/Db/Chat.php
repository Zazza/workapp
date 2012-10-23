<?php

/**
 * This file is part of the Workapp project.
 *
 * Chat Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Chat\Model\Db;

use Engine\Modules\Model;
use PDO;

/**
 * Model\Chat class
 *
 * Класс чата для работы с БД
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Chat extends Model {
	
	/**
	 * Добавить комнату
	 * 
	 * @param string $name - имя
	 * @param array $parts - учатники
	 * @return array
	 */
	public function addChatRoom($name, $parts) {
		$sql = "INSERT INTO chat_room (name, parts) VALUES (:name, :parts)";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":name" => $name, ":parts" => $parts);
		$res->execute($param);
		
		$chatid = $this->registry['db']->lastInsertId();

		return $chatid;
	}
	
	/**
	 * Удалить комнату
	 * 
	 * @param int $id
	 */
	private function delChatRoom($id) {
		$sql = "DELETE FROM chat_room WHERE id = :id LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
	}
	
	/**
	 * Получить информацию о всех комнатах
	 * 
	 * @return array
	 */
	public function getChatsRoom() {
		$sql = "SELECT id, name, parts FROM chat_room ORDER BY id";
	
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$row = $res->fetchAll(PDO::FETCH_ASSOC);

		return $row;
	}
	
	/**
	 * Получить информацию о комнате
	 * 
	 * @param int $id
	 * @return array
	 */
	public function getChatRoom($id) {
		$sql = "SELECT id, name, parts FROM chat_room WHERE id = :id LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
	
		return $row;
	}
	
	/**
	 * Добавить текущего пользователя к комнате
	 * 
	 * @param int $cid
	 */
	public function addChatPart($cid) {
		$sql = "INSERT INTO chat_room_part (cid, uid) VALUES (:cid, :uid)";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":cid" => $cid, ":uid" => $this->registry["ui"]["id"]);
		@$res->execute($param);
	}
	
	/**
	 * Получить участников всех комнат
	 * 
	 * @return array
	 */
	public function getChatsPart() {
		$sql = "SELECT id, cid, uid FROM chat_room_part ORDER BY id";
		
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $row;
	}
	
	/**
	 * Удалить комнату
	 * 
	 * @param int $id
	 * @param int $cid
	 */
	public function delChatPart($id, $cid) {
		$sql = "DELETE FROM chat_room_part WHERE id = :id LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
		
		$sql = "SELECT COUNT(id) as count FROM chat_room_part WHERE cid = :cid";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":cid" => $cid);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
		
		if ($row[0]["count"] == 0) {
			self::delChatRoom($cid);
		}
	}
	
	/**
	 * Получить участников комнаты
	 * 
	 * @param int $cid
	 */
	public function getChatPart($cid) {
		$sql = "SELECT id, uid FROM chat_room_part WHERE cid = :cid ORDER BY id";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":cid" => $cid);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $row;
	}
	
	/**
	 * Отправить сообщение в чат (комнату
	 * )
	 * @param int $cid
	 * @param string $message
	 */
	public function addMessage($cid, $message) {
		$sql = "INSERT INTO chat (cid, who, text) VALUES (:cid, :who, :text)";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":cid" => $cid, ":who" => $this->registry["ui"]["id"], ":text" => $message);
		$res->execute($param);
	}
	
	/**
	 * Получить новые сообщения комнаты
	 * 
	 * @param int $cid - ID комнаты
	 * @param int $update - ID последнего сообщения
	 */
	public function getMessages($cid, $update) {
		$sql = "SELECT id, `who`, `text`, `timestamp` FROM chat WHERE cid = :cid AND id > :id ORDER BY id";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":cid" => $cid, ":id" => $update);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);

		return $row;
	}
	
	/**
	 * Получить последние сообщения чата за определённое время
	 * 
	 * @param int $cid
	 * @param string(date) $time
	 */
	public function getNumMessages($cid, $time) {
		$sql = "SELECT id, `who`, `text`, `timestamp` FROM chat WHERE cid = :cid AND timestamp > (NOW() - :time) ORDER BY id DESC";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":cid" => $cid, ":time" => $time);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
		$row = array_reverse($row);

		return $row;
	}
	
	/**
	 * Удалить сообщение
	 * 
	 * @param int $id - ID комнаты
	 */
	public function delMessage($id) {
		$sql = "DELETE FROM chat WHERE id = :id LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
	}
}