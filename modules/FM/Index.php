<?php
class FM extends PreModule implements Modules_Interface {
	function __construct() {
		$module = new ReflectionClass($this);
		parent::__construct($module->getName());
	}
	
	function preInit() {
		
	}
	
	function postInit() {
		$mfile = new Model_File($this->config);
		$count = $mfile->countBuffer();

		$this->view->setBottomPanel($this->view->render("block_buffer", array("count" => $count)), "right");
	}
}
?>