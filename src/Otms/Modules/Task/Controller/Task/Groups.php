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

use Otms\Modules\Task\Model\Groups;

use Otms\Modules\Task\Controller\Task;

/**
 * Controller\Task\Groups class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Groups extends Task {

	public function index() {
		$this->view->setTitle("Проекты");

		$this->view->setLeftContent($this->view->render("left_tt", array()));

		$groups = new Groups();

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

		$this->view->groups_date(array("date" => $groupsSess));

		if ( ($this->args[0] == "groups") or (!isset($this->args[0])) ) {

			if (isset($this->args[1])) {
				$groups->links = "task/groups/" . $this->args[1] . "/";
			}

			if (isset($_GET["page"])) {
				if (is_numeric($_GET["page"])) {
					if (!$groups->setPage($_GET["page"])) {
						$this->__call("groups", "groups");
					}
				}
			}

			if (isset($this->args[1])) {
				$data = $groups->getGroupsStatFromGroups($groupsSess, $this->args[1]);

				if (!isset($this->args[2]) or ($this->args[2] == "page"))  {

					foreach($data as $part) {

						if ($data = $this->registry["task"]->getTask($part["id"])) {
							
							if ($data[0]["remote_id"] == 0) {
								$author = $this->registry["user"]->getUserInfo($data[0]["who"]);
							} else {
								$author = $this->registry["task_user"]->getRemoteUserInfo($data[0]["who"]);
							}

							//$author = $this->registry["user"]->getUserInfo($data[0]["who"]);

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
							
							$cuser = $this->registry["user"]->getUserInfo($data[0]["cuid"]);

							$notObj = true;
							if (!$obj = $object->getShortObject($data[0]["oid"])) {
								$notObj = false;
							}

							$this->view->tt_taskshort(array("data" => $data, "author" => $author, "ruser" => $ruser, "cuser" => $cuser, "notObj" => $notObj, "obj" => $obj));

							unset($ruser);
						}
					}

					//Отобразим пейджер
					if (count($groups->pager) != 0) {
						$this->view->pager(array("pages" => $groups->pager));
					}

				}
			} else {
				$data = $groups->getGroupsStat($groupsSess);

				$this->view->groups_groups(array("data" => $data));
			}

		}

		$this->view->showPage();
	}
}
?>