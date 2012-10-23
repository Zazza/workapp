<?php

/**
 * This file is part of the Workapp project.
 *
 * Objects Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Objects;

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
		$this->registry->set("object", new Model\Object());
	}
	
	function postInit() {

	}
}