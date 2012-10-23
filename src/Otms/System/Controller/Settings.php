<?php

/**
 * This file is part of the Workapp project.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\System\Controller;

use Engine\Controller;

class Settings extends Controller {
	public function index() {
		if ($this->registry["ui"]["admin"]) {
			$this->view->setLeftContent($this->view->render("left_settings", array()));
			
			$this->view->settings_index();
		}
	}
}