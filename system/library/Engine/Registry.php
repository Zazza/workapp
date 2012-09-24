<?php
class Engine_Registry Implements ArrayAccess {
	
	private $vars = array();
	private static $instance;
	
	public static function getInstance() {
		if (empty(self::$instance)) {
			self::$instance = new Engine_Registry();
		}
		
		return self::$instance;
	}
	
	function set($key, $var) {
		if (isset($this->vars[$key]) == true) {
			throw new Exception('Unable to set var `' . $key . '`. Already set.');
		}
	
		$this->vars[$key] = $var;
	
		return true;
	}
	
	function get($key) {
		if (isset($this->vars[$key]) == false) {
			return null;
		}
	
		return $this->vars[$key];
	}
	
	function remove($key) {
		unset($this->vars[$key]);
	}
	
	function offsetExists($offset) {
		return isset($this->vars[$offset]);
	}
	
	function offsetGet($offset) {
		return $this->get($offset);
	}
	
	function offsetSet($offset, $value) {
		$this->set($offset, $value);
	}
	
	function offsetUnset($offset) {
		unset($this->vars[$offset]);
	}
}
?>