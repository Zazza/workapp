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

use Otms\Modules\Objects\Controller\Objects;
use Otms\Modules\Objects\Model\Template;

/**
 * Controller\Objects\Lists class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Lists extends Objects {

	public function index() {
		$this->view->setTitle("View");
		
		$this->view->setLeftContent($this->view->render("left_objects", array()));

		$template = new Template();
		$list = $template->getTemplates();

		$this->object->links = "/lists";

		$this->view->objects_find(array("list" => $this->templates, "templates" => $this->templates));

		$this->view->showPage();
	}
}
?>
