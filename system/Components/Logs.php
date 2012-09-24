<?php
class Components_Logs extends Engine_Model {
	function addChatMessageAll($params) {
		$room = $params[0];
		
		$string = $this->render("logs_invite", array("room" => $room));
		 
		$users = $this->registry["user"]->getUsersList();
		 
		foreach($users as $user) {
			if ($user["id"] != $this->registry["ui"]["id"]) {
				$this->registry["logs"]->uid = $user["id"];
				$this->registry["logs"]->set("service", $string, "");
			}
		}
	}
	
	function addChatMessageGroup($params) {
		$gid = $params[0];
		$room = $params[1];
		
		$string = $this->render("logs_invite", array("room" => $room));
		 
		$users = $this->registry["user"]->getUserInfoFromGroup($gid);
		 
		foreach($users as $user) {
			if ($user["uid"] != $this->registry["ui"]["id"]) {
				$this->registry["logs"]->uid = $user["uid"];
				$this->registry["logs"]->set("service", $string, "");
			}
		}
	}
	
	function addChatMessageUser($params) {
		$uid = $params[0];
		$room = $params[1];
		
		$string = $this->render("logs_invite", array("room" => $room));
		 
		if ($uid != $this->registry["ui"]["id"]) {
			$this->registry["logs"]->uid = $uid;
			$this->registry["logs"]->set("service", $string, "");
		}
	}
	
	function addEvent($params) {
		$this->registry["logs"]->uid = $params[0];
		$this->registry["logs"]->set("service", "Объект <a href='" . $this->registry["uri"] . "objects/" . $params[2] . "/'>" . $params[2] . "</a>: " . $params[1], "");
	}
}
?>