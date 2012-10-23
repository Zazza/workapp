<?php

/**
 * This file is part of the Workapp project.
 *
 * Mail Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Mail;

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
		$this->registry->set("mailModel", new Model\Mail());
	}
	
	function postInit() {
		$mailClass = new Model\Mail();
		$mails = $mailClass->getUserInMailboxes($this->registry["ui"]["id"]);
		if (count($mails) > 0) {
			$enableCheck = true;
			$this->registry["enableCheck"] = true;
		} else {
			$enableCheck = false;
			$this->registry["enableCheck"] = false;
		}
	}
}