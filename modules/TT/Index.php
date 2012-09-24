<?php
class TT extends PreModule implements Modules_Interface {
	function __construct() {
		$module = new ReflectionClass($this);
		parent::__construct($module->getName());
	}
	
	function preInit() {
		$this->registry["tt"] = new Model_Tt();
		$this->registry["tt_groups"] = new Model_TTGroups();
		$this->registry["tt_user"] = new Model_TTUser();
		$this->registry->set("ttgroups", $this->registry["tt"]->getGroups());
	}
	
	function postInit() {
		$this->view->setContent($this->view->render("header", array()));
	}
}
?>
