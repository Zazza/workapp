<?php
class Controller_Route_Action extends Modules_Controller {
	function index() {
		if ($this->registry["ui"]["admin"]) {
			if (isset($this->get["id"])) {
				$this->view->setTitle("Действие");
				
				$routes = new Model_Route();
				$data = $routes->getStepData($this->get["id"]);
				
				$this->view->setLeftContent($this->view->render("left_route", array()));
				$this->view->setLeftContent($this->view->render("left_data", array("data" => $data)));

				$rid = $routes->getRouteIdFromStep_id($this->get["id"]);
				$steps = $routes->getStepFromRoute($rid);
				$action = $routes->getDraftRouteAction($this->get["id"]);
				
				$this->view->action(array("action" => $action, "steps" => $steps, "data" => $data, "step_id" => $this->get["id"], "rid" => $rid));
				
				$this->view->showPage();
			}
		}
	}
}
?>
