<?php
class Controller_Help extends Engine_Controller {
	public function __construct() {
		parent::__construct();
	}
	
	public function index() {
        $this->view->setTitle("Справка");
        
        $this->view->help();
    }
}
?>