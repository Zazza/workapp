<?php

/**
 * This file is part of the Workapp project.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\System\Controller;

use Engine\Controller;
use Otms\System\Model\Ui;

class Logout extends Controller {
	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$this->view->setTitle("Logout");
		
		$ui = new Ui();

		session_destroy();
		
		$ui->stopSess($this->registry["ui"]["id"]);

		$this->view->refresh(array("timer" => "1", "url" => ""));
	}
}