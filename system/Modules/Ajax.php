<?php
class Modules_Ajax extends Modules_Parent {
	
	protected $view;
    protected $memcached;
    
    protected $module_name;
    protected $module_path;
    protected $config;

	function __construct($config) {
		parent::__construct();
 
		$this->config = $config;
		$this->module_name = $config["module_name"];
		$this->module_path = $config["module_path"]; 
		
		$this->initView();
	}
	
	protected function initView() {
		$loader = new Twig_Loader_Filesystem($this->module_path . $this->config["path"]["tpl"]);
		if ($this->registry["twig_cache"]) {
			$twig = array('cache' => $this->registry["cache"], 'autoescape' => FALSE);
		} else {
			$twig = array('cache' => FALSE, 'autoescape' => FALSE);
		}
		$templates = new Twig_Environment($loader, $twig);
	
		$this->view = new Modules_View($templates);
	}
    
    public function __call($name, $args) {
    	if (isset($args[0]["action"])) {
        	$action = $args[0]["action"];
        	$this->errorload($action);
    	}
    }
    
    private function errorload($name) {
        echo "<p>Error load Ajax controller: " . $name . "</p>";
    }
}
?>