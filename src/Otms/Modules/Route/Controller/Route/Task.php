<?php

/**
 * This file is part of the Workapp project.
 *
 * Route (Бизнес-процессы) Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Route\Controller\Route;

use Otms\Modules\Objects\Model\Template;

use Engine\Modules\Controller;
use Otms\Modules\Route\Model;

/**
 * Controller\Route\Task class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Task extends Controller {
	function index() {
		if ($this->registry["ui"]["admin"]) {
			if (isset($this->get["id"])) {
				$this->view->setTitle("Правка задачи");
				
				$routes = new Model\Route();
				$data = $routes->getTaskData($this->get["id"]);
				
				$this->view->setLeftContent($this->view->render("left_route", array()));
				$this->view->setLeftContent($this->view->render("left_data", array("data" => $data)));

				$task = $routes->getDraftRouteTask($this->get["id"]);
				
				$data[0] = $task["task"];
				$formtask = $this->registry["module_task"]->formfulltask($data);
				
				$template = new Template();
				$datatypes = $template->getDataTypes();
				$result =  $routes->getResult($this->get["id"]);
					
				$this->view->taskedit(array("formtask" => $formtask, "task" => $task, "datatypes" => $datatypes, "result" => $result));
				
				$this->view->showPage();
			}
		}
	}
}
?>
