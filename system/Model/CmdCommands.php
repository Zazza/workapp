<?php
class Model_CmdCommands extends Engine_Model {
	protected $args;
	private $result = null;
	private $_history = array();
	
	public function set($string) {
		if (mb_strpos($string, " ")) {
			$firstWord = mb_substr($string, 0, mb_strpos($string, " "));
		} else {
			$firstWord = mb_substr($string, 0, mb_strlen($string));
		}

		$this->args = explode(" ", $string);
		
		$method = "_" . strtolower($firstWord);
		
		if (mb_substr($method, 0, 2) != "__") {
			$flag = $this->$method();
			
			//memcached save
			if ($this->registry["memc"]["enable"]) {
				$this->memcached->set("cmd_ui:" . $this->registry["ui"]["id"]);
				if ($this->memcached->load()) {
					$this->_history = $this->memcached->get();
				}
				$result["message"] = $string;
				$result["date"] = date("H:i:s");
				$result["text"] = $this->result;
				$this->_history[] = $result;
				$this->memcached->save($this->_history);
			}
			//!memcached save
			
			return $flag;
		} else {
			return false;
		}
	}
	
	public function get() {
		return $this->result;
	}
	
	public function setHistory($string) {
		if ($this->registry["memc"]["enable"]) {
			$this->memcached->set("cmd_ui:" . $this->registry["ui"]["id"]);
			if ($this->memcached->load()) {
				$this->_history = $this->memcached->get();
			}
			$result["message"] = $string;
			$result["date"] = date("H:i:s");
			$result["text"] = "";
			$this->_history[] = $result;
			$this->memcached->save($this->_history);
		}
	}
	
	public function getHistory() {
		$result = array();
		
		$this->memcached->set("cmd_ui:" . $this->registry["ui"]["id"]);
		if ($this->memcached->load()) {
			$result = $this->memcached->get();
		}
		
		if ($result != NULL) {
			return $result;
		} else {
			return array();
		}
	}
	
	private function array_to_string($var){
		if (is_array($var)) {
			$output = "<pre class='pre'>";
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
					$cmds[] = mb_substr($name, 1, mb_strlen($name) - 1);
				}
			} 
		}

		$cmd = implode(", ", $cmds);
		
		$this->result = "<b>" . $cmd . "</b>";
		
		return true;
	}
	
	private function _about() {
		$this->result = $this->render("cmd_about", array());
		
		return true;
	}
	
	private function _clear() {
		$this->memcached->set("cmd_ui:" . $this->registry["ui"]["id"]);
		if ($this->memcached->load()) {
    		$this->memcached->delete();
    	}
		
		return true;
	}
	
	private function _exit() {
		return true;
	}
	
	private function _date() {
		$this->result = date("Y-m-d");
	
		return true;
	}
	
	private function _time() {
		$this->result = date("H:i:s");
	
		return true;
	}
	
	private function _status() {
		if ( (isset($this->args[1])) and (is_string($this->args[1])) ) {
			$uid = $this->registry["user"]->getUserId($this->args[1]);
			$ui = $this->registry["user"]->getUserInfo($uid);
			
			$status_bool = $this->registry["user"]->getstatus($uid);
			if ($status_bool) {
				$status = "<span style='color: #4F4'>online</span>";
			} else { $status = "<span style='color: red'>offline</span>";
			}
			
			$this->result = "<b>Статус: </b> " . $status;
		} else {
			$this->result = "Usage: status login";
		}
		
		return true;
	}
	
	private function _info() {
		if ( (isset($this->args[1])) and (is_string($this->args[1])) ) {
			$uid = $this->registry["user"]->getUserId($this->args[1]);
			$ui = $this->registry["user"]->getUserInfo($uid);
			
			if (count($ui) > 1) {
				$status_bool = $this->registry["user"]->getstatus($uid);
				if ($status_bool) {
					$status = "<span style='color: #4F4'>online</span>";
				} else {
					$status = "<span style='color: red'>offline</span>";
				}
				
				$this->result .= "<div>" . $ui["name"] . " " . $ui["soname"] . "</div>";
				$this->result .= "<div><b>Подпись: </b>" . $ui["signature"] . "</div>";
				$this->result .= "<div><b>Адрес: </b>" . $ui["adres"] . "</div>";
				$this->result .= "<div><b>Телефон: </b>" . $ui["phone"] . "</div>";
				$this->result .= "<div><b>ICQ: </b>" . $ui["icq"] . " " . $ui["skype"] . "</div>";
				$this->result .= "<div><b>Email: </b>" . $ui["email"] . "</div>";
				$this->result .= "<div><b>Статус: </b>" . $status . "</div>";
			} else {
				$this->result .= "---";
			}
		} else {
			$this->result = "Usage: info login";
		}
	
		return true;
	}
	
	private function _list() {
		$groups = $this->registry["user"]->getUsersGroups();
		
		$new_groups = array();
		if (count($groups) > 0) {
			$new_groups[0] = $groups[0];
		}
		
		for($i=1; $i<count($groups); $i++) {
			$flag = true;
			
			foreach($new_groups as $part) {
				if ($part["gname"] == $groups[$i]["gname"]) {
					$flag = false;
				}
			}
			
			if ($flag) {
				$new_groups[] = $groups[$i];
			}
		}
		
		foreach($new_groups as $group) {
			$users = $this->registry["user"]->getUserInfoFromGroup($group["gid"]);
				
			foreach($users as $user) {
				$status_bool = $this->registry["user"]->getStatus($user["uid"]);
				
				if ($status_bool) {
					$result[$group["gname"]][] = "<span style='color: #4F4'>[" . $user["name"] . " " . $user["soname"] . "] " . $user["login"] . "</span>";
				} else {
					$result[$group["gname"]][] = "<span style='color: red'>[" . $user["name"] . " " . $user["soname"] . "] " . $user["login"] . "</span>";
				}
			}
		}
		
		$this->result = $this->array_to_string($result);

		return true;
	}
	
	private function _msg() {
		if ( (isset($this->args[1])) and (is_string($this->args[1])) ) {
			
			if (count($this->args) > 2) {

				$result = array();
				for($i=2; $i<count($this->args); $i++) {
					$result[] = $this->args[$i];
				}
				$result_str = implode(" ", $result);

				if ($this->args[1] == "all") {
					
					$users = $this->registry["user"]->getUsersList();
					 
					foreach($users as $user) {
						$string = $this->render("logs_msg", array("msg" => $result_str));
						$this->registry["logs"]->uid = $user["id"];
						$this->registry["logs"]->set("service", $string, "");
					}
					
					$this->result = "message success send";
					
				} else if (mb_substr($this->args[1], 0, mb_strpos($this->args[1], "=")+1) == "group=") {
					
					$gid = $this->registry["user"]->getSubgroupId(mb_substr($this->args[1], mb_strpos($this->args[1], "=")+1, mb_strlen($this->args[1]) - mb_strpos($this->args[1], "=")));

					if (isset($gid)) {
						$users = $this->registry["user"]->getUserInfoFromGroup($gid);
						 
						foreach($users as $user) {
							$string = $this->render("logs_msg", array("msg" => $result_str));
							$this->registry["logs"]->uid = $user["uid"];
							$this->registry["logs"]->set("service", $string, "");
						}
						
						$this->result = "message success send";
					} else {
						$this->result = "group invalid";
					}					
				} else {
					$uid = $this->registry["user"]->getUserId($this->args[1]);

					if (isset($uid)) {
						$string = $this->render("logs_msg", array("msg" => $result_str));
						$this->registry["logs"]->uid = $uid;
						$this->registry["logs"]->set("service", $string, "");

						$this->result = "message success send";
					} else {
						$this->result = "login invalid";
					}
				}
			} else {
				$this->result = "message empty";
			}
		} else {
			$this->result = "Usage: msg [all,group=group_name,login] msg";
		}
		
		return true;
	}
	
	private function _registry() {
		if ($this->registry["ui"]["admin"]) {
			if ( (isset($this->args[1])) and (is_string($this->args[1])) ) {
				if (isset($this->registry[$this->args[1]])) {
					$this->result = $this->array_to_string($this->registry[$this->args[1]]);
				} else {
					$this->result = "argument not found";
				}
			} else {
				$this->result = "Usage: registry [arg]";
			}
		} else {
			$this->result = "access denied!";
		}
		
		return true;
	}
	
	private function _cache() {
		if ($this->registry["ui"]["admin"]) {
			if ( (isset($this->args[1])) and (is_string($this->args[1])) ) {
				
				$this->memcached->set($this->args[1]);
				
				if ($this->memcached->load()) {
					$this->result = $this->array_to_string($this->memcached->get());
				} else {
					$this->result = "argument not found";
				}
			} else {
				$this->result = "Usage: cache [arg]";
			}
		} else {
			$this->result = "access denied!";
		}
	
		return true;
	}
	
	private function _sessTime() {
		if ( (isset($this->args[1])) and (is_string($this->args[1])) ) {
				
			if (count($this->args) > 2) {
		
				$result = array();
		
				$count = count($this->args);
					
				for($i=2; $i<$count; $i++) {
					$result[] = $this->args[$i];
					$result = implode(" ", $result);
				}
		

				$uid = $this->registry["user"]->getUserId($this->args[1]);
	
				if (isset($uid)) {
					$ui = new Model_Ui();
					
					if ($this->args[2] == "today") {
						$this->args[2] = date("Ymd");
					}
					
					$sessTime = $ui->getSess($uid, $this->args[2]);
					
					$this->result = $this->array_to_string($sessTime);
				} else {
					$this->result = "login invalid";
				}
			} else {
				$this->result = "Usage: sessTime login date [date format: 'Y-m-d' or 'Ymd']";
			}

		} else {
			$this->result = "Usage: sessTime login date [date format: 'Y-m-d' or 'Ymd']";
		}
		
		return true;
	}
	
	private function _dashboard() {
		if ( (isset($this->args[1])) and (is_string($this->args[1])) ) {
			$dashboard = new Model_Dashboard();
			
			if ($this->args[1] == "today") {
				$this->args[1] = date("Ymd");
			}
			
			$session = & $_SESSION["dashboard"];
			$session["date"] = $this->args[1];
			
			$listevents = $dashboard->getEvents();
			
			$list = null;
			if (count($listevents) == 0) {
				$list = "Событий нет";
			}

			foreach($listevents as $event) {	
				$list .= $this->render("event", array("event" => $event));
			}
			
			$this->result = $list;
		} else {
			$this->result = "Usage: dashboard date [date format: 'Y-m-d' or 'Ymd']";
		}
		
		return true;
	}
	
	private function _task() {
		if ( (isset($this->args[1])) and (is_string($this->args[1])) ) {
		
			if ($this->registry["ui"]["admin"]) {
				if ($this->args[1] == "close") {
					if ( (isset($this->args[2])) and (is_numeric($this->args[2])) ) {
						$tt = $this->registry["module_tt"];
						$tt->closeTask($this->args[2]);
						
						$this->result = "task " . $this->args[2] . " closed";
					} else {
						$this->result = "ID invalid";
					}
				} else if ($this->args[1] == "info") {
					if ( (isset($this->args[2])) and (is_numeric($this->args[2])) ) {
						$tt = $this->registry["module_tt"];
						$task = $tt->getTask($this->args[2]);
						
						$this->result = $this->array_to_string($task);
					} else {
						$this->result = "ID invalid";
					}
				} else {
					$this->result = "Usage: task [info, close] ID";
				}				
			} else {
				$this->result = "access denied!";
			}
		
		} else {
			$this->result = "Usage: task [info, close] ID";
		}
		
		return true;
	}
	
	private function _modules() {
		if ($this->registry["ui"]["admin"]) {
			$dirs = array();
			$dir = $this->registry["rootPublic"] . "/" . $this->registry["path"]["modules"];
			$dh = opendir($dir);
			while (false !== ($filename = readdir($dh))) {
				if ( ($filename != ".") and ($filename != "..") ) { 
					$dirs[] = $filename;
				}
			}
			
			$this->result = $this->array_to_string($dirs);
		} else {
			$this->result = "access denied!";
		}
	}
	
	private function _models() {
		if ($this->registry["ui"]["admin"]) {
			$dirs = array(); $files = array();
			
			$dir = $this->registry["rootPublic"] . "/" . $this->registry["path"]["modules"];
			$dh = opendir($dir);
			while (false !== ($filename = readdir($dh))) {
				if ( ($filename != ".") and ($filename != "..") ) { 
					$dirs[] = $filename;
				}
			}
			
			$sdir = $this->registry["rootPublic"] .  $this->registry['path']['application'] . "Model/";
			$dh  = opendir($sdir);
			while (false !== ($filename = readdir($dh))) {
				if ( ($filename != ".") and ($filename != "..") ) {
					$files[] = "Model_" . substr($filename, 0, strpos($filename, "."));
				}
			}
			
			foreach($dirs as $module) {
				$dir = $this->registry["rootPublic"] . "/" . $this->registry["path"]["modules"] . $module . "/Model/";
				if (is_dir($dir)) {
					$dh  = opendir($dir);
					while (false !== ($filename = readdir($dh))) {
						if ( ($filename != ".") and ($filename != "..") ) {
							$dir = $this->registry["rootPublic"] . "/" . $this->registry["path"]["modules"] . $module . "/Model/" . $filename . "/";
							$dirname = $filename;
							if (is_dir($dir)) {
								$dh  = opendir($dir);
								while (false !== ($filename = readdir($dh))) {
									if ( ($filename != ".") and ($filename != "..") ) {
										$files[] = "Model_" . $dirname . "_" . substr($filename, 0, strpos($filename, "."));
									}
								}
							} else {
								$files[] = "Model_" . substr($filename, 0, strpos($filename, "."));
							}
						}
					}
				}
				
				$this->result = $this->array_to_string($files);
			}
		} else {
			$this->result = "access denied!";
		}
	}
	
	private function _model() {
		if ($this->registry["ui"]["admin"]) {
			if ( (isset($this->args[1])) and (is_string($this->args[1])) ) {
				$module = new ReflectionClass($this->args[1]);
				$this->result = $this->array_to_string($module->getMethods());
			} else {
				$this->result = "Usage: model [Model_Name]";
			}
		} else {
			$this->result = "access denied!";
		}
	}
	
	private function _getMethodParameters() {
		if ($this->registry["ui"]["admin"]) {
			if ( (isset($this->args[1])) and (is_string($this->args[1])) ) {
				if ( (isset($this->args[2])) and (is_string($this->args[2])) ) {
					$module = new ReflectionClass($this->args[1]);
					$this->result = $this->array_to_string($module->getMethod($this->args[2])->getParameters());
				} else {
					$this->result = "Usage: getMethodParameters [Model_Name] [MethodName]";
				}
			} else {
				$this->result = "Usage: getMethodParameters [Model_Name] [MethodName]";
			}
		} else {
			$this->result = "access denied!";
		}
	}
	
	private function _function() {
		if ($this->registry["ui"]["admin"]) {
			$params = array();
			
			if ( (isset($this->args[1])) and (is_string($this->args[1])) ) {
				if ( (isset($this->args[2])) and (is_string($this->args[2])) ) {
					$module = new ReflectionClass($this->args[1]);
					$obj = $module->newInstance();
					$method = $this->args[2];	
					
					for($i=3; $i<count($this->args); $i++) {
						$params[] = $this->args[$i];
					}
					$this->result = $this->array_to_string(call_user_func_array(array($obj, $method), $params));
				} else {
					$this->result = "Usage: function [Model_Name] [MethodName] [args]";
				}
			} else {
				$this->result = "Usage: function [Model_Name] [MethodName] [args]";
			}
		} else {
			$this->result = "access denied!";
		}
	}
	
	public function __call($method, $args) {
		$this->result = "Unknown command: <b>" . mb_substr($method, 1, mb_strlen($method) - 1) . "</b><br /> Try <b>help</b>";
		
		return true;
	}
}
?>
