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

/**
 * Controller\Task\Task class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Task extends \Otms\Modules\Task\Controller\Task {
	public function index() {
		$this->view->setTitle("Задачи");
		
		$this->view->setLeftContent($this->view->render("left_tt", array()));
		
		if (isset($this->args[1])) {
			$this->registry["task"]->links = "task/" . $this->args[0] . "/" . $this->args[1] . "/";
		} else {
			$this->registry["task"]->links = "task/" . $this->args[0] . "/";
		}
		
		if (isset($_GET["page"])) {
			if (is_numeric($_GET["page"])) {
				if (!$this->registry["task"]->setPage($_GET["page"])) {
					$this->registry["task"]->__call("task", "task");
				}
			}
		}
			
		if (isset($this->args[1])) {
			if($this->args[1] == "iter") {
				$tasks = $this->registry["task"]->getIterTasks();
				$this->view->tt_caltype(array("caltype" => $cal["type"], "date" => $this->model->editDate(date("Y-m-d"))));
			} elseif($this->args[1] == "time") {
				$tasks = $this->registry["task"]->getTimeTasks();
				$this->view->tt_caltype(array("caltype" => $cal["type"], "date" => $this->model->editDate(date("Y-m-d"))));
			} elseif($this->args[1] == "noiter") {
				$tasks = $this->registry["task"]->getNoiterTasks();
				$this->view->tt_caltype(array("caltype" => $cal["type"], "date" => $this->model->editDate(date("Y-m-d"))));
			} elseif($this->args[1] == "me") {
				$sortmytt = & $_SESSION["sortmytt"];
				if ( (!isset($sortmytt["sort"])) or (!isset($sortmytt["id"])) ) {
					$sortmytt["sort"] = "date";
					$sortmytt["id"] = "false";
				}

				$sort_groups = $this->registry["task"]->getSortGroupsMe();
				$this->view->setLeftContent($this->view->render("left_sortmytt", array("sort" => $sortmytt, "sg" => $sort_groups)));


				$tasks = $this->registry["task"]->getMeTasks();
			}
		} else {
			$this->__call("task", "index");
		}

		$this->showTasks($tasks);
	}
}
?>