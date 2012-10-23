<?php

/**
 * This file is part of the Workapp project.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\System\Controller;

use Engine\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Index extends Controller {

	public function index() {
		$response = new RedirectResponse($this->registry["siteName"] . "/task/");
		$response->send();
		
		exit();
	}
}
