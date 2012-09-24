<?php
class Model_Dashboard extends Engine_Model {
	private $dashboard;
	private $logs;
	private $_service = false;
	private $_numEvents = 0;
	
	function __construct() {
		parent::__construct();
				
		$this->dashboard = & $_SESSION["dashboard"];
		$this->logs = new Model_Logs();
	}
	
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
	
	public function getServiceVar() {
		return $this->_service;
	}
	
	public function getnumEventsVar() {
		return $this->_numEvents;
	}
	
	private function rightLogs($data) {
		$k = 0;
		$result = array();
		
		for($i=0; $i<count($data); $i++) {
			$data[$i]["timestamp"] = date("d F Y H:i:s", strtotime($data[$i]["timestamp"]));
		
			if ( ($data[$i]["type"] == "task") or ($data[$i]["type"] == "com") ) {
				if ($task = $this->registry["tt"]->getTask($data[$i]["oid"])) {
					if ($this->registry["tt"]->acceptReadTask($task)) {
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
	
	private function getScrollSessionWhere() {
		return $this->getSessionWhere() . " AND logs.id < " . $this->dashboard["log_id"];
	}
	
	private function getSessionDate() {
		if (isset($this->dashboard["date"])) {
			$date = "TO_DAYS(logs.timestamp) <= TO_DAYS('" . date("Y-m-d", strtotime($this->dashboard["date"])) . "')";
		} else {
			$date = "TO_DAYS(logs.timestamp) <= TO_DAYS('" . date("Y-m-d") . "')";
		}
		
		return $date;
	}

	function getEvents() {
		$data = array();
		
		$where = $this->getSessionWhere();

		$date = $this->getSessionDate();

		$db = new Model_Db_Dashboard();
		$data = $db->getEvents($date, $where);
		
		if (count($data) > 0) {
			$this->dashboard["log_id"] = $data[count($data)-1]["id"];
		}

		return $this->rightLogs($data);
	}
	
	function getEventsWithoutSess() {
		$data = array();
		
		$where = $this->getScrollSessionWhere();
		
		$db = new Model_Db_Dashboard();
		$data = $db->getEventsId($this->dashboard["log_id"], $where);
		
		if (count($data) > 0) {
			$this->dashboard["log_id"] = $data[count($data)-1]["id"];
			
			return $this->rightLogs($data);
		} else {
			return FALSE;
		}
	}
	
	function getDashEvents() {
		$data = array();
	
		$where = "AND logs.timestamp LIKE '%' AND lc.uid = " . $this->registry["ui"]["id"];
	
		$date = "logs.type LIKE '%'";
	
		$db = new Model_Db_Dashboard();
		$data = $db->getDashEvents($date, $where);

		for($i=0; $i<count($data); $i++) {
			$data[$i]["param"] = $this->logs->getHistoryFromId($data[$i]["id"]);
		}
		return $data;
	}
	
	function getNewEvents() {
		$lid = $this->logs->getLastDashId();
		
		$where = "AND logs.timestamp LIKE '%' AND lc.uid = " . $this->registry["ui"]["id"];

		$db = new Model_Db_Dashboard();
		$data = $db->getNewEvents($lid, $where);

		for($i=0; $i<count($data); $i++) {
			$data[$i]["param"] = $this->logs->getHistoryFromId($data[$i]["id"]);
		}
		return $data;
	}

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

	public function closeEvent($eid) {
		$db = new Model_Db_Dashboard();
		$db->closeEvent($eid);
	}
}
?>