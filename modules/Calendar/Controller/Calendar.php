<?php
class Controller_Calendar extends Modules_Controller {

	public function index() {
		$this->view->setTitle("Календарь");
		
		$cal = & $_SESSION["cal"];
		if(!isset($cal["year"])) {
			$cal["year"] = date("Y");
		}
		if(!isset($cal["month"])) {
			$cal["month"] = date("m");
		}
		
		if (isset($this->get["oid"])) {
			$this->view->cal(array("ui" => $this->registry["ui"], "oid" => $this->get["oid"], "day" => date("d"), "month" => $cal["month"], "year" => $cal["year"], "calYear" => $this->registry["calYear"], "caltype" => $cal["type"], "type" => "oid"));
		} else if (isset($this->get["uid"])) {
			$this->view->cal(array("ui" => $this->registry["ui"], "uid" => $this->get["uid"], "day" => date("d"), "month" => $cal["month"], "year" => $cal["year"], "calYear" => $this->registry["calYear"], "caltype" => $cal["type"], "type" => "uid"));
		} else {
			$allmytask = $this->registry["tt"]->getNumStatTasks();
			$itertask = $this->registry["tt"]->getNumIterTasks();
			$timetask = $this->registry["tt"]->getNumTimeTasks();
			
			$this->view->cal(array("ui" => $this->registry["ui"], "day" => date("d"), "month" => $cal["month"], "year" => $cal["year"], "allmytask" => $allmytask, "itertask" => $itertask, "timetask" => $timetask, "calYear" => $this->registry["calYear"], "caltype" => $cal["type"], "type" => "all"));
		}
		 
		$this->view->showPage();
	}
}
?>