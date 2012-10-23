<?php

/**
 * This file is part of the Workapp project.
 *
 * Chat Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Chat\Controller\Chat;

use Engine\Modules\Controller;
use Otms\Modules\Chat\Model\Logs;

/**
 * Controller\Chat\Add class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Add extends Controller {

	public function index() {
		$class_logs = new Logs();

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
						$string = $this->view->render("logs_invite", array("room" => $room));
						$class_logs->addChatMessageAll($string);							
						break;
					case "gruser":
						foreach($val as $gid) {
							$string = $this->view->render("logs_invite", array("room" => $room));
							$class_logs->addChatMessageGroup($gid, $string);
						}
							
						break;
					case "ruser":
						foreach($val as $uid) {
							$string = $this->view->render("logs_invite", array("room" => $room));
							$class_logs->addChatMessageUser($uid, $string);
						}
							
						break;
				}
			}

			$this->view->refresh(array("timer" => "1", "url" => "chat/?id=" . $chatid));
		}

		$this->view->showPage();
	}
}