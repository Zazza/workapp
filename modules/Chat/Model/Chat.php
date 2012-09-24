<?php
class Model_Chat extends Modules_Model {
	private $cid = null;
	private $online_time = 60;
	private $time = 300;
	private $renderRooms = array();
	private $db;
	
	public function __construct($config) {
		parent::__construct($config);
		
		$this->db = new Model_Db_Chat();
	}

	public function getRenderRooms() {
		return $this->renderRooms;
	}
	
	public function getChatsRoom() {
		$db = new Model_Db_Chat();
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
	
	public function addChatRoom($name, $parts) {
		return $this->db->addChatRoom($name, $parts);
	}
	
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
	
	private function getStatus($cid, $uid) {
		$this->memcached->set("chat[" . $cid . "]" . $uid);
		if ($this->memcached->load()) {
			return true;
		} else {
			return false;
		}
	}
	
	private function delChatPart($cid, $uid) {
		$this->memcached->set("chat[" . $cid . "]" . $uid);
		$this->memcached->delete();
	}
	
	private function commands($messages, $first = false) {
		$command = new Model_ChatCommands($this->config);
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
	
	public function getParts() {
		if ($this->cid != null) {
			$parts = $this->db->getChatPart($this->cid);
				
			$this->memcached->set("parts_chat[" . $this->cid . "]" . $this->registry["ui"]["id"]);
			$this->memcached->save($parts);
				
			return $parts;
		}
	}
	
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
	
	public function addMessage($message) {
		if ($this->cid != null) {
			$this->db->addMessage($this->cid, $message);
		}
	}
	
	public function getChatRoom($id) {
		if ($this->cid != null) {
			return $this->db->getChatRoom($id);
		}
	}
}
?>