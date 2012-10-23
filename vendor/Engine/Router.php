<?php

/**
 * This file is part of the Workapp project Engine.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Engine;

use Engine\Singleton;
use Otms\System\Component;
use Engine\Modules\LoadModule;
use Symfony\Component\HttpFoundation\Response;

/**
 * This file is part of the Workapp project Engine.
 *
 * Роутинг
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

class Router extends Singleton {
	private $args;

	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Проверка зависимостей для загрузки модуля (сторонних модулей)
	 *    например: memcached
	 *
	 * @param array $config
	 * @param object $module
	 * @return boolean
	 *    если FALSE - array $module->setReason
	 */
	protected function required_env($config, $module) {
		if (isset($config["env"])) {
			$env = $config["env"];
	
			if (count($env) > 0) {
				foreach($env as $part) {
					if ($part == "memcached") {
						if (!$this->registry["memc"]["enable"]) {
							$module->setReason("Memcached not enabled");
	
							return false;
						}
					}
				}
			}
		}
	
		return true;
	}
	
	/**
	 * Проверка зависимостей для загрузки модуля (других модулей)
	 * 
	 * @param array $config
	 * @param object $module
	 * @return boolean
	 *    если FALSE - array $module->setReason
	 */
	protected function required_modules($config, $module) {
		if (isset($this->registry["mods"])) {
			$this->modules = $this->registry["mods"];
		}
	
		if (count($this->modules) > 0) {
			foreach($this->modules as $module) {
				if (isset($this->registry["module_" . $module])) {
					$this->modules[$module] = $this->registry["module_" . $module];
				} else {
					$module->setReason("Не найден требуемый модуль: " . $module);
	
					return false;
				}
			}
		}
	
		$config["modules"] = $this->modules;
	
		return true;
	}
	
	/**
	 * Проверка зависимостей для загрузки модуля
	 * 
	 * @param array $config
	 * @param object $module
	 * @return boolean
	 *    если FALSE - array $module->showReason
	 */
	function run($config, $module) {
		$en_env = $this->required_env($config, $module);
		$en_mods = $this->required_modules($config, $module);
	
		if ( ($en_env) and ($en_mods) ) {
			return TRUE;
		} else {
			$reason = $module->showReason();
			
			return FALSE;
		}
	}
	
	/**
	 * Роутинг
	 * 
	 * @return boolean
	 */
	function showContent() {        
		$action = (empty($_GET['main'])) ? '' : $_GET['main'];
        if (empty($action)) { $action = 'index'; };
		
		$action = trim($action, '/\\');
		$parts = explode('/', $action);

		$action = array_shift($parts);
		$action = mb_strtolower(quotemeta($action));
		
		$arguments = $parts;

		foreach($arguments as $part) {
			$this->args[] = quotemeta($part);
		}

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
		    		$module = $this->registry["module_" . $this->args[0]];
		    		
		    		$config = $module->getConfig();
		    		
		    		$class = 'Otms\Modules\\' . ucfirst($this->args[0]) . '\Ajax\\' . ucfirst($this->args[0]);
		    		$controller = new $class($config);
		    	} else {
		    		$class = "Otms\System\Ajax\\" . ucfirst($this->args[0]);
		    		$controller = new $class();
		    	}
		    	
		    	$method = $this->registry["action"];
		    	$result = $controller->$method($this->registry["post"]);
		    	
		    	$response = new Response($result);
		    	$response->send();

		    	return false;
			}
		} else {
			if (isset($this->registry["module_" . $action])) {
				$this->registry->set("module", $action);
				
				$module = $this->registry["module_" . $action];

				$config = $module->getConfig();
				
				if (isset($this->args[0])) {
					if (!is_file($this->registry["rootPublic"] . $config["module_path"] . 'Controller/' . ucfirst($action) . "/" . ucfirst($this->args[0]) . '.php')) {
						$class = 'Engine\Controller';
						$controller = new $class();
							
						$controller->__call();
					} else {
						$class = 'Otms\Modules\\' . ucfirst($action) . '\Controller\\' . ucfirst($action) . '\\' . ucfirst($this->args[0]);
						$controller = new $class($config);
				
						if ($this->run($config, $module)) {
							$controller->index();
						}
					}
				} else {
					$class = 'Otms\Modules\\' . ucfirst($action) . '\Controller\\' . ucfirst($action);
					$controller = new $class($config);
				
					if ($this->run($config, $module)) {
						$controller->index();
					}
				}
			} else {
				$this->registry->set("syscontroller", $action);
				
				if (!is_file($this->registry["controller"] . ucfirst($action) . '.php')) {
				
					$class = 'Engine\Controller';
					$controller = new $class();
					
					$controller->__call();
				} else {

					if (isset($this->registry["args"][0])) {
						if (!is_file($this->registry["controller"] . ucfirst($action) . "/" . ucfirst($this->registry["args"][0]) . '.php')) {
							$class = 'Engine\Controller';
							$controller = new $class();
					
							$controller->__call();
						} else {
							$class = 'Otms\System\Controller\\' . ucfirst($action) . "\\" . ucfirst($this->registry["args"][0]);
							$controller = new $class();
							
							$controller->index();
						}
					} else {
						$class = 'Otms\System\Controller\\' . ucfirst($action);
						$controller = new $class();
						
						$controller->index();
					}
				}
			}
			
			$precontroller = new Component\PostController();
			$precontroller->run();
			
			return true;
		}
	}
}

?>