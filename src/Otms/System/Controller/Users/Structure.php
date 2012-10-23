<?php

/**
 * This file is part of the Workapp project.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\System\Controller\Users;

use Otms\System\Controller\Users;

class Structure extends Users {

	public function index() {
		if ($this->registry["ui"]["admin"]) {

			$this->view->setTitle("Структура");

			$this->view->users_subgrouplist();
		}
	}
}