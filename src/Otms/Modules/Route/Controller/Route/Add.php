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
 * Controller\Route\Add class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Add extends Controller {
	function index() {
		if ($this->registry["ui"]["admin"]) {
			$this->view->setTitle("Создание нового бизнес-маршрута");
			
			$this->view->setLeftContent($this->view->render("left_route", array()));
			
			$routes = new Model\Route();
			
			$name = date("Y-m-d H:i:s");
			$rid = $routes->addDraftRoute($name);
			
			$this->view->refresh(array("timer" => "1", "url" => "route/draft/edit/?id=" . $rid));
			
			$this->view->showPage();
		}
	}
}
?>
