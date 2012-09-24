<?php
class Controller_Route extends Modules_Controller {
	function index() {
		if ($this->registry["ui"]["admin"]) {
			$this->view->setTitle("Управление бизнес-процессами");
			
			$this->view->setLeftContent($this->view->render("left_route", array()));
			
			$routes = new Model_Route();
			$list = $routes->getRoutes();
			
			$this->view->routes(array("list" => $list));
			
			$this->view->showPage();
		}
	}
}
?>
