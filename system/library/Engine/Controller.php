<?php

class Engine_Controller extends Engine_Interface {
	protected $view;
	protected $model;
	
	protected $action;
	protected $args;
	protected $get;
	protected $post;
	
	function __construct() {
		parent::__construct();

		$this->view = $this->registry['view'];
		
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
}
?>
