<?php

/**
 * This file is part of the Workapp project Engine.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Engine;

use ArrayAccess;
use Exception;

/**
 * Registry class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Registry Implements ArrayAccess {
	
	private $vars = array();
	private static $instance;
	
	public static function getInstance() {
		if (empty(self::$instance)) {
			self::$instance = new Registry();
		}
		
		return self::$instance;
	}
	
	/**
	 * Setter
	 * @param string $key
	 * @param unknown_type $var
	 * @throws Exception
	 * @return TRUE
	 */
	function set($key, $var) {
		if (isset($this->vars[$key]) == true) {
			throw new Exception('Unable to set var `' . $key . '`. Already set.');
		}
	
		$this->vars[$key] = $var;
	
		return true;
	}
	
	/**
	 * Getter
	 * 
	 * @param string $key
	 * @return Result or NULL
	 */
	function get($key) {
		if (isset($this->vars[$key]) == false) {
			return null;
		}
	
		return $this->vars[$key];
	}
	
	/**
	 * 
	 * @param unknown_type $key
	 */
	function remove($key) {
		unset($this->vars[$key]);
	}
	
	/**
	 * 
	 * @param unknown_type $offset
	 */
	function offsetExists($offset) {
		return isset($this->vars[$offset]);
	}
	
	/**
	 * 
	 * @param unknown_type $offset
	 * @return Ambigous <\Engine\Result, NULL>
	 */
	function offsetGet($offset) {
		return $this->get($offset);
	}
	
	/**
	 * 
	 * @param unknown_type $offset
	 * @param unknown_type $value
	 */
	function offsetSet($offset, $value) {
		$this->set($offset, $value);
	}
	
	/**
	 * 
	 * @param unknown_type $offset
	 */
	function offsetUnset($offset) {
		unset($this->vars[$offset]);
	}
}
?>