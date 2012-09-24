<?php
class Mail extends PreModule implements Modules_Interface {
	function __construct() {
		$module = new ReflectionClass($this);
		parent::__construct($module->getName());
	}
	
	function preInit() {
		$this->registry->set("mailModel", new Model_Mail());
	}
	
	function postInit() {
		$mailClass = new Model_Mail();
		$mails = $mailClass->getUserInMailboxes($this->registry["ui"]["id"]);
		if (count($mails) > 0) {
			$enableCheck = true;
			$this->registry["enableCheck"] = true;
		} else {
			$enableCheck = false;
			$this->registry["enableCheck"] = false;
		}
	}
}
?>
