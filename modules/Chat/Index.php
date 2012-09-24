<?php
class Chat extends PreModule implements Modules_Interface {

	function __construct() {
		$module = new ReflectionClass($this);
		parent::__construct($module->getName());
	}
	
	function preInit() {
		$this->registry["chat"] = new Model_Chat($this->config);
	}
	
	function postInit() {
		$this->view->setBottomPanel($this->view->render("fastmenu", array()));
	}
}
?>
