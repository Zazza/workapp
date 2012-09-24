<?php
class Controller_Route_Draft extends Modules_Controller {
	function index() {
		if ($this->registry["ui"]["admin"]) {

			$this->view->setLeftContent($this->view->render("left_route", array()));
			
			$routes = new Model_Route();

			if ((isset($this->args[1])) and ($this->args[1] == "edit")) {

				if (isset($this->post["actionsubmit"])) {
					$routes->addDraftRouteAction($this->post["step_id"], $this->post["ifdata"], $this->post["ifcon"], $this->post["ifval"], $this->post["goto"], $this->post["ifid"]);
				} 
				if (isset($this->post["submit"])) {
					if (isset($this->post["tid"])) {
						if (isset($this->post["delegate"])) { $uid = $this->post["delegate"]; } else { $uid = 0; };
						$routes->setDraftRouteTask($this->post["tid"], $this->post, $uid);
					}
				}
				
				if (isset($this->get["id"])) {
					$route = $routes->getDraftRoute($this->get["id"]);
					
					$this->view->setTitle("Правка маршрута: " . $route[0]["name"]);
					
					$steps = $routes->getDraftSteps();
					for($i=0; $i<count($steps); $i++) {
						$steps[$i]["action"] = $routes->getDraftRouteAction($steps[$i]["step_id"]);
					}
					
					$this->view->draftedit(array("route" => $route, "rid" => $this->get["id"], "steps" => $steps));
				}
			} else {
				$this->view->setTitle("Черновики бизнес-маршрутов");
				
				$list = $routes->getDraftRoutes();
				
				$this->view->draftlist(array("list" => $list));
			}		

			$this->view->showPage();
		}
	}
}
?>
