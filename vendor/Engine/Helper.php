<?php

/**
 * This file is part of the Workapp project Engine.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Engine;

use Engine\Singleton;

/**
 * Helper class
 * 
 * Класс наследуемый другими Helper классами
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Helper extends Singleton {
	protected $view;
	
	function __construct() {
		parent::__construct();
		
        $this->view = $this->registry['view'];
	}
}
?>