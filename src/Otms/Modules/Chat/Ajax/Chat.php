<?php

/**
 * This file is part of the Workapp project.
 * 
 * Chat Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Chat\Ajax;

use Engine\Modules\Ajax;

/**
 * Ajax\Chat class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Chat extends Ajax {
	/**
	* Model_Chat()
	*
	* @var object
	*/
	private $chat;
	
	public function __construct($config) {
		parent::__construct($config);

		$this->chat = $this->registry["chat"];
	}
	
	/**
	* Отправить сообщение в чат
	*
	* @param $params
	*    int $params["cid"] - ID текущей комнаты чата
	*    string $params["message"] - сообщение
	* @return string (twig render) - рендер строки в окне чата
	*/
	public function addMessage($params) {
		$cid = $params["cid"];
		$message = htmlspecialchars($params["message"]);

		$this->chat->set($cid);
		$this->chat->addMessage($message);
		
		return $this->view->render("chat_message", array("author" => $this->registry["ui"], "date" => date("H:i:s"), "message" => $message));
	}
	
	/**
	* Получить участников чата (комнаты) и последние сообщения при первом входе в чат
	* 
	* @param $params
	*    int $params["cid"] - ID текущей комнаты чата
	* @return string (twig render) - рендер чата
	 */
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
		
		return json_encode($data);
	}
	
	/**
	* Получить новые сообщения в чате (комнате) и изменение в участниках чата
	* 
	* @param $params
	*    int $params["cid"] - ID текущей комнаты чата
	* @return string (twig render) - рендер чата
	*/
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
		
		return json_encode($data);
	}
}   