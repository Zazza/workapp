<?php

/**
 * This file is part of the Workapp project.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\System\Component;

use Engine\Singleton;
use Otms\System\Model;

/**
 * PostController class
 * 
 * Выполняется после Контроллера, до отображения страницы.
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class PostController extends Singleton {
	/**
	* Экземпляр вида
	* 
	* @var object
	 */
	private $view;
	
	function __construct() {
		parent::__construct();
		
		$this->view = $this->registry["view"];
	}
	
	/**
	 * Список методов для выполнения
	 */
	public function run() {
		$this->dashboard();
		$this->users();
		$this->fa();
	}
	
	/**
	 * Получение событий в dashboard и чатов
	 */
	private function dashboard() {
		$ui = new Model\Ui();
		
		$dashboard = new Model\Dashboard();
			
		$dash["events"] = $dashboard->findEvents();
			
		if ($dashboard->getServiceVar()) {
			$dash["service"] = true;
		}
			
		$dash["notify"] = $dashboard->getnumEventsVar();
			
		if (count($dash["events"]) == 0) {
			$dash["events"] = "<p id='emptyEvents'>Новых событий нет</p>";
		}
			
		if (isset($this->registry["module_chat"])) {
			$chat = $this->registry["module_chat"];
			$rooms = $chat->getChatsRoom();
			$dash["rooms"] = $chat->getRenderRooms();
			$dash["numChats"] = count($rooms);
		}
			
		$dash = json_encode($dash);
			
		$this->view->setBottomPanel($this->view->render("fastmenu_dashboard", array("dash" => $dash)), 0);
	}
	
	/**
	 * Получение списка пользователей и открытие-закрытие списка, согласно сету 'bu'.
	 * Данный список пользователей доступен на панели вверху страницы.
	 * Шаблон: .../fastmenu/users.tpl
	 */
	private function users() {
		$json = $this->registry["user"]->getUserList();
		$json["listUsers"] = $this->view->render("users_bplist", array("listUsers" => $json["listUsers"]));
		$ulist = json_encode($json);
		$this->view->setBottomPanel($this->view->render("fastmenu_users", array("ulist" => $ulist)), 1);
	}
	
	/**
	 * Создание JS автоматического загрузчика файлов http://github.com/valums/file-uploader
	 */
	private function fa() {
		$content = $this->view->render("fa_content", array());
		$this->view->setContent($content);
	}
}