<?php
class Controller_Route_Add extends Modules_Controller {
	function index() {
		if ($this->registry["ui"]["admin"]) {
			$this->view->setTitle("Создание нового бизнес-маршрута");
			
			$this->view->setLeftContent($this->view->render("left_route", array()));
			
			$routes = new Model_Route();
			
			$name = date("Y-m-d H:i:s");
			$rid = $routes->addDraftRoute($name);
			
			$this->view->refresh(array("timer" => "1", "url" => "route/draft/edit/?id=" . $rid));
			
			$this->view->showPage();
		}
	}
}
?>
