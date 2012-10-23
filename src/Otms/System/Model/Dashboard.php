<?php

/**
 * This file is part of the Workapp project.
*
* (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
*
*/

namespace Otms\System\Model;

use Engine\Model;
use Otms\System\Model\Db;
use Otms\System\Model\Logs;

/**
 * Dashboard Model class
 * 
 * Обеспечивает работу dashboard - информация о событиях в системе
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */

class Dashboard extends Model {
	/**
	 * private переменная для доступа к сессии
	 * @var object $_SESSION["dashboard"]
	 */
	private $dashboard;
	
	/**
	 * private переменная для доступа к модели Logs
	* @var object new Logs()
	*/
	private $logs;
	
	/**
	 * private переменная показывающая есть ли системные уведомления в событиях
	 * *Важные уведомления выделяемые цветом
	* @var boolean
	*/
	private $_service = false;
	
	/**
	 * Количество событий
	* @var int
	*/
	private $_numEvents = 0;
	
	/**
	 * Конструктор
	 * Определяет переменные $dashboard и $logs
	 */
	function __construct() {
		parent::__construct();
				
		$this->dashboard = & $_SESSION["dashboard"];
		$this->logs = new Logs();
	}
	
	/**
	 * Получает список НОВЫХ событий
	 * Ставит указатель на последнее полученное событие
	 * 
	 *  @return array $arr_events
	 */
	public function findEvents() {
		$arr_events = null;
	
		$listevents = $this->getDashEvents();
			
		if (count($listevents) > 0) {
	
			$max = $listevents[0]["id"];
			for($i=0; $i<count($listevents); $i++) {
				if ($max < $listevents[$i]["id"]) {
					$max = $listevents[$i]["id"];
				}
			}
	
			$this->logs->addLastDashId($max);
	
		}
	
		foreach($listevents as $event) {
			if ($event["type"] == "service") {
				$this->_service = true;
			}
	
			$this->_numEvents++;
			$arr_events .= $this->render("dashboard_events_dash", array("event" => $event));
		}
	
		return $arr_events;
	}
	
	/**
	 * Getter Получает значение переменной $this->_service
	 * @return private boolean $this->_service
	 */
	public function getServiceVar() {
		return $this->_service;
	}
	
	/**
	 * Getter Получает значение переменной $this->_numEvents
	 * @return private int $this->_numEvents
	 */
	public function getnumEventsVar() {
		return $this->_numEvents;
	}
	
	/**
	 * Возвращает массив событий в зависимости от привлегий пользоватиеля
	 * @return array $result
	 */
	private function rightLogs($data) {
		$k = 0;
		$result = array();
		
		for($i=0; $i<count($data); $i++) {
			$data[$i]["timestamp"] = date("d F Y H:i:s", strtotime($data[$i]["timestamp"]));
		
			if ( ($data[$i]["type"] == "task") or ($data[$i]["type"] == "com") ) {
				if ($task = $this->registry["task"]->getTask($data[$i]["oid"])) {
					if ($this->registry["task"]->acceptReadTask($task)) {
						$result[$k] = $data[$i];
						$result[$k]["param"] = $this->logs->getHistoryFromId($data[$i]["id"]);
		
						$k++;
					}
				}
			} elseif ( ($data[$i]["type"] == "mail") or ($data[$i]["type"] == "service") ) {
				if ($data[$i]["uid"] == $this->registry["ui"]["id"]) {
					$result[$k] = $data[$i];
					$result[$k]["param"] = $this->logs->getHistoryFromId($data[$i]["id"]);
		
					$k++;
				}
			} else {
				$result[$k] = $data[$i];
				$result[$k]["param"] = $this->logs->getHistoryFromId($data[$i]["id"]);
					
				$k++;
			}
		}
		
		return $result;
	}
	
	/**
	 * Формирует WHERE для SQL-запроса, в зависимости от значений сохранённых в сессии $this->dashboard
	 * Type: task, com, obj, info, mail, service
	 * 
	 * @return string $where
	*/
	private function getSessionWhere() {
	
		$where = array();
			
		if ($this->dashboard["task"]) {
			$where[] = "logs.type = 'task'";
		};
		if ($this->dashboard["com"]) {
			$where[] = "logs.type = 'com'";
		};
		if ($this->dashboard["obj"]) {
			$where[] = "logs.type = 'obj'";
		};
		if ($this->dashboard["info"]) {
			$where[] = "logs.type = 'info'";
		};
		if ($this->dashboard["mail"]) {
			$where[] = "logs.type = 'mail'";
		};
		if ($this->dashboard["service"]) {
			$where[] = "logs.type = 'service'";
		};
		
		$where = implode(" OR ", $where);
		$where = "AND (" . $where . ")";
		
		if ($this->dashboard["filtr"]) {
			$where .= " AND ((lo.val LIKE '%" . $this->dashboard["filtr"] . "%') OR (logs.event LIKE '%" . $this->dashboard["filtr"] . "%'))";
		};
		
		return $where;
	}
	
	/**
	 * Формирует WHERE для SQL-запроса для автоматической подгрузки данных при 
	 * прокрутке (ajax), основываясь на значение ID последнего события $this->dashboard["log_id"]
	 * 
	 * @return string
	*/
	private function getScrollSessionWhere() {
		return $this->getSessionWhere() . " AND logs.id < " . $this->dashboard["log_id"];
	}
	
	/**
	 * Формирует WHERE для SQL-запроса, на основе даты запроса
	 * 
	 * @return string $date
	*/
	private function getSessionDate() {
		if (isset($this->dashboard["date"])) {
			$date = "TO_DAYS(logs.timestamp) <= TO_DAYS('" . date("Y-m-d", strtotime($this->dashboard["date"])) . "')";
		} else {
			$date = "TO_DAYS(logs.timestamp) <= TO_DAYS('" . date("Y-m-d") . "')";
		}
		
		return $date;
	}

	/**
	 * Получает все события на основе даты и значений в сессии $this->dashboard
	 * 
	 * @return array $result
	*/
	function getEvents() {
		$data = array();
		
		$where = $this->getSessionWhere();

		$date = $this->getSessionDate();

		$db = new Db\Dashboard();
		$data = $db->getEvents($date, $where);
		
		if (count($data) > 0) {
			$this->dashboard["log_id"] = $data[count($data)-1]["id"];
		}

		return $this->rightLogs($data);
	}
	
	/**
	 * Получает события для автоматической подгрузки данных при 
	 * прокрутке (ajax), основываясь на значение ID последнего события $this->dashboard["log_id"]
	 * 
	 * @return array $result
	 * @return FALSE - если событий нет
	*/
	function getEventsWithoutSess() {
		$data = array();
		
		$where = $this->getScrollSessionWhere();
		
		$db = new Db\Dashboard();
		$data = $db->getEventsId($this->dashboard["log_id"], $where);
		
		if (count($data) > 0) {
			$this->dashboard["log_id"] = $data[count($data)-1]["id"];
			
			return $this->rightLogs($data);
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Получает все события для выпадающего dashboard на панели вверху страницы
	 * 
	 * @return array $data
	 */
	function getDashEvents() {
		$data = array();
	
		$where = "AND logs.timestamp LIKE '%' AND lc.uid = " . $this->registry["ui"]["id"];
	
		$date = "logs.type LIKE '%'";
	
		$db = new Db\Dashboard();
		$data = $db->getDashEvents($date, $where);

		for($i=0; $i<count($data); $i++) {
			$data[$i]["param"] = $this->logs->getHistoryFromId($data[$i]["id"]);
		}
		return $data;
	}
	
	/**
	 * Получает все НОВЫЕ события для выпадающего dashboard на панели вверху страницы
	 * 
	 * @return array $data
	 */
	function getNewEvents() {
		$lid = $this->logs->getLastDashId();
		
		$where = "AND logs.timestamp LIKE '%' AND lc.uid = " . $this->registry["ui"]["id"];

		$db = new Db\Dashboard();
		$data = $db->getNewEvents($lid, $where);

		for($i=0; $i<count($data); $i++) {
			$data[$i]["param"] = $this->logs->getHistoryFromId($data[$i]["id"]);
		}
		return $data;
	}

	/**
	 * Формирует массивы $notify и $this->dashboard на основе даннных содержащихся в сессии (даже если сессия пуста
	 * )
	 * @return array $notify
	 */
	function getNotify() {
		// Если сессии нет - по умолчанию включены все уведомления
		if (isset($this->dashboard["task"])) {
			$notify["task"] = $this->dashboard["task"];
		} else { $notify["task"] = true; $this->dashboard["task"] = true;
		}
		if (isset($this->dashboard["com"])) {
			$notify["com"] = $this->dashboard["com"];
		} else { $notify["com"] = true; $this->dashboard["com"] = true;
		}
		if (isset($this->dashboard["obj"])) {
			$notify["obj"] = $this->dashboard["obj"];
		} else { $notify["obj"] = true; $this->dashboard["obj"] = true;
		}
		if (isset($this->dashboard["info"])) {
			$notify["info"] = $this->dashboard["info"];
		} else { $notify["info"] = true; $this->dashboard["info"] = true;
		}
		if (isset($this->dashboard["mail"])) {
			$notify["mail"] = $this->dashboard["mail"];
		} else { $notify["mail"] = true; $this->dashboard["mail"] = true;
		}
		if (isset($this->dashboard["service"])) {
			$notify["service"] = $this->dashboard["service"];
		} else { $notify["service"] = true; $this->dashboard["service"] = true;
		}
		if (isset($this->dashboard["filtr"])) {
			$notify["filtr"] = $this->dashboard["filtr"];
		} else { $notify["filtr"] = NULL; $this->dashboard["filtr"] = NULL;
		}
		 
		return $notify;
	}

	/**
	 * Удалить событие в выпадающем dashboard на панели вверху страницы
	 */
	public function closeEvent($eid) {
		$db = new Db\Dashboard();
		$db->closeEvent($eid);
	}
}