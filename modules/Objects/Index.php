<?php
class Objects extends PreModule implements Modules_Interface {
	function __construct() {
		$module = new ReflectionClass($this);
		parent::__construct($module->getName());
	}
	
	function preInit() {
		$this->registry->set("object", new Model_Object());
	}
	
	function postInit() {

	}
}
?>