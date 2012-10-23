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
 * Controller\Task\Date class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Date extends Task {
	public function index() {
		$this->view->setLeftContent($this->view->render("left_tt", array()));
		
		$cal = & $_SESSION["cal"];
		
        $this->registry["task"]->links = "task/" . $this->args[0] . "/" . $this->args[1] . "/";

		$this->view->tt_caltype(array("caltype" => $cal["type"], "date" => $this->model->editDate(date("Y-m-d", strtotime($this->args[1])))));

		$tasks = $this->registry["task"]->getTasksDate($this->registry["ui"]["id"], $this->args[1]);
		
		$this->showTasks($tasks);
    }
}
?>