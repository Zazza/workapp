<?php

/**
 * This file is part of the Workapp project.
 *
 * Route (Бизнес-процессы) Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Route\Controller;

use Engine\Modules\Controller;
use Otms\Modules\Route\Model;

/**
 * Controller\Route class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Route extends Controller {
	function index() {
		if ($this->registry["ui"]["admin"]) {
			$this->view->setTitle("Administration workflow");
			
			$this->view->setLeftContent($this->view->render("left_route", array()));
			
			$routes = new Model\Route();
			$list = $routes->getRoutes();
			
			$this->view->routes(array("list" => $list));
			
			$this->view->showPage();
		}
	}
}
?>
