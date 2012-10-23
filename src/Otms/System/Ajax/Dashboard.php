<?php

/**
 * This file is part of the Workapp project.
*
* (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
*
*/

namespace Otms\System\Ajax;

use Engine\Ajax;
use Otms\System\Model;
use Otms\System\Model\Ui;

/**
 * Dashboard Ajax class
 *
 * Класс ajax-контроллер получающий информацию о событиях на странице dashboard и выпадающем dashboard вверху страницы
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */

class Dashboard extends Ajax {
	/**
	 * private переменная для доступа к модели Dashboard
	* @var object new Model\Dashboard()
	*/
	private $dashboard;
	
	/**
	 * Количество событий
	* @var int
	*/
	private $numEvents = 0;
	
	/**
	 * private переменная показывающая есть ли системные уведомления в событиях
	* *Важные уведомления выделяемые цветом
	* @var boolean
	*/
	private $service = false;
	
	/**
	 * Конструктор
	* Определяет переменную $dashboard
	*/
	function __construct() {
		parent::__construct();
		$this->dashboard = new Model\Dashboard();
	}
	
	/**
	 * Функция вызывается при прокрутке страницы соыбтий (ajax) - получает следующие события
	 * 
	 * @return string
	 * (.../dashboard/events/event.tpl) или 'end' если событий нет
	 */
	function scroll() {
		$sess = & $_SESSION["dashboard"];
		
		$dashboard = new Model\Dashboard();
		
		$listevents = $dashboard->getEventsWithoutSess();
		
		$content = NULL;
		if (is_array($listevents)) {			
			$list = null;
			
			foreach($listevents as $event) {
				$content .= $this->view->render("dashboard_events_event", array("event" => $event));
			}
		} else {
			$content = 'end';
		}
		
		return $content;
	}
	
	/**
	 * Обнуление массива $_SESSION["dashboard"]
	 */
	function reset() {
		unset($_SESSION["dashboard"]);
	}
	
	/**
	 * Заполнение массива $_SESSION["dashboard"]
	 * 
	 * @param array $params:
	 *    boolean $params["task"]
	 *    boolean $params["com"]
	 *    boolean $params["obj"]
	 *    boolean $params["info"]
	 *    boolean $params["mail"]
	 *    boolean $params["service"]
	 *    string $params["filtr"]
	 */
	function setNotify($params) {
		$dashboard = & $_SESSION["dashboard"];
	
		if ($params["date"] == "") {
			$dashboard["date"] = date("Ymd");
		} else {
			$dashboard["date"] = $params["date"];
		}
	
		$dashboard["task"] = $params["task"];
		$dashboard["com"] = $params["com"];
		$dashboard["obj"] = $params["obj"];
		$dashboard["info"] = $params["info"];
		$dashboard["mail"] = $params["mail"];
		$dashboard["service"] = $params["service"];
		$dashboard["filtr"] = $params["filtr"];
	}
	
	/**
	 * Получение новых событий для выпадающего dashboard на панели вверху страницы
	 * 
	 * Возвращет JSON строку событий
	 * Одно событие: .../dashboard/events/dash.tpl
	 */
	function newevents() {
		$arr_events = null;

		$listevents = $this->dashboard->getNewEvents();
		
		if (count($listevents) > 0) {
			
			$max = $listevents[0]["id"];
			for($i=0; $i<count($listevents); $i++) {
				if ($max < $listevents[$i]["id"]) { $max = $listevents[$i]["id"]; }
			}
			
			$this->registry["logs"]->addLastDashId($max);
		}
		
		foreach($listevents as $event) {
			if ($event["type"] == "service") {
				$this->service = true;
			}
			
			$this->numEvents++;
			$arr_events .= $this->view->render("dashboard_events_dash", array("event" => $event));
		}
		
		if ($this->service) { $dash["service"] = true; }
		$dash["events"] = $arr_events;
		$dash["notify"] = $this->numEvents;
		
		if (isset($this->registry["module_chat"])) {
			$chat = $this->registry["module_chat"];
			$rooms = $chat->getChatsRoom();
			$dash["rooms"] = $chat->getRenderRooms();
			$dash["numChats"] = count($rooms);
		}
		
		return json_encode($dash);
	}
	
	/**
	 * Закрывает событие для выпадающего dashboard на панели вверху страницы
	 * 
	 * @param array $params:
	 *    int $params["eid"] - ID события
	 * 
	 * Возвращает TRUE или FALSE в завсисмости от наличия service событий
	 * (нужно для выделения цветом, если service событие есть)
	 */
	function closeEvent($params) {
		$eid = $params["eid"];
		
		$this->dashboard->closeEvent($eid);
		
		$this->dashboard->findEvents();
		
		if ($this->dashboard->getServiceVar()) {
			$service = true;
		} else {
			$service = false;
		}
		
		return $service;
	}
	
	/**
	 * Удалить все события для выпадающего dashboard на панели вверху страницы
	 */
	function clearEvents() {
		$events = $this->dashboard->getDashEvents();
		foreach($events as $event) {
			$this->dashboard->closeEvent($event["id"]);
		}
	}
	
	/**
	 * Сохраняет настройки для вывода типа событий для выпадающего dashboard на панели вверху страницы
	 * @param array $params:
	 *    boolean $params["task"]
	 *    boolean $params["com"]
	 *    boolean $params["obj"]
	 *    boolean $params["info"]
	 *    boolean $params["mail"]
	 */
	function saveNotice($params) {
		$ui = new Ui();
		
		$res["task"] = $params["task"];
		$res["com"] = $params["com"];
		$res["mail"] = $params["mail"];
		$res["obj"] = $params["obj"];
		$res["info"] = $params["info"];
		
		$res = json_encode($res);
		
		if (is_array($this->registry["ajax_notice_sets"])) {
			$ui->setSet("ajax_notice", $res);
		} else {
			$ui->addSet("ajax_notice", $res);
		}
	}
}