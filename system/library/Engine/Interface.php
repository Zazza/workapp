<?php
abstract class Engine_Interface {
	public $registry;
	
	function __construct() {
		$this->registry = Engine_Registry::getInstance();
	}
}
?>