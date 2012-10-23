<?php

/**
 * This file is part of the Workapp project.
 *
 * Task Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Task\Controller\Task;

use Otms\Modules\Objects\Model\Object;

use Otms\Modules\Task\Controller\Task;

/**
 * Controller\Task\Draft class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Draft extends Task {

	public function index() {
		$this->view->setTitle("Черновики");
		
		$this->view->setLeftContent($this->view->render("left_tt", array()));
		//$top[0] = $this->view->render("top_tt", array());
		//$this->view->top_top(array("blocks" => $top));

		if (isset($_GET["page"])) {
			if (is_numeric($_GET["page"])) {
				if (!$this->registry["task"]->setPage($_GET["page"])) {
					$this->__call("task", "index");
				}
			}
		}

		$this->registry["task"]->links = "/";

		$tasks = $this->registry["task"]->getDrafts();

		if (count($tasks) == 0) {
			$this->view->setMainContent("<p>Черновиков нет</p>");
		}

		foreach($tasks as $part) {

			if ($data = $this->registry["task"]->getDraft($part["id"])) {

				$author = $this->registry["user"]->getUserInfo($data[0]["who"]);

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

				$object = new Object();
				$notObj = true;
				if (!$obj = $object->getShortObject($data[0]["oid"])) {
					$notObj = false;
				}

				$this->view->tt_task(array("type" => "draft", "data" => $data, "author" => $author, "ruser" => $ruser, "notObj" => $notObj, "obj" => $obj));

				unset($ruser);
			} else {
				$this->view->setMainContent("<p>Черновиков нет</p>");
			}
		}

		//Отобразим пейджер
		if (count($this->registry["task"]->pager) != 0) {
			$this->view->pager(array("pages" => $this->registry["task"]->pager));
		}

		$this->view->showPage();
	}
}
?>