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

/**
 * Controller\Objects\History class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class History extends Objects {

	public function index() {
		$this->view->setTitle("История");
		
		$this->view->setLeftContent($this->view->render("left_objects", array()));
		
		$tasks = $this->registry["logs"]->getHistory("obj", $this->args[1]);

		foreach($tasks as $task) {
			if ($task["param"][0]["key"]) {
				$this->view->history(array("obj" => $task));
			}
		}
		
		$this->view->showPage();
	}
}
?>