<?php
class Controller_Objects_Admin extends Controller_Objects {

	public function index() {
		if ($this->registry["ui"]["admin"]) {
			$this->view->setTitle("Управление объектами");
			
			$this->view->setLeftContent($this->view->render("left_objects", array()));
			
			$template = new Model_Template();
			$list = $template->getTemplates();
			
			$this->view->objects_admin(array("list" => $list));
			
			$this->view->showPage();
		}
	}
}
?>