<?php
class Controller_Route_Edit extends Modules_Controller {
	function index() {
		if ($this->registry["ui"]["admin"]) {

			$this->view->setLeftContent($this->view->render("left_route", array()));
			
			$routes = new Model_RealRoute();

			if ($this->args[0] == "edit") {
				if (isset($this->post["actionsubmit"])) {
					$routes->addRouteAction($this->post["step_id"], $this->post["ifdata"], $this->post["ifcon"], $this->post["ifval"], $this->post["goto"]);
				} 
				if (isset($this->post["submit"])) {
					if (isset($this->post["tid"])) {
						if (isset($this->post["delegate"])) { $uid = $this->post["delegate"]; } else { $uid = 0; };
						$routes->setRouteTask($this->post["tid"], $this->post, $uid);
					}
				}
				
				if (isset($this->get["id"])) {
					$route = $routes->getRoute($this->get["id"]);
					
					$this->view->setTitle("Бизнес-процесс: " . $route[0]["name"]);
					
					$steps = $routes->getSteps();
					for($i=0; $i<count($steps); $i++) {
						$steps[$i]["action"] = $routes->getRouteAction($steps[$i]["step_id"]);
					}
					
					$this->view->edit(array("route" => $route, "rid" => $this->get["id"], "steps" => $steps));
				}
			}

			$this->view->showPage();
		}
	}
}
?>
