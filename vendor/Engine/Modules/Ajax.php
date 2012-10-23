<?php

/**
 * This file is part of the Workapp project Engine\Modules.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Engine\Modules;

use Engine\Singleton;

/**
 * Ajax\Module class
 *
 * Класс наследуемый ajax классами модулей
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
	 * Имя модуля
	 * 
	 * @var string
	 */
    protected $module_name;
    
    /**
     * Путь к модулю
     *
     * @var string
     */
    protected $module_path;
    
    /**
     * Конфиг модуля
     *
     * @var array
     */
    protected $config;

	function __construct($config) {
		parent::__construct();
 
		$this->config = $config;
		if (isset($config["module_name"])) {
			$this->module_name = $config["module_name"];
		}
		if (isset($config["module_path"])) {
			$this->module_path = $config["module_path"];
		}
		
		$this->view = new View($this->registry["twig_" . mb_strtolower($this->config["module_name"])]);
	}
	
    public function __call($name, $args) {
    	if (isset($args[0]["action"])) {
        	$action = $args[0]["action"];
        	$this->errorload($action);
    	}
    }
    
    private function errorload($name) {
        echo "<p>Error load Ajax controller: " . $name . "</p>";
    }
}
?>