<?php
class Engine_View extends Engine_Interface {

	protected $title = null;
	protected $description = array();
	protected $keywords = array();
	protected $mainContent = null;

	protected $menu = null;

	protected $main;
	protected $twig;

	function __construct() {
		parent::__construct();
		
        $this->main = $this->registry['layouts'];
        $this->twig = $this->registry['templates'];
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

	public function __call($name, $params) {
        $param = array("registry" => $this->registry);
        
		$template = $this->twig->loadTemplate($this->getTemplate($name));

		if (isset($params[0])) {
			$content = $template->render($param + $params[0]);
		} else {
			$content = $template->render($param);
		};

		$this->setMainContent($content);
	}
	
	public function render($name, $params) {
        $param = array("registry" => $this->registry);
        
		$template = $this->twig->loadTemplate($this->getTemplate($name));

		if (isset($params)) {
			$content = $template->render($param + $params);
		} else {
			$content = $template->render($param);
		};

		return $content;
	}

	public function setTitle($text) {
		$this->registry["title"] = $text;
		$this->title .= $text;
	}

	public function setDescription($text) {
		$this->description[] = str_replace('"',"",$text);
	}

	public function setKeywords($text) {
		$this->keywords[] = str_replace('"',"",$text);
	}

	public function setMainContent($text) {
		$this->mainContent .= $text;
	}
    
    public function showPage() {
        echo $this->mainContent;
    }
    
    public function show($content) {
    	echo $content;
    }
}
?>
