<?php

/**
 * This file is part of the Workapp project.
 *
 * Task Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Task\Controller;

use Engine\Modules\Controller;
use Otms\Modules\Objects\Model\Object;

/**
 * Controller\Task class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Task extends Controller {

	public function __construct($config) {
		parent::__construct($config);

		$this->registry->set("getNumMeTasks", $this->registry["task"]->getNumMeTasks());
		$this->registry->set("getNumTasks", $this->registry["task"]->getNumTasks());
		$this->registry->set("draftttnum", $this->registry["task"]->getDraftNumTasks($this->registry["ui"]["id"]));
	}

	public function showTasks($tasks) {
		$task = array();

		$object = new Object();

		if (count($tasks) == 0) {
			$this->view->setMainContent("<p style='margin: 10px'>Задачи не найдены</p>");
		}

		foreach($tasks as $part) {

			if ($data = $this->registry["task"]->getTask($part["id"])) {
				$numComments = $this->registry["task"]->getNumComments($part["id"]);
				$newComments = $this->registry["task"]->getNewCommentsFromTid($part["id"]);

				if ($data[0]["remote_id"] == 0) {
					$author = $this->registry["user"]->getUserInfo($data[0]["who"]);
				} else {
					$author = $this->registry["task_user"]->getRemoteUserInfo($data[0]["who"]);
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

				$task[] = $this->view->render("tt_task", array("data" => $data, "author" => $author, "ruser" => $ruser, "cuser" => $cuser, "notObj" => $notObj, "obj" => $obj, "numComments" => $numComments, "newComments" => $newComments));

				unset($ruser);
			} else {
				$this->view->setMainContent("<p style='margin: 10px 0'>Задача не найдена</p>");
			}
		}
		
		$this->view->tt_taskpage(array("tasks" => $task));

		//Отобразим пейджер
		if (count($this->registry["task"]->pager) != 0) {
			$this->view->pager(array("pages" => $this->registry["task"]->pager));
		}
			
		$this->view->showPage();
	}

	public function index() {
		$this->view->setTitle("Задачи");

		$this->view->setLeftContent($this->view->render("left_tt", array()));

		$cal = & $_SESSION["cal"];

		if (isset($_GET["page"])) {
			if (is_numeric($_GET["page"])) {
				if (!$this->registry["task"]->setPage($_GET["page"])) {
					$this->__call("task", "index");
				}
			}
		}
		
		$sortmytt = & $_SESSION["sortmytt"];
		if ( (!isset($sortmytt["sort"])) or (!isset($sortmytt["id"])) ) {
			$sortmytt["sort"] = "date";
			$sortmytt["id"] = "false";
		}

		$sort_groups = $this->registry["task"]->getSortGroups();
		$this->view->setLeftContent($this->view->render("left_sortmytt", array("sort" => $sortmytt, "sg" => $sort_groups)));
		$this->registry["task"]->links = "task/";

		$tasks = $this->registry["task"]->getTasks();

		$this->showTasks($tasks);
	}
}
?>