<?php
class Controller_Tt_Date extends Controller_Tt {
	public function index() {
		$this->view->setLeftContent($this->view->render("left_tt", array()));
		
		$cal = & $_SESSION["cal"];
		
        $this->registry["tt"]->links = "tt/" . $this->args[0] . "/" . $this->args[1] . "/";

		$this->view->tt_caltype(array("caltype" => $cal["type"], "date" => $this->model->editDate(date("Y-m-d", strtotime($this->args[1])))));

		$tasks = $this->registry["tt"]->getTasksDate($this->registry["ui"]["id"], $this->args[1]);
		
		$this->showTasks($tasks);
    }
}
?>