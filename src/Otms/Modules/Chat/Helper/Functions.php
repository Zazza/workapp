<?php

/**
 * This file is part of the Workapp project.
 *
 * Chat Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Chat\Helper;

use Engine\Modules;

/**
 * Chat Helper class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Functions extends Modules\Functions {
	/**
	 * Model_Chat()
	 *
	 * @var object
	 */
	private $chat;
	
	function __construct($config) {
		parent::__construct($config);
		
		$this->chat = $this->registry["chat"];
	}
	
	/**
	 * Получить список всех комнат
	 * 
	 * @return array
	 */
	public function getChatsRoom() {
		return $this->chat->getChatsRoom();
	}
	
	/**
	 * Getter Получить список всех комнат (html рендер)
	 * Вызов геттера $this->chat->getChatsRoom()
	 * 
	 * @return array
	 */
	public function getRenderRooms() {
		return $this->chat->getRenderRooms();
	}
}
?>