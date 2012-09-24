<?php
class Modules_Controller extends Modules_Parent {
	protected $view;
	protected $model;
	
	protected $action;
	protected $args;
	protected $get;
	protected $post;
	
	protected $modules;
	protected $module_name;
	protected $module_path;
	protected $config;
	
	function __construct($config = array()) {
		parent::__construct();

		if (count($config) == 0) {
        	$this->view = $this->registry['view'];
		} else {
        	$this->config = $config;
        	$this->modules = $config["modules"];
        	$this->module_name = $config["module_name"];
        	$this->module_path = $config["module_path"];
        
        	$this->initView();
		}
		
		$this->model = new Engine_Model();
        
        $this->action = $this->registry["action"];
        $this->args = $this->registry["args"];
        $this->get = $this->registry["get"];
        $this->post = $this->registry["post"];
    }

	public function __call($name = null, $args = null) {
		$this->view->setTitle("404");
		
        $this->view->page404();
	}
	
	protected function initView() {
		$this->view = new Modules_View($this->registry["twig_" . mb_strtolower($this->module_name)]);
	}
}
?>
