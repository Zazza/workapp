<?php

/**
 * This file is part of the Workapp project.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\System\Controller\Users;

use Otms\System\Controller\Users;

class Tasks extends Users {

	public function index() {
		$this->view->setTitle("Задачи пользователя");
			
		$mtask = $this->registry["task"];
			
		if (isset($_GET["clear"])) {
			unset($_SESSION["groups"]);
		}

		$mtaskSess = & $_SESSION["groups"];

		if (isset($_POST["submit"])) {

			$_POST["sday"] = htmlspecialchars($_POST["sday"]);
			$_POST["smonth"] = htmlspecialchars($_POST["smonth"]);
			$_POST["syear"] = htmlspecialchars($_POST["syear"]);
			$_POST["fday"] = htmlspecialchars($_POST["fday"]);
			$_POST["fmonth"] = htmlspecialchars($_POST["fmonth"]);
			$_POST["fyear"] = htmlspecialchars($_POST["fyear"]);

			$mtaskSess = $_POST;
		} else {
			if (!isset($mtaskSess)) {
				$mtaskSess = array();
			}
		}

		if (!isset($mtaskSess["sday"])) { $mtaskSess["sday"] = "01"; }
		if (!isset($mtaskSess["smonth"])) { $mtaskSess["smonth"] = "01"; }
		if (!isset($mtaskSess["syear"])) { $mtaskSess["syear"] = "2010"; }
		if (!isset($mtaskSess["fday"])) { $mtaskSess["fday"] = date("d"); }
		if (!isset($mtaskSess["fmonth"])) { $mtaskSess["fmonth"] = date("m"); }
		if (!isset($mtaskSess["fyear"])) { $mtaskSess["fyear"] = date("Y"); }

		$this->view->users_date(array("date" => $mtaskSess));

		if ($this->args[0] == "tasks") {

			if (isset($this->args[1])) {
				$mtask->links = "users/tasks/" . $this->args[1] . "/";
			}

			if (isset($_GET["page"])) {
				if (is_numeric($_GET["page"])) {
					if (!$mtask->setPage($_GET["page"])) {
						$this->__call("groups", "rusers");
					}
				}
			}
			 
			if (isset($this->args[1])) {
				$data = $mtask->getRusersStatFromRid($mtaskSess, $this->args[1]);

				$this->view->setMainContent("<p class='title'><b>Задач: " . $mtask->getOpenNum() . "(" . $mtask->getCloseNum() . ")</b></p>");

				if (!isset($this->args[2]) or ($this->args[2] == "page"))  {

					$taskshorts = null;
					foreach($data as $part) {
						$taskshorts .= $this->registry["module_task"]->taskshort($part["id"]);
					}
					
					$this->view->setMainContent($taskshorts);

					//Отобразим пейджер
					if (count($mtask->pager) != 0) {
						$this->view->pager(array("pages" => $mtask->pager));
					}
				}
			}
		}
	}
}