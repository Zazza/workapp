<?php
class Engine_Router extends Engine_Interface {
	private $args;

	function __construct() {
		parent::__construct();
	}

	private function getArgs($arguments) {
		foreach($arguments as $part) {
			$this->args[] = quotemeta($part);
		}
	}
	
	function showContent() {
        
		$action = (empty($_GET['main'])) ? '' : $_GET['main'];
        if (empty($action)) { $action = 'index'; };
		
		$action = trim($action, '/\\');
		$parts = explode('/', $action);

		$action = array_shift($parts);
		$action = mb_strtolower(quotemeta($action));
		
		$arguments = $parts;
		
		$this->getArgs($arguments);

		if (isset($_POST["action"])) {
			$this->registry->set("action", $_POST["action"]);
			unset($_POST["action"]);
		} else if (isset($_GET["action"])) {
			$this->registry->set("action", $_GET["action"]);
		}
		$this->registry->set("args", $this->args);
		$this->registry->set("get", $_GET);
		$this->registry->set("post", $_POST);

		if ( ($action == "ajax") and (isset($parts[0])) ) {

			if ( (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) and ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ) {
				if (isset($this->registry["module_" . $this->args[0]])) {
		    		$module = new LoadModule($this->args[0]);
		    		$module->ajax($this->registry["action"], $this->registry["post"]);
		    		
		    		return false;
		    	} else {
		    		$class = "Controller_Ajax_" . ucfirst($this->args[0]);
		    		$controller = new $class();
		    		
		    		$method = $this->registry["action"];
		    		$controller->$method($this->registry["post"]);
		    		
		    		return false;
		    	}
			}
		} else {
			if (isset($this->registry["module_" . $action])) {
				$this->registry->set("module", $action);
				
				$module = new LoadModule($action);
				$module->show();
			} else {
				$this->registry->set("syscontroller", $action);
				
				if (!is_file($this->registry["controller"] . ucfirst($action) . '.php')) {
				
					$class = 'Engine_Controller';
					$controller = new $class();
					
					$controller->__call();
				} else {

					if (isset($this->registry["args"][0])) {
						if (!is_file($this->registry["controller"] . ucfirst($action) . "/" . ucfirst($this->registry["args"][0]) . '.php')) {
							$class = 'Engine_Controller';
							$controller = new $class();
					
							$controller->__call();
						} else {
							$class = 'Controller_' . ucfirst($action) . "_" . ucfirst($this->registry["args"][0]);
							$controller = new $class();
							
							$controller->index();
						}
					} else {
						$class = 'Controller_' . ucfirst($action);
						$controller = new $class();
						
						$controller->index();
					}
				}
			}
			
			$notAjax = new notAjax();
			$notAjax->run();
			
			return true;
		}
	}
}

?>