<?php

/**
 * This file is part of the Workapp project.
 *
 * Filemanager Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Filemanager;

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
		$mfile = new Model\File($this->config);
		$count = $mfile->countBuffer();

		$this->view->setBottomPanel($this->view->render("block_buffer", array("count" => $count)), "right");
	}
}