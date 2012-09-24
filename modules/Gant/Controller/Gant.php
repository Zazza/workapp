<?php
class Controller_Gant extends Modules_Controller {

	public function index() {
		$this->view->setTitle("Диаграмма Ганта");
		
		$mgant = new Model_Gant();
		$gant = $mgant->getCalendar();
		
		$mreserv = new Model_Reserv();
		$reservs = $mreserv->getCalendar(); 

		$l = 0;
		$sgant = & $_SESSION["gant"];
		if (!isset($sgant["date"])) {
			$sgant["date"] = 1;
		}
		if(!isset($sgant["limit"])) {
			$sgant["limit"] = 10;
		}
		$while = ($sgant["date"] * $sgant["limit"]) - $sgant["limit"];
		for($i=$sgant["limit"]*$sgant["date"]; $i>=$while; $i--) {
			$cal[$l]["date"] = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-$i, date("y")));
			$cal[$l]["fdate"] = date("d.m D", mktime(0, 0, 0, date("m"), date("d")-$i, date("y")));
			
			$num = date("N", mktime(0, 0, 0, date("m"), date("d")-$i, date("y")));
			if ($num == 6 or $num == 7) {
				$cal[$l]["color"] = "#A55";
			} else {
				$cal[$l]["color"] = "#5A5";
			}
			
			$l++;
		}
		
		if (isset($this->get["reservs"])) {
			$type = "reservs";
		} else {
			$type = "tasks";
		}
		
		$this->view->calendar(array("cal" => $cal, "reservs" => $reservs, "gant" => $gant, "task" => $mgant->getTaskName(), "group" => $mgant->getGroupName(), "sess" => $sgant["date"], "limit" => $sgant["limit"], "type" => $type));
		
		$this->view->showPage();
	}
}
?>