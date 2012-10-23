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
 * Ajax class
 * 
 * Класс наследуемый другими ajax классами
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Ajax extends Singleton {
	/**
	 * Экземпляр Вида
	 *
	 * @var object
	 */
	protected $view;
	
	/**
	 * Экземпляр memcached
	 *
	 * @var object
	 */
    protected $memcached;

	function __construct() {
		parent::__construct();
		
		$this->view = $this->registry["view"];
	}
    
    public function __call($name, $args) {
    	if (isset($args[0]["action"])) {
        	$action = $args[0]["action"];
        	$this->errorload($action);
    	} else {
    		$this->errorload('NULL');
    		exit();
    	}
    }

    private function errorload($name) {
        echo "<p>Error load Ajax controller: " . $name . "</p>";
    }
}
?>