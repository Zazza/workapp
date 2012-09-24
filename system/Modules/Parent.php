<?php
abstract class Modules_Parent {
	protected $registry;
	
	function __construct() {
		$this->registry = Engine_Registry::getInstance();
	}
}
?>