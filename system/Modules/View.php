<?php
class Modules_View extends View_Index {

	protected $config = null;
	
	protected $title = null;
	protected $description = array();
	protected $keywords = array();
	protected $mainContent = null;

	protected $main;
	protected $twig;
	
	protected $mainView;

	function __construct($twig) {
		parent::__construct();
		
        $this->main = $this->registry['layouts'];
        $this->twig = $twig;
        
        $this->mainView = $this->registry["view"];
	}
	
	function setConfig($config) {
		$this->config = $config;
	}
	
	function get() {
		return $this->mainContent;
	}

	function setMain($name, $params) {
		$this->setMainContent($this->mainView->render($name, $params));
	}

	function getTemplate($template) {
		$dirClass = explode("_", $template);
	
		if (sizeof($dirClass) > 1) {
			$template = implode(DIRECTORY_SEPARATOR, $dirClass) . '.tpl';
		} else {
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
	
	public function addCSS($css) {
		$this->mainView->addCSS($css);
	}
	
	public function addJS($js) {
		$this->mainView->addJS($js);
	}

	public function setMenu($menu, $href = null, $order = null) {
		if ($href == null) {
			$href = mb_strtolower($this->config["module_name"]) . "/";
		}
		
		$this->mainView->setMenu($menu, $href, $order);
	}
	
	public function setFastMenu($name, $content, $notdrop = true) {
		$this->mainView->setFastMenu($name, $content, $notdrop);
	}
	
	public function setContent($content) {
		$this->mainView->setContent($content);
	}
	
	public function setBottomPanel($content, $num = null) {
		$this->mainView->setBottomPanel($content, $num);
	}
	
	public function setRightContent($content, $num = null) {
		$this->mainView->setRightContent($content, $num);
	}
	
	public function setAdvBottomPanel($content) {
		$this->mainView->setAdvBottomPanel($content);
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
    	$this->mainView->title = $this->title;
    	$this->mainView->description = $this->description;
    	$this->mainView->keywords = $this->keywords;
    	$this->mainView->setMainContent($this->get());
    }
    
    public function refresh($array) {
    	$content = $this->mainView->render("refresh", $array);
    	$this->setMainContent($content);
    }

    public function pager($array) {
    	$content = $this->mainView->render("pager", $array);
    	$this->setMainContent($content);
    }

    public function setLeftContent($content) {
    	$content = $this->mainView->setLeftContent($content);
    }
}
?>
