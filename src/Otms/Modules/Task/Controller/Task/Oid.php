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
 * Controller\Task\Oid class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Oid extends Task {
	public function index() {
		$this->view->setTitle("Задачи для объекта");
		
		$this->view->setLeftContent($this->view->render("left_tt", array()));
		
		if (isset($this->args[1])) {
        	$this->registry["task"]->links = "task/" . $this->args[0] . "/" . $this->args[1] . "/";
        	$tasks = $this->registry["task"]->getOidTasks($this->args[1]);
        } else {
        	$this->__call("task", "index");
        }
        
        $this->showTasks($tasks);
    }
}
?>