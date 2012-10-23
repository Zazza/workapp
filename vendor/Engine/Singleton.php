<?php

/**
 * This file is part of the Workapp project Engine.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Engine;

use Engine\Registry;

/**
 * Singleton class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
abstract class Singleton {
	public $registry;
	
	function __construct() {
		$this->registry = Registry::getInstance();
	}
}
?>