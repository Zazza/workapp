<?php
class Controller_Route_Task extends Modules_Controller {
	function index() {
		if ($this->registry["ui"]["admin"]) {
			if (isset($this->get["id"])) {
				$this->view->setTitle("Правка задачи");
				
				$routes = new Model_Route();
				$data = $routes->getTaskData($this->get["id"]);
				
				$this->view->setLeftContent($this->view->render("left_route", array()));
				$this->view->setLeftContent($this->view->render("left_data", array("data" => $data)));

				$task = $routes->getDraftRouteTask($this->get["id"]);
				
				$data[0] = $task["task"];
				$formtask = $this->registry["module_tt"]->formfulltask($data);
				
				$template = new Model_Template();
				$datatypes = $template->getDataTypes();
				$result =  $routes->getResult($this->get["id"]);
					
				$this->view->taskedit(array("formtask" => $formtask, "task" => $task, "datatypes" => $datatypes, "result" => $result));
				
				$this->view->showPage();
			}
		}
	}
}
?>
