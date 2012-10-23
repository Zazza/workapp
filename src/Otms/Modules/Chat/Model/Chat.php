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
use Otms\Modules\Chat\Model\Db;

/**
 * Model\Chat class
 * 
 * Класс для управления чатом
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Chat extends Model {
	/**
	 * ID созданной комнаты чата
	 * 
	 * @var int
	 */
	private $cid = NULL;
	
	/**
	 * Время в течении которого комната считается "жива".
	 * Время начинает уменьшаться, если в конате нет участников
	 * 
	 * @var int
	 */
	private $online_time = 60;
	
	/**
	 * Время в секундах за которое новый участник чата получит прошлые сообщения, при первом входу в комнату
	 * 
	 * @var int
	 */
	private $time = 300;
	
	/**
	 * Список существующих комнат
	 * 
	 * @var array
	 */
	private $renderRooms = array();
	
	/**
	 * Объект Db\Chat()
	 * 
	 * @var object
	 */
	private $db;
	
	public function __construct($config) {
		parent::__construct($config);
		
		$this->db = new Db\Chat();
	}

	/**
	 * Getter Получить список всех комнат (html рендер)
	 * 
	 * @return array
	 */
	public function getRenderRooms() {
		return $this->renderRooms;
	}
	
	/**
	 * Получить список всех комнат
	 * 
	 * @return array - обычный массив
	 *    $this->renderRooms - twig render (.../chat/chat.tpl)
	 *    получить можно через getter getRenderRooms()
	 */
	public function getChatsRoom() {
		$db = new Db\Chat();
		$parts = $db->getChatsPart();
		foreach($parts as $part) {
			if (!self::getStatus($part["cid"], $part["uid"])) {
				$this->db->delChatPart($part["id"], $part["cid"]);
				self::delChatPart($part["cid"], $part["id"]);
			}
		}
	
		$arr_chats = null;
	
		$row = $db->getChatsRoom();
	
		foreach($row as $part) {
			$parts = json_decode($part["parts"], true);
				
			if (self::rightChat($parts)) {
				$arr_chats[$part["id"]] = $part["name"];
	
				$this->renderRooms[] = $this->render("chat_chat", array("room" => $part));
			}
		}
	
		return $arr_chats;
	}
	
	/**
	 * В зависимости от того, разрешено или нет текущему пользователю видеть комнату вернуть boolean переменную
	 * 
	 * @param array $parts
	 * @return boolean
	 */
	private function rightChat($parts) {
		$flag = false;
	
		if (isset($parts)) {
				
			foreach($parts as $key=>$val) {
				if ($key == "rall") {
					if ($val) {
						$flag = true;
					}
				}
					
				if ($key == "gruser") {
					foreach($val as $gid) {
						if ($gid == $this->registry["ui"]["gid"]) {
							$flag = true;
						}
					}
				}
					
				if ($key == "ruser") {
					foreach($val as $uid) {
						if ($uid == $this->registry["ui"]["id"]) {
							$flag = true;
						}
					}
				}
			}
		}
	
		return $flag;
	}
	
	/**
	 * Проверка, хватает ли прав у текущего пользователя для доступа к комнате.
	 * Если да - $this->reg()
	 * 
	 * @param int $cid - ID комнаты
	 * @return boolean
	 */
	public function set($cid) {
		$parts = null;

		$row = $this->db->getChatRoom($cid);
	
		if (isset($row[0]["parts"])) {
			$parts = json_decode($row[0]["parts"], true);
		}
	
		if ($this->rightChat($parts)) {
			$this->cid = $cid;
			$this->reg();
				
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Создать комнату
	 * 
	 * @param string $name - имя комнаты
	 * @param JSON array $parts - массив участников
	 */
	public function addChatRoom($name, $parts) {
		return $this->db->addChatRoom($name, $parts);
	}
	
	/**
	 * Добавить пользователя к участникам комнаты
	 */
	private function reg() {
		if ($this->cid != null) {
			$this->db->addChatPart($this->cid);
				
			$this->memcached->set("chat[" . $this->cid . "]" . $this->registry["ui"]["id"]);
			if ($this->memcached->load()) {
				$this->memcached->delete();
			}
				
			$this->memcached->saveTime("online", $this->online_time);
		}
	}
	
	/**
	 * Проверка статуса пользователя в чате
	 * 
	 * @param int $cid
	 * @param int $uid
	 * @return boolean
	 *    TRUE - online
	 *    FALSE - offline
	 */
	private function getStatus($cid, $uid) {
		$this->memcached->set("chat[" . $cid . "]" . $uid);
		if ($this->memcached->load()) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Удалить пользователя из участников чата
	 * 
	 * @param int $cid
	 * @param int $uid
	 */
	private function delChatPart($cid, $uid) {
		$this->memcached->set("chat[" . $cid . "]" . $uid);
		$this->memcached->delete();
	}
	
	/**
	 * Отправить команду в чат (пример: /private)
	 * 
	 * @param string $messages
	 * @param boolean $first
	 * @return string
	 */
	private function commands($messages, $first = false) {
		$command = new ChatCommands($this->config);
		$count = count($messages);
	
		for($i=0; $i<$count; $i++) {
			$result = $command->set($messages[$i]["text"]);
	
			if ($result) {
				if ($messages[$i]["who"] == $this->registry["ui"]["id"]) {
					if (!$first) {
						$text = $command->get();
						if ($text != null) {
							$messages[$i]["text"] = $text;
							unset($messages[$i]["who"]);
								
							$this->db->delMessage($messages[$i]["id"]);
						}
					} else {
						unset($messages[$i]);
					}
				} else {
					unset($messages[$i]);
				}
			} else {
				if ($messages[$i]["who"] == $this->registry["ui"]["id"]) {
					if (!$first) {
						unset($messages[$i]);
					}
				} else {
					$text = $command->get();
					if ($text != null) {
						$messages[$i]["text"] = $text;
					}
				}
			}
		}

		return $messages;
	}
	
	/**
	 * Получить текст текущего чата (комнаты)
	 * 
	 * @param boolean $first - первый вход пользователя?
	 */
	public function getBody($first = false) {
		if ($this->cid != null) {
	
			$this->memcached->set("update_chat[" . $this->cid . "]" . $this->registry["ui"]["id"]);
				
			if ($this->memcached->load()) {
				$update = $this->memcached->get();
			} else {
				$update = 0;
			}
				
			if ($first) {
				$messages = $this->db->getNumMessages($this->cid, $this->time);
			} else {
				$messages = $this->db->getMessages($this->cid, $update);
			}
	
			if (count($messages) > 0) {
				$this->memcached->save($messages[count($messages)-1]["id"]);
			}
				
			$clear_mes = $this->commands($messages, $first);
				
			return $clear_mes;
		}
	}
	
	/**
	 * Получить список участников чата
	 * 
	 * @return array
	 */
	public function getParts() {
		if ($this->cid != null) {
			$parts = $this->db->getChatPart($this->cid);
				
			$this->memcached->set("parts_chat[" . $this->cid . "]" . $this->registry["ui"]["id"]);
			$this->memcached->save($parts);
				
			return $parts;
		}
	}
	
	/**
	 * Обновить участников чата (комнаты)
	 * 
	 * $this->cid - ID комнаты
	 * @return array
	 *    array["add"] = UID
	 *    array["del"] = UID
	 */
	public function getDiffParts() {
		if ($this->cid != null) {
				
			// START update online time current user
			$this->memcached->set("chat[" . $this->cid . "]" . $this->registry["ui"]["id"]);
			if ($this->memcached->load()) {
				$this->memcached->delete();
			}
	
			$this->memcached->saveTime("online", $this->online_time);
			// END update online time current user
				
				
			$this->memcached->set("parts_chat[" . $this->cid . "]" . $this->registry["ui"]["id"]);
			if ($this->memcached->load()) {
				$last_parts = $this->memcached->get();
			}
				
			$this->memcached->set("parts_chat[" . $this->cid . "]" . $this->registry["ui"]["id"]);
			$new_parts = $this->db->getChatPart($this->cid);
			$this->memcached->save($new_parts);
				
			$add = null; $del = null;
				
			for($i=0; $i<count($new_parts); $i++) {
				$flag = true;
				for($j=0; $j<count($last_parts); $j++) {
					if ($new_parts[$i]["uid"] == $last_parts[$j]["uid"]) {
						$flag = false;
					};
				}
				if ($flag) {
					$add[] = $new_parts[$i]["uid"];
				};
			}
				
			$diff_parts["add"] = $add;
				
			for($i=0; $i<count($last_parts); $i++) {
				$flag = true;
				for($j=0; $j<count($new_parts); $j++) {
					if ($last_parts[$i]["uid"] == $new_parts[$j]["uid"]) {
						$flag = false;
					};
				}
				if ($flag) {
					$del[] = $last_parts[$i]["uid"];
				};
			}
				
			$diff_parts["delete"] = $del;
	
			return $diff_parts;
		}
	}
	
	/**
	 * Отправить сообщение в чат (комнату)
	 * 
	 * @param string $message
	 */
	public function addMessage($message) {
		if ($this->cid != null) {
			$this->db->addMessage($this->cid, $message);
		}
	}
	
	/**
	 * Получить информаию о чате (комнате)
	 * 
	 * @param int $id - ID комнаты
	 */
	public function getChatRoom($id) {
		if ($this->cid != null) {
			return $this->db->getChatRoom($id);
		}
	}
}
?>