<?php
class Chat_Public_Functions extends Modules_Functions {
	private $chat;
	
	function __construct($config) {
		parent::__construct($config);
		
		$this->chat = $this->registry["chat"];
	}
	
	public function getChatsRoom() {
		return $this->chat->getChatsRoom();
	}
	
	public function getRenderRooms() {
		return $this->chat->getRenderRooms();
	}
}
?>