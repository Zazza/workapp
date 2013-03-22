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
use Otms\Modules\Objects\Model;

/**
 * Controller\Objects\Admin class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Admin extends Objects {

	public function index() {
		if ($this->registry["ui"]["admin"]) {
			$this->view->setTitle("Administration object");
			
			$this->view->setLeftContent($this->view->render("left_objects", array()));
			
			$template = new Model\Template();
			$list = $template->getTemplates();
			
			$this->view->objects_admin(array("list" => $list));
			
			$this->view->showPage();
		}
	}
}
?>