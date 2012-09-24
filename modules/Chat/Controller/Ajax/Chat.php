<?php
class Controller_Ajax_Chat extends Modules_Ajax {
	private $chat;
	
	public function __construct($config) {
		parent::__construct($config);

		$this->chat = $this->registry["chat"];
	}
	
	public function addMessage($params) {
		$cid = $params["cid"];
		$message = htmlspecialchars($params["message"]);

		$this->chat->set($cid);
		$this->chat->addMessage($message);
		
		echo $this->view->render("chat_message", array("author" => $this->registry["ui"], "date" => date("H:i:s"), "message" => $message));
	}
	
	public function getFirst($params) {
		$cid = $params["cid"];
		
		$this->chat->set($cid);
		
		$parts = $this->chat->getParts();
		$data["parts"] = null;
		foreach($parts as $part) {
			$data["parts"] .= $this->view->render("chat_part", array("data" => $this->registry["user"]->getUserInfo($part["uid"])));
		}
		
		$body = $this->chat->getBody(true);
		$data["body"] = null;
		if ((isset($body)) and (count($body) > 0)) {
			foreach($body as $part) {
				if ($part["text"] != null) {
					$who = null;
					if ( (isset($part["who"])) and (is_numeric($part["who"])) ) {
						$who = $this->registry["user"]->getUserInfo($part["who"]);
					}
					
					$data["body"] .= $this->view->render("chat_message", array("author" => $who, "date" => date("H:i:s", strtotime($part["timestamp"])), "message" => $part["text"]));
				}
			}
		}
		
		echo json_encode($data);
	}
	
	public function getInstance($params) {
		$cid = $params["cid"];
		
		$this->chat->set($cid);
		
		$body = $this->chat->getBody();
		$data["body"] = null;
		if ((isset($body)) and (count($body) > 0)) {
			foreach($body as $part) {
				if ($part["text"] != null) {
					$who = null;
					if ( (isset($part["who"])) and (is_numeric($part["who"])) ) {
						$who = $this->registry["user"]->getUserInfo($part["who"]);
					}
					
					$data["body"] .= $this->view->render("chat_message", array("author" => $who, "date" => date("H:i:s", strtotime($part["timestamp"])), "message" => $part["text"]));
				}
			}
		}
		
		$parts = $this->chat->getDiffParts();
		$data["addparts"] = null; $data["delparts"] = null;
		foreach($parts as $key=>$val) {
			if ($key == "add") {
				if ((isset($val)) and (count($val) > 0)) {
					foreach($val as $part) {
						$data["addparts"] .= $this->view->render("chat_part", array("data" => $this->registry["user"]->getUserInfo($part)));
					}
				}
			}
			
			if ($key == "delete") {
				if ((isset($val)) and (count($val) > 0)) {
					foreach($val as $part) {
						$data["delparts"] .= $part;
					}
				}
			}
		}
		
		echo json_encode($data);
	}
}
?>
    