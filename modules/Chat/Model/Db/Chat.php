<?php
class Model_Db_Chat extends Modules_Model {
	public function addChatRoom($name, $parts) {
		$sql = "INSERT INTO chat_room (name, parts) VALUES (:name, :parts)";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":name" => $name, ":parts" => $parts);
		$res->execute($param);
		
		$chatid = $this->registry['db']->lastInsertId();

		return $chatid;
	}
	
	private function delChatRoom($id) {
		$sql = "DELETE FROM chat_room WHERE id = :id LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
	}
	
	public function getChatsRoom() {
		$sql = "SELECT id, name, parts FROM chat_room ORDER BY id";
	
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$row = $res->fetchAll(PDO::FETCH_ASSOC);

		return $row;
	}
	
	public function getChatRoom($id) {
		$sql = "SELECT id, name, parts FROM chat_room WHERE id = :id LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
	
		return $row;
	}
	
	public function addChatPart($cid) {
		$sql = "INSERT INTO chat_room_part (cid, uid) VALUES (:cid, :uid)";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":cid" => $cid, ":uid" => $this->registry["ui"]["id"]);
		@$res->execute($param);
	}
	
	public function getChatsPart() {
		$sql = "SELECT id, cid, uid FROM chat_room_part ORDER BY id";
		
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $row;
	}
	
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
	
	public function getChatPart($cid) {
		$sql = "SELECT id, uid FROM chat_room_part WHERE cid = :cid ORDER BY id";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":cid" => $cid);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $row;
	}
	
	public function addMessage($cid, $message) {
		$sql = "INSERT INTO chat (cid, who, text) VALUES (:cid, :who, :text)";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":cid" => $cid, ":who" => $this->registry["ui"]["id"], ":text" => $message);
		$res->execute($param);
	}
	
	public function getMessages($cid, $update) {
		$sql = "SELECT id, `who`, `text`, `timestamp` FROM chat WHERE cid = :cid AND id > :id ORDER BY id";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":cid" => $cid, ":id" => $update);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);

		return $row;
	}
	
	public function getNumMessages($cid, $time) {
		$sql = "SELECT id, `who`, `text`, `timestamp` FROM chat WHERE cid = :cid AND timestamp > (NOW() - :time) ORDER BY id DESC";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":cid" => $cid, ":time" => $time);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
		$row = array_reverse($row);

		return $row;
	}
	
	public function delMessage($id) {
		$sql = "DELETE FROM chat WHERE id = :id LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
	}
}
?>