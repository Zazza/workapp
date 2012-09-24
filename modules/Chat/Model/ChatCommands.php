<?php
class Model_ChatCommands extends Modules_Model {
	protected $args;
	private $result = null;
	
	public function set($string) {
		if (mb_substr($string, 0, 1) == "/") {
			if (mb_strpos($string, " ")) {
				$firstWord = mb_substr($string, 1, mb_strpos($string, " ") - 1);
			} else {
				$firstWord = mb_substr($string, 1, mb_strlen($string) - 1);
			}

			$this->args = explode(" ", $string);
			
			$method = "_" . strtolower($firstWord);
			
			if (mb_substr($method, 0, 2) != "__") {
				return $this->$method();
			} else {
				return false;
			}
		} else {
			$this->result = $string;
			
			return false;
		}
	}
	
	public function get() {
		return $this->result;
	}
	
	private function array_to_string($var){
		if (is_array($var)) {
			$output = "<pre>";
			$this->var_dump_to_string($var,$output);
			$output .= "</pre>";
		} else {
			$output = $var;
		}
		
		return $output;
	}
	
	private function var_dump_to_string($var,&$output,$prefix=""){
		foreach($var as $key=>$value){
			if(is_array($value)){
				$output.= $prefix.$key.": \n";
				$this->var_dump_to_string($value,$output,"  ".$prefix);
			} else{
				$output.= $prefix.$key.": ".$value."\n";
			}
		}
	}
	
	private function _help() {
		$class = new ReflectionClass($this);
		$methods = $class->getMethods();
		
		foreach($methods as $method) {
			$name = $method->getName();
			if (mb_substr($name, 0, 1) == "_") {
				if (mb_substr($name, 0, 2) != "__") {
					$cmds[] = "/" . mb_substr($name, 1, mb_strlen($name) - 1);
				}
			} 
		}

		$cmd = implode(", ", $cmds);
		
		$this->result = "<b>" . $cmd . "</b>";
		
		return true;
	}
	
	private function _private() {
		if ($this->args[1] == $this->registry["ui"]["login"]) {
			$result = array();
			
			$count = count($this->args);

			for($i=2; $i<$count; $i++) {
				$result[] = $this->args[$i];
			}
		
			$this->result = "<b>[Приватно]</b> <span style='color: green; font-style: italic'>" . implode(" ", $result) . "</span>";
		}
		
		return false;
	}

	public function __call($method, $args) {
		$this->result = "Unknown command: <b>/" . mb_substr($method, 1, mb_strlen($method) - 1) . "</b><br /> Try <b>/help</b>";
		
		return true;
	}
}
?>
