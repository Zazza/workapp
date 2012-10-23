<?php

/**
 * This file is part of the Workapp project.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\System\Controller;

use Engine\Controller;

class Fa extends Controller {
	public function index() {
		Controller_Fm_Attach::index();
	}
}