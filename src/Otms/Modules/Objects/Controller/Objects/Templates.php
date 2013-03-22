<?php

/**
 * This file is part of the Workapp project.
 *
 * Object Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Objects\Controller\Objects;

use Otms\Modules\Objects\Model\Template;

use Otms\Modules\Objects\Controller\Objects;

/**
 * Controller\Objects\Templates class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Templates extends Objects {

	public function index() {
		if ($this->registry["ui"]["admin"]) {

			$this->view->setTitle("Templates");

			$this->view->setLeftContent($this->view->render("left_objects", array()));
			
			$template = new Template();
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
				}
			} else {
				$this->view->objects_templates(array("list" => $list));
			}
		}

		$this->view->showPage();
	}
}
?>