<?php
class Controller_Objects_Kb extends Modules_Controller {
	protected $tree;
	
	protected function print_array($arr) {
		if (!is_array($arr)) {
			return;
		}
	
		while(list($key, $val) = each($arr)) {
			if (!is_array($val)) {
				if ($val == null) {
					$val = "пусто";
				}
	
				$this->tree .= "<ul><a style='font-size: 14px;' href='" . $this->registry["uri"] . "objects/kb/?tag=" . $val . "'>" . $val . "</a></ul>";
			}
			if (is_array($val)) {
				if ($key != "0") {
					$this->tree .= "<ul><li><span class='folder'>&nbsp;" . $key . "</span>";
				}
	
				$this->print_array($val);
	
				if ($key != "0") {
					$this->tree .= "</li></ul>";
				}
			}
		}
	}

	public function index() {
		$this->view->setLeftContent($this->view->render("left_kb", array()));
		
		$advinfo = new Model_Ai();
		$tpl = new Model_Template();

		if (isset($this->get["history"])) {
			$this->view->setTitle("История");
	
			$tasks = $this->registry["logs"]->getHistory("info", $this->get["history"]);
	
			foreach($tasks as $task) {
				if ($task["param"][0]["key"]) {
					$this->view->history(array("obj" => $task));
				}
			}
		} elseif (isset($_GET["tag"])) {
			$this->view->setTitle("Тег: " . htmlspecialchars($_GET["tag"]));
			
			$ai = $advinfo->getAIFromTag($_GET["tag"]);

			$this->view->setMainContent("<b>Тег:</b> " . htmlspecialchars($_GET["tag"]));

			foreach($ai as $part) {
				$aiinfo = $advinfo->getaiinfo();
				/*
				if ($ainfo = json_decode($part["val"], true)) {
					$part["val"] = null;
					foreach($ainfo as $key=>$val) {
						$part["val"] .= "<b>" . $key . "</b>: " . $val . "<br />";
					}
				}
				*/
				$this->view->ai(array("ai" => $part, "info" => $aiinfo));
			}
		} elseif ( (isset($this->args[1])) and ($this->args[1] == "add") ) {
			$this->view->setTitle("Добавить информацию");

			$this->view->kb_add();
		} else {
			$this->view->setTitle("Теги");
			
			$templates = $tpl->getTemplates();
			$id = count($templates);
			$templates[$id]["id"] = 0;
			$templates[$id]["name"] = "Заметки";
			$list = $advinfo->getAi();
			
			for($i=0; $i<count($list); $i++) {
				if ($list[$i]["oid"] == "0") {
					$list[$i]["name"] = "Заметки";
				}
			}

			$sortlist = array();
			foreach($templates as $template) {
				foreach($list as $kb) {
					if ($kb["name"] == $template["name"]) {
						$sortlist[$template["name"]][] = $kb["tag"];
					}
				}
			}

			$this->print_array($sortlist);

			$this->view->kb_tree(array("list" => $this->tree));
		}

		 
		$this->view->showPage();
	}
}
?>
