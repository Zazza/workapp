<?php

/**
 * This file is part of the Workapp project.
 *
 * Chat Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Chat\Controller;

use Engine\Modules\Controller;

/**
 * Controller\Chat class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Chat extends Controller {

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