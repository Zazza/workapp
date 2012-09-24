<?php
class Controller_Chat extends Modules_Controller {

	public function index() {

		$chat = $this->registry["chat"];

		$cid = $this->get["id"];

		if ($chat->set($cid)) {
			$ch = $chat->getChatRoom($this->get["id"]);
			$this->view->setTitle("Чат " . $ch[0]["name"]);

			$this->view->chat_room(array("cid" => $cid));
		}

		$this->view->showPage();
	}
}
?>