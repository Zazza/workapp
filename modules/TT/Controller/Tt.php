<?php
class Controller_Tt extends Modules_Controller {

	public function __construct($config) {
		parent::__construct($config);

		$this->registry->set("getNumMeTasks", $this->registry["tt"]->getNumMeTasks());
		$this->registry->set("getNumTasks", $this->registry["tt"]->getNumTasks());
		$this->registry->set("draftttnum", $this->registry["tt"]->getDraftNumTasks($this->registry["ui"]["id"]));
	}

	public function showTasks($tasks) {
		$task = array();
		
		$object = new Model_Object();

		if (count($tasks) == 0) {
			$this->view->setMainContent("<p style='margin: 10px'>Задачи не найдены</p>");
		}

		foreach($tasks as $part) {

			if ($data = $this->registry["tt"]->getTask($part["id"])) {
				$numComments = $this->registry["tt"]->getNumComments($part["id"]);
				$newComments = $this->registry["tt"]->getNewCommentsFromTid($part["id"]);

				if ($data[0]["remote_id"] == 0) {
					$author = $this->registry["user"]->getUserInfo($data[0]["who"]);
				} else {
					$author = $this->registry["tt_user"]->getRemoteUserInfo($data[0]["who"]);
				}

				$ruser = array();

				foreach($data as $val) {
					if (isset($val["uid"])) {
						if ($val["uid"] != 0) {
							$user = $this->registry["user"]->getUserInfo($val["uid"]);

							$ruser[] = "<a style='cursor: pointer' onclick='getUserInfo(" . $val["uid"] . ")'>" . $user["name"] . " " . $user["soname"] . "</a>";
						}
					}

					if (isset($val["rgid"])) {
						if ($val["rgid"] != 0) {
							$ruser[] = "<span style='color: #5D7FA6'><b>" . $this->registry["user"]->getSubgroupName($val["rgid"]) . "</b></span>";
						}
					}

					if ($val["all"] == 1) {
						$ruser[] = "<span style='color: #D9A444'><b>Все</b></span>";
					}
				}

				$cuser = $this->registry["user"]->getUserInfo($data[0]["cuid"]);

				$notObj = true;
				if (!$obj = $object->getShortObject($data[0]["oid"])) {
					$notObj = false;
				}

				//$task[] = $this->view->render("tt_tasktable", array("data" => $data, "author" => $author, "ruser" => $ruser, "cuser" => $cuser, "notObj" => $notObj, "obj" => $obj, "numComments" => $numComments, "newComments" => $newComments));
				$task[] = $this->view->render("tt_task", array("data" => $data, "author" => $author, "ruser" => $ruser, "cuser" => $cuser, "notObj" => $notObj, "obj" => $obj, "numComments" => $numComments, "newComments" => $newComments));

				unset($ruser);
			} else {
				$this->view->setMainContent("<p style='margin: 10px 0'>Задача не найдена</p>");
			}
		}
		
		$this->view->tt_taskpage(array("tasks" => $task));

		//Отобразим пейджер
		if (count($this->registry["tt"]->pager) != 0) {
			$this->view->pager(array("pages" => $this->registry["tt"]->pager));
		}
			
		$this->view->showPage();
	}

	public function index() { //print_r($this->registry);
		$this->view->setTitle("Задачи");

		$this->view->setLeftContent($this->view->render("left_tt", array()));

		$cal = & $_SESSION["cal"];

		if (isset($_GET["page"])) {
			if (is_numeric($_GET["page"])) {
				if (!$this->registry["tt"]->setPage($_GET["page"])) {
					$this->__call("tt", "index");
				}
			}
		}
		
		$sortmytt = & $_SESSION["sortmytt"];
		if ( (!isset($sortmytt["sort"])) or (!isset($sortmytt["id"])) ) {
			$sortmytt["sort"] = "date";
			$sortmytt["id"] = "false";
		}

		$sort_groups = $this->registry["tt"]->getSortGroups();
		$this->view->setLeftContent($this->view->render("left_sortmytt", array("sort" => $sortmytt, "sg" => $sort_groups)));
		//$top[0] = $this->view->render("top_tt", array());
		//$top[1] = $this->view->render("top_sort", array("sort" => $sortmytt, "sg" => $sort_groups));
		//$this->view->top_top(array("blocks" => $top));

		$this->registry["tt"]->links = "tt/";

		$tasks = $this->registry["tt"]->getTasks();

		$this->showTasks($tasks);
	}
}
?>