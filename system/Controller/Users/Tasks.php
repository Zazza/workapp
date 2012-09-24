<?php
class Controller_Users_Tasks extends Controller_Users {

	public function index() {
		$this->view->setTitle("Задачи пользователя");
			
		$groups = $this->registry["tt_groups"];
			
		if (isset($_GET["clear"])) {
			unset($_SESSION["groups"]);
		}

		$groupsSess = & $_SESSION["groups"];

		if (isset($_POST["submit"])) {

			$_POST["sday"] = htmlspecialchars($_POST["sday"]);
			$_POST["smonth"] = htmlspecialchars($_POST["smonth"]);
			$_POST["syear"] = htmlspecialchars($_POST["syear"]);
			$_POST["fday"] = htmlspecialchars($_POST["fday"]);
			$_POST["fmonth"] = htmlspecialchars($_POST["fmonth"]);
			$_POST["fyear"] = htmlspecialchars($_POST["fyear"]);

			$groupsSess = $_POST;
		} else {
			if (!isset($groupsSess)) {
				$groupsSess = array();
			}
		}

		if (!isset($groupsSess["sday"])) { $groupsSess["sday"] = "01"; }
		if (!isset($groupsSess["smonth"])) { $groupsSess["smonth"] = "01"; }
		if (!isset($groupsSess["syear"])) { $groupsSess["syear"] = "2010"; }
		if (!isset($groupsSess["fday"])) { $groupsSess["fday"] = date("d"); }
		if (!isset($groupsSess["fmonth"])) { $groupsSess["fmonth"] = date("m"); }
		if (!isset($groupsSess["fyear"])) { $groupsSess["fyear"] = date("Y"); }

		$this->view->users_date(array("date" => $groupsSess));

		if ($this->args[0] == "tasks") {

			if (isset($this->args[1])) {
				$groups->links = "users/tasks/" . $this->args[1] . "/";
			}

			if (isset($_GET["page"])) {
				if (is_numeric($_GET["page"])) {
					if (!$groups->setPage($_GET["page"])) {
						$this->__call("groups", "rusers");
					}
				}
			}
			 
			if (isset($this->args[1])) {
				$data = $groups->getRusersStatFromRid($groupsSess, $this->args[1]);

				$this->view->setMainContent("<p class='title'><b>Задач: " . $groups->open . "(" . $groups->close . ")</b></p>");

				if (!isset($this->args[2]) or ($this->args[2] == "page"))  {

					$taskshorts = null;
					foreach($data as $part) {
						$taskshorts .= $this->registry["module_tt"]->taskshort($part["id"]);
					}
					
					$this->view->setMainContent($taskshorts);

					//Отобразим пейджер
					if (count($groups->pager) != 0) {
						$this->view->pager(array("pages" => $groups->pager));
					}
				}
			}
		}
	}
}
?>