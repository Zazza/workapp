<?php
class Engine_Ajax extends Engine_Interface {
	
	protected $view;
    protected $memcached;
    
    protected $module_name;
    protected $module_path;
    protected $config;

	function __construct() {
		parent::__construct();
		
		$this->initView();
	}
	
	protected function initView() {
		$loader = new Twig_Loader_Filesystem($this->registry["path"]["root"] . $this->registry['path']['templates']);
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
    	} else {
    		$this->errorload('NULL');
    		exit();
    	}
    }
    
    private function errorload($name) {
        echo "<p>Error load Ajax controller: " . $name . "</p>";
    }
}
?>