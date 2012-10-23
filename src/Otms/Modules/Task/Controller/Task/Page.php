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
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Controller\Task\Page class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Page extends Task {
	public function index() {
		$response = new RedirectResponse($this->registry["siteName"] . "/task/");
		$response->send();
		
		exit();
    }
}
?>