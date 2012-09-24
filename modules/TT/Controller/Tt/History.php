<?php
class Controller_Tt_History extends Controller_Tt {

	function index() {
		$this->view->setTitle("История");
		
		$this->view->setLeftContent($this->view->render("left_tt", array()));
		
		$tasks = $this->registry["logs"]->getHistory("task", $this->args[1]);

		foreach($tasks as $task) {
			if ($task["param"][0]["key"]) {
				$this->view->history(array("obj" => $task));
			}
		}
		
		$this->view->showPage();
	}
}
?>