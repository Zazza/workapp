<?php
class Controller_Objects_List extends Controller_Objects {

	public function index() {
		$this->view->setTitle("Просмотр");
		
		$this->view->setLeftContent($this->view->render("left_objects", array()));

		$template = new Model_Template();
		$list = $template->getTemplates();

		$this->object->links = "/list";

		$this->view->objects_find(array("list" => $this->templates, "templates" => $this->templates));

		$this->view->showPage();
	}
}
?>
