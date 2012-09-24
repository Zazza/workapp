<?php
class Modules_Functions extends Modules_Parent {
	
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
		$this->view = new Modules_View($this->registry["twig_" . mb_strtolower($this->module_name)]);
	}
    
    public function __call($name, $args) {
        $this->errorload($name, $args);
    }
    
    private function errorload($name, $args) {
        echo "<p>Error find public method: " . $name . "</p>";
    }
}
?>