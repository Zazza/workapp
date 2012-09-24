<?php
class Modules_Model extends Engine_Model {
	protected $config;
	
	protected $modules;
	protected $module_name;
	protected $module_path;
	protected $twig;

	function __construct($config = array()) {
		parent::__construct();

		if (count($config) > 0) {
			$this->config = $config;
			if (isset($config["modules"])) {
				$this->modules = $config["modules"];
			}
			if (isset($config["module_name"])) {
				$this->module_name = $config["module_name"];
			}
			if (isset($config["module_path"])) {
				$this->module_path = $config["module_path"];
			}

			$this->twig = $this->registry["twig_" . mb_strtolower($this->module_name)];
		}
	}
	
	protected function render($name, $params) {
		$param = array("registry" => $this->registry);
	
		$template = $this->twig->loadTemplate($this->getTemplate($name));
	
		if (isset($params)) {
			$content = $template->render($param + $params);
		} else {
			$content = $template->render($param);
		};
	
		return $content;
	}
	
	function getTemplate($template) {
		$dirClass = explode("_", $template);
	
		if (sizeof($dirClass) > 1) {
			$template = implode(DIRECTORY_SEPARATOR, $dirClass) . '.tpl';
		} else
		{
			$template = $template . '.tpl';
		};
	
		return $template;
	}
}
?>
