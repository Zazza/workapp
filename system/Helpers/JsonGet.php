<?php
class Helpers_JsonGet {
	public $call = null;

	function is_valid_callback($subject) {
		$identifier_syntax = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';
	
		$reserved_words = array('break', 'do', 'instanceof', 'typeof', 'case', 'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue', 'for', 'switch', 'while', 'debugger', 'function', 'this', 'with', 'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum', 'extends', 'super', 'const', 'export', 'import', 'implements', 'let', 'private', 'public', 'yield', 'interface', 'package', 'protected', 'static', 'null', 'true', 'false');
	
		return preg_match($identifier_syntax, $subject) && ! in_array(mb_strtolower($subject, 'UTF-8'), $reserved_words);
	}
	
	function JSONGet($json) {
		# JSON if no callback
		if ($this->call == null) {
		    exit(json_encode($json));
		}
		
		# JSONP if valid callback
		if ($this->is_valid_callback($this->call)) {
		    exit($this->call . "(" . json_encode($json) . ")");
		}
		
		# Otherwise, bad request
		header('Status: 400 Bad Request', true, 400);
	}
}
?>