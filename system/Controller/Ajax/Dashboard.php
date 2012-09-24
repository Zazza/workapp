<?php
class Controller_Ajax_Dashboard extends Engine_Ajax {
	private $dashboard;	
	private $numEvents = 0;
	private $service = false;
	
	function __construct() {
		parent::__construct();
		$this->dashboard = new Model_Dashboard();
	}
	
	function scroll($params) {
		$sess = & $_SESSION["dashboard"];
		
		$dashboard = new Model_Dashboard();
		
		$listevents = $dashboard->getEventsWithoutSess();
		
		if (is_array($listevents)) {			
			$list = null;
			
			foreach($listevents as $event) {
				echo $this->view->render("dashboard_events_event", array("event" => $event));
			}
		} else {
			echo 'end';
		}
	}
	
	function reset() {
		unset($_SESSION["dashboard"]);
	}
	
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
		
		echo json_encode($dash);
	}
	
	function closeEvent($params) {
		$eid = $params["eid"];
		
		$this->dashboard->closeEvent($eid);
		
		$this->dashboard->findEvents();
		
		if ($this->dashboard->getServiceVar()) {
			$service = true;
		} else {
			$service = false;
		}
		
		echo $service;
	}
	
	function clearEvents() {
		$events = $this->dashboard->getDashEvents();
		foreach($events as $event) {
			$this->dashboard->closeEvent($event["id"]);
		}
	}
	
	function saveNotice($params) {
		$ui = new Model_Ui();
		
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
?>