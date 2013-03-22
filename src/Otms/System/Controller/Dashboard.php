<?php

/**
 * This file is part of the Workapp project.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\System\Controller;

use Engine\Controller;
use Otms\System\Model;

class Dashboard extends Controller {
	public function index() {
		$this->view->setTitle("Events");
		
		$dashboard = new Model\Dashboard();
		
		$sess = & $_SESSION["dashboard"];
		
		if (isset($sess["date"])) {
			$date = date("m/d/y", strtotime($sess["date"]));
			$formatDate = date("d.m.Y", strtotime($sess["date"]));
		} else {
			$date = date("m/d/y");
			$formatDate = date("d.m.Y");
		}
		
		if(isset($sess["filtr"])) {
			$filtr = $sess["filtr"];
		} else {
			$filtr = NULL;
		}
		
		$this->view->setLeftContent($this->view->render("left_dashboard", array("notify" => $dashboard->getNotify(), "date" => $date, "formatDate" => $formatDate, "filtr" => $filtr)));
		 
		if (isset($_GET["page"])) {
			if (is_numeric($_GET["page"])) {
				if (!$dashboard->setPage($_GET["page"])) {
					$this->__call("task", "index");
				}
			}
		}

		$dashboard->links = "/";

		$list = NULL;

		$listevents = $dashboard->getEvents();
		
		if (count($listevents) == 0) {
			$list = "Events are absent";
		}

		foreach($listevents as $event) {
			
			$list .= $this->view->render("dashboard_events_event", array("event" => $event));
		}

		$this->view->dashboard_dashboard(array("list" => $list));
	}
}