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

use Engine\Modules\Controller;
use Otms\Modules\Route\Model;

/**
 * Controller\Route\Action class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Action extends Controller {
	function index() {
		if ($this->registry["ui"]["admin"]) {
			if (isset($this->get["id"])) {
				$this->view->setTitle("Действие");
				
				$routes = new Model\Route();
				$data = $routes->getStepData($this->get["id"]);
				
				$this->view->setLeftContent($this->view->render("left_route", array()));
				$this->view->setLeftContent($this->view->render("left_data", array("data" => $data)));

				$rid = $routes->getRouteIdFromStep_id($this->get["id"]);
				$steps = $routes->getStepFromRoute($rid);
				$action = $routes->getDraftRouteAction($this->get["id"]);
				
				$this->view->action(array("action" => $action, "steps" => $steps, "data" => $data, "step_id" => $this->get["id"], "rid" => $rid));
				
				$this->view->showPage();
			}
		}
	}
}
?>
