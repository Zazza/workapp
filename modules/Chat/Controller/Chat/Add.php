<?php
class Controller_Chat_Add extends Modules_Controller {

	public function index() {
		$class_logs = new Components_Logs();

		$chat = $this->registry["chat"];

		$this->view->setTitle("Новый чат");
			
		if (isset($this->get["add"])) {
			if ($this->get["chatname"] == null) {
				$name = "чат_" . date("H:i:s Y-m-d");
			} else { $name = $this->get["chatname"];
			}
			$parts = $this->get;
			unset($parts["chatname"]); unset($parts["add"]);
			$parts["ruser"][] = $this->registry["ui"]["id"];

			$parts_json = json_encode($parts);

			$chatid = $chat->addChatRoom($name, $parts_json);

			$room["id"] = $chatid;
			$room["name"] = $name;
			foreach($parts as $key=>$val) {
				switch ($key) {
					case "rall":
						$class_logs->addChatMessageAll($room);
							
						break;
					case "gruser":
						foreach($val as $gid) {
							$class_logs->addChatMessageGroup($gid, $room);
						}
							
						break;
					case "ruser":
						foreach($val as $uid) {
							$class_logs->addChatMessageUser($uid, $room);
						}
							
						break;
				}
			}

			$this->view->refresh(array("timer" => "1", "url" => "chat/?id=" . $chatid));
		}

		$this->view->showPage();
	}
}
?>