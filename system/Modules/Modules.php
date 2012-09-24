<?php
class LoadModule extends PreModule {
	private $module;
	
	function __construct($module) {
		$registry = Engine_Registry::getInstance();
		
		$this->module = $registry["module_" . $module];

		$this->module->setPath();
	}
	
	function ajax($action, $params = null) {
		$this->module->ajax($action, $params);
	}
	
	function show() {
		$en_env = $this->module->required_env();
		$en_mods = $this->module->required_modules();

		if ( ($en_env) and ($en_mods) ) {
			$this->module->show();
		} else {
			$reason = $this->module->showReason();
		}
	}
}

class PreModule {
	protected $registry;
	
	protected $short_module_name;
	protected $module_name;
	protected $module_path;

	protected $view;
	
	private $timeLife = 2592000; // 1 месяц
	protected $config;
	
	protected $modules = array();

	private $reason = array();
	
	function __construct($module_name) {
		$module = new ReflectionClass($this);
		
		$this->registry = Engine_Registry::getInstance();
		$this->module_name = $module_name;

		$this->short_module_name = mb_strtolower($module_name);
		$this->module_path = $this->registry["path"]["modules"] . $module_name . "/";

		$this->setPath();
		
		$this->getConfig();
		
        if (isset($this->config["path"]["tpl"])) {
    		$loader = new Twig_Loader_Filesystem($this->module_path . $this->config["path"]["tpl"]);
    		if ($this->registry["twig_cache"]) {
    			$twig = array('cache' => $this->registry["cache"], 'autoescape' => FALSE);
    		} else {
    			$twig = array('cache' => FALSE, 'autoescape' => FALSE);
    		}
    		$templates = new Twig_Environment($loader, $twig);
    		
    		$this->registry["twig_" . $this->short_module_name] = $templates;
        }
	}
	
	function postRouterInit() {
		$this->initView();
		
		$this->initModule();
	}

	private function getConfig() {
		$this->registry["memcached"]->set("module_" . $this->module_name);
		
		if ($this->registry["memcached"]->load()) {
			$config = $this->registry["memcached"]->get();
		} else {
			$module_config = $this->registry["rootPublic"] . $this->module_path . 'config.ini';

			$config = array_merge(parse_ini_file($module_config, true));

			$this->registry["memcached"]->save($config, false, $this->timeLife);
		}
		
		$config["module_name"] = $this->module_name;
		$config["module_path"] = $this->module_path;
		
		$this->config = $config;
	}
	
	protected function initView() {
		$this->view = new Modules_View($this->registry["twig_" . $this->short_module_name]);
		$this->view->setConfig($this->config);
	}
	
	private function initModule() {
		if (isset($this->config["css"])) {
			if (is_array($this->config["css"])) {
				foreach($this->config["css"] as $css) {
					$this->view->addCSS($this->registry["siteName"] . $this->registry["uri"] . $this->module_path . $css);
				}
			} else {
				$this->view->addCSS($this->registry["siteName"] . $this->registry["uri"] . $this->module_path . $this->config["css"]);
			}
		}
		
		if (isset($this->config["js"])) {
			if (is_array($this->config["js"])) {
				foreach($this->config["js"] as $js) {
					$this->view->addJS($this->registry["siteName"] . $this->registry["uri"] . $this->module_path . $js);
				}
			} else {
				$this->view->addJS($this->registry["siteName"] . $this->registry["uri"] . $this->module_path . $this->config["js"]);
			}
		}
	}

	protected function setPath() {
		set_include_path(get_include_path() . ":" . $this->registry["rootPublic"] . "/" . $this->module_path);
	}

	protected function required_env() {
		if (isset($this->config["env"])) {
			$env = $this->config["env"];

			if (count($env) > 0) {
				foreach($env as $part) {
					if ($part == "memcached") {
						if (!$this->registry["memc"]) {
							$this->reason[] = "Memcached not enabled";

							return false;
						}
					}
				}
			}
		}

		return true;
	}
	
	protected function required_modules() {
		if (isset($this->config["modules"])) {
			$this->modules = $this->config["modules"];
		}
		
		if (count($this->modules) > 0) {
			foreach($this->modules as $module) {
				if (isset($this->registry["module_" . $module])) {
					$this->modules[$module] = $this->registry["module_" . $module];
				} else {
					$this->reason[] = "Не найден требуемый модуль: " . $module;

					return false;
				}
			}
		}
		
		$this->config["modules"] = $this->modules;

		return true;
	}
	
	public function ajax($action, $params = null) {
		if (isset($this->config["ajax"])) {
			$class = $this->config["ajax"];
			$controller = new $class($this->config);
		} else {
			$class = 'Controller_Ajax_' . ucfirst($this->short_module_name);
			$controller = new $class($this->config);
		}

		if (is_string($action)) {
			$controller->$action($params);
		}
	}
	
	public function show() {
		if (isset($this->registry["args"][0])) {
			if (!is_file($this->registry["rootPublic"] . $this->config["module_path"] . 'Controller/' . ucfirst($this->short_module_name) . "/" . ucfirst($this->registry["args"][0]) . '.php')) {
				$class = 'Engine_Controller';
				$controller = new $class();
					
				$controller->__call();
			} else {
				$class = 'Controller_' . ucfirst($this->short_module_name) . '_' . ucfirst($this->registry["args"][0]);
				$controller = new $class($this->config);
				
				$controller->index();
			}
		} else {
			$class = 'Controller_' . ucfirst($this->short_module_name);
			$controller = new $class($this->config);
			
			$controller->index();
		}
	}
	
	public function __call($name, $args) {
		return $this->publicMethods($name, $args);
	}
	
	public function publicMethods($name, $args) {

		if (isset($this->config["publicMethods"])) {
			$class = $this->config["publicMethods"];
			$path = str_replace('_', '/', $this->config["publicMethods"]) . '.php';
		} else {
			$prefix =  str_replace("/", "_", mb_substr($this->module_path, 8, mb_strlen($this->module_path) - 9));
			$class = $prefix . '_Public_Functions';
			$path = 'Public/Functions.php';
		}

		if (is_file($this->registry["rootPublic"] . "/" . $this->module_path . $path)) {
			$functions = new $class($this->config);
			return $functions->$name($args);
		}
	}

	public function showReason() {
		$this->registry['view']->setMainContent($this->registry['view']->render("errorModule", array("error" => $this->reason)));
	}
}

class Modules_Modules extends Preload {
	public function load() {
		$module_name = array();
		
		$interface = new ReflectionClass("Modules_Interface");
		
		$this->registry["memcached"]->set("modules");
		
		if (!$this->registry["memcached"]->load()) {
			$modules = array();
			
			if ($dh  = opendir($this->registry["rootPublic"] . "/" . $this->registry["path"]["modules"])) {
				while (false !== ($filename = readdir($dh))) {
					if ( ($filename != ".") and ($filename != "..") ) {
						$modules[] = $filename;
					}
				}
			}
			
			$this->registry["memcached"]->save($modules);
		} else {
			$modules = $this->registry["memcached"]->get();
		}
		
		foreach($modules as $part) {
			require_once $part . '/Index.php';
			$module = new ReflectionClass($part);
			
			if (!$module->isSubclassOf($interface)) {
				throw new Exception("Unknow module: " . $module);
			} else {
				$obj = $module->newInstance();
				$this->registry->set("module_" . mb_strtolower($part), $obj);
				$module_name[] = mb_strtolower($part);
				
				$obj->preInit();
			}
		}
		
		$this->registry["mods"] = $module_name;
	}
}
?>