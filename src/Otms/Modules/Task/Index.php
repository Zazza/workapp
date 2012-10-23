<?php

/**
 * This file is part of the Workapp project.
 *
 * Task Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Task;

use Engine\Modules\PreModule;
use Engine\Modules\Module;
use ReflectionClass;

/**
 * Index class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Index extends PreModule implements Module {
	function __construct() {
		$module = new ReflectionClass($this);
		parent::__construct($module->getName());
	}
	
	function preInit() {
		$this->registry["task"] = new Model\Task();
		$this->registry["task_groups"] = new Model\Groups();
		$this->registry["task_user"] = new Model\User();
		$this->registry->set("ttgroups", $this->registry["task"]->getGroups());
	}
	
	function postInit() {
		$this->view->setContent($this->view->render("header", array()));
	}
}