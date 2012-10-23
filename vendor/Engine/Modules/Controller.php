<?php

/**
 * This file is part of the Workapp project Engine\Modules.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Engine\Modules;

/**
 * Controller\Module class
 *
 * Класс наследуемый controller классами модулей
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Controller extends \Engine\Controller {
	/**
	 * Экземпляр Вида
	 *
	 * @var object
	 */
	protected $view;

	/**
	 * Request: http://fomen.com/action/args[0]/args[1]/...
	 * 
	 * @var unknown_type
	 */	
	protected $action;
	
	/**
	 * Request: http://fomen.com/action/args[0]/args[1]/...
	 * @var unknown_type
	 */
	protected $args;
	
	/**
	 * Request $_GET
	 * @var unknown_type
	 */
	protected $get;
	
	/**
	 * Request $_POST
	 * @var unknown_type
	 */
	protected $post;

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
	
	function __construct($config = array()) {
		parent::__construct();

		if (count($config) == 0) {
        	$this->view = $this->registry['view'];
		} else {
			$this->config = $config;
			if (isset($config["module_name"])) {
				$this->module_name = $config["module_name"];
			}
			if (isset($config["module_path"])) {
				$this->module_path = $config["module_path"];
			}
        
			$this->view = new View($this->registry["twig_" . mb_strtolower($this->module_name)]);
		}

        $this->action = $this->registry["action"];
        $this->args = $this->registry["args"];
        $this->get = $this->registry["get"];
        $this->post = $this->registry["post"];      
    }

	public function __call($name = null, $args = null) {
		$this->view->setTitle("404");
		
        $this->view->page404();
	}
}
