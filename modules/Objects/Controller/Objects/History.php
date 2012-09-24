<?php
class Controller_Objects_History extends Controller_Objects {

	public function index() {
		$this->view->setTitle("История");
		
		$this->view->setLeftContent($this->view->render("left_objects", array()));
		
		$tasks = $this->registry["logs"]->getHistory("obj", $this->args[1]);

		foreach($tasks as $task) {
			if ($task["param"][0]["key"]) {
				$this->view->history(array("obj" => $task));
			}
		}
		
		$this->view->showPage();
	}
}
?>