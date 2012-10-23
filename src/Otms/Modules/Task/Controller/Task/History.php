<?php

/**
 * This file is part of the Workapp project.
 *
 * Task Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Task\Controller\Task;

use Otms\Modules\Task\Controller\Task;

/**
 * Controller\Task\History class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class History extends Task {

	function index() {
		$this->view->setTitle("История");
		
		$this->view->setLeftContent($this->view->render("left_tt", array()));
		
		$tasks = $this->registry["logs"]->getHistory("task", $this->args[1]);

		foreach($tasks as $task) {
			if ($task["param"][0]["key"]) {
				$this->view->history(array("obj" => $task));
			}
		}
		
		$this->view->showPage();
	}
}
?>