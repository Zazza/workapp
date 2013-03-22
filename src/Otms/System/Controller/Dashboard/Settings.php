<?php

/**
 * This file is part of the Workapp project.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\System\Controller\Dashboard;

use Engine\Controller;
use Otms\System\Model;

class Settings extends Controller {
	public function index() {
		$this->view->setTitle("Notice settings");
		
		$dashboard = new Model\Dashboard();
		
		$this->view->dashboard_settings(array());
	}
}