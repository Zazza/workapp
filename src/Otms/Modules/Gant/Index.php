<?php

/**
 * This file is part of the Workapp project.
 *
 * Gant Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Gant;

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

	}
	
	function postInit() {

	}
}