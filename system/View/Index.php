<?php
class View_Index extends Engine_View {

    public $leftBlock = null;
    public $rightBlock = array();
    private $main_content = null;
    private $error = null;
    private $content = null;
    private $topMenu = array();
    private $orderTopMenu = null;
    private $bottomPanel = array();
	private $bottomPanelRight = array();
    private $advBottomPanel = null;
    private $fastmenu = array();
    private $fastmenu_notdrop = null;
    private $css = array();
    private $js = array();
    private $settings = null;
    
    public function setLeftContent($text) {
        $this->leftBlock .= $text;
    }
    
    public function setRightContent($content, $num) {
    	if (is_numeric($num)) {
    		if (array_key_exists($num, $this->rightBlock)) {
    			$temp = $this->rightBlock[$num];
    			$this->rightBlock[$num] = $content;
    			$this->rightBlock[] = $temp;
    		} else {
    			$this->rightBlock[$num] = $content;
    		}
    	} else {
    		$this->rightBlock[] = $content;
    	}
    }

    public function setMainContent($text) {
        $this->error .= $text;
    }

	public function showPage() {
		$this->settings = new Model_Settings();
		
		$menu = $this->renderMenu();
		$fastmenu = $this->renderFastMenu();

		if ($this->error != null) {
			$this->mainContent .= $this->error;
		}

		$template = $this->main->loadTemplate("layouts.html");
		$template->display(array("registry" => $this->registry,
		                                "description" => implode(",", $this->description),
										"keywords" => implode(",", $this->keywords),
										"title" => $this->title,
										"css" => $this->css,
										"js" => $this->js,
										"menu" => $menu,
										"fastmenu" => $fastmenu,
										"fastmenu_notdrop" => $this->fastmenu_notdrop,
										"leftBlock" => $this->leftBlock,
										"rightBlock" => $this->rightBlock,
                                		"main_content" => $this->mainContent,
                                		"error" => $this->error,
										"content" => $this->content,
										"bottomPanel" => $this->bottomPanel,
										"bottomPanelRight" => $this->bottomPanelRight,
										"advBottomPanel" => $this->advBottomPanel));
	}
	
	public function addCSS($css) {
		$this->css[] = '<link href="' . $css . '" rel="stylesheet" type="text/css" />';
	}
	
	public function addJS($js) {
		$this->js[] = '<script type="text/javascript" src="' . $js . '"></script>';
	}
	
	private function renderMenu() {
		$menu = array();

		foreach ($this->topMenu as $key=>$val) {
			if (is_array($val)) {
				if (isset($this->orderTopMenu[$key])) {
					if (isset($menu[$this->orderTopMenu[$key]])) {
						$temp = $menu[$this->orderTopMenu[$key]];
						$menu[$this->orderTopMenu[$key]] = $this->render("menu_sub", array("key" => $key, "val" => $val));
						$menu[] = $temp;
					} else {
						$menu[$this->orderTopMenu[$key]] = $this->render("menu_sub", array("key" => $key, "val" => $val));
					}
				} else {
					$menu[] = $this->render("menu_sub", array("key" => $key, "val" => $val));
				}
			} else {
				if (isset($this->orderTopMenu[$key])) {
					if (isset($menu[$this->orderTopMenu[$key]])) {
						$temp = $menu[$this->orderTopMenu[$key]];
						$menu[$this->orderTopMenu[$key]] = $this->render("menu_one", array("key" => $key, "val" => $val));
						$menu[] = $temp;
					} else {
						$menu[$this->orderTopMenu[$key]] = $this->render("menu_one", array("key" => $key, "val" => $val));
					}
				} else {
					$menu[] = $this->render("menu_one", array("key" => $key, "val" => $val));
				}
			}
		}
		
		ksort($menu, SORT_NUMERIC);

		return implode(" ", $menu);
	}
	
	public function setMenu($menu, $href, $order = null) {
		$href = $this->registry["siteName"] . $this->registry["uri"] . $href;
		
		if (!is_array($menu)) {
			$this->topMenu[$menu] = $href;
			if (is_numeric($order)) {
				$this->orderTopMenu[$menu] = $order;
			}
		} else {
			foreach($menu as $key=>$val) {
				$this->topMenu[$key][$val] = $href;
			}
			if (is_numeric($order)) {
				$this->orderTopMenu[$key] = $order;
			}
		}
	}
	
	public function renderFastMenu() {
		if (count($this->fastmenu) != 0) {
			return implode(" ", $this->fastmenu);
		}
	}
	
	public function setFastMenu($name, $content, $notdrop) {
		if ($notdrop) {
			$this->fastmenu[$name] = $content;
		} else {
			$this->fastmenu_notdrop .= $content;
		}
	}
	
	public function setContent($content) {
		$this->content .= $content;
	}
	
	public function setBottomPanel($content, $num) {
		if (is_numeric($num)) {
			if (array_key_exists($num, $this->bottomPanel)) {
				$temp = $this->bottomPanel[$num];
				$this->bottomPanel[$num] = $content;
				$this->bottomPanel[] = $temp;
			} else {
				$this->bottomPanel[$num] = $content;
			}
		} else if ($num == "right") {
			$this->bottomPanelRight[] = $content;
		} else {
			$this->bottomPanel[] = $content;
		}
	}
	
	public function setAdvBottomPanel($content) {
		$this->advBottomPanel = $content;
	}
}
?>