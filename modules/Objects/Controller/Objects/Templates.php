<?php
class Controller_Objects_Templates extends Controller_Objects {

	public function index() {
		if ($this->registry["ui"]["admin"]) {

			$this->view->setTitle("Шаблоны");

			$this->view->setLeftContent($this->view->render("left_objects", array()));
			
			$template = new Model_Template();
			$list = $template->getTemplates();
			
			$datatypes = $template->getDataTypes();

			if (isset($this->args[1])) {
				if ($this->args[1] == "add") {
					if (isset($_POST["submit"])) {
						$template->addTemplate($_POST);

						$this->view->refresh(array("timer" => "1", "url" => "objects/admin/"));
					} else {
						$this->view->objects_templateadd(array("datatypes" => $datatypes));
					}
				} elseif ($this->args[1] == "edit") {
					if (isset($this->args[2])) {
						if (isset($_POST["submit"])) {
							$template->editTemplate($this->args[2], $_POST);

							$this->view->refresh(array("timer" => "1", "url" => "objects/admin/"));
						} else {
							$param = $template->getTemplate($this->args[2]);
							$this->view->objects_templateedit(array("post" => $param, "datatypes" => $datatypes));
						}
					}
				} elseif ($this->args[1] == "editview") {
					$param = $template->getTemplateView($this->args[2]);
					$this->view->objects_templateeditview(array("tid" => $this->args[2], "post" => $param, "datatypes" => $datatypes));
				} elseif ($this->args[1] == "list") {
					$this->view->objects_templatelist(array("id" => $this->args[2]));
				}
			} else {
				$this->view->objects_templates(array("list" => $list));
			}
		}

		$this->view->showPage();
	}
}
?>