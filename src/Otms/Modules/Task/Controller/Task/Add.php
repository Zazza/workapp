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

use Otms\Modules\Task\Controller\Task;
use Otms\Modules\Objects\Model\Object;

/**
 * Controller\Task\Add class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Add extends Task {

	function index() {
		
		if (isset($this->registry["get"]["sub"])) {
			$this->view->setTitle("Создать задачу (подзадача к №" . $this->registry["get"]["sub"] . ")");
		} else {
			$this->view->setTitle("Создать задачу");
		}
		
		$this->view->setLeftContent($this->view->render("left_tt", array()));
		$this->view->setLeftContent($this->view->render("left_attach", array()));

		//Сохранение в черновики
		if (isset($_POST["draft"])) {
				
			$_POST["task"] = $_POST["textfield"];
				
			if ( (isset($_GET["oid"])) and ($_GET["oid"] != "") ) {
				$oid = $_GET["oid"];
			} elseif (isset($_POST["selObjHid"])) {
				$oid = $_POST["selObjHid"];
			} else {
				$oid = 0;
			}

			$this->registry["task"]->addDraft($oid, $_POST);
				
			$this->view->refresh(array("timer" => "1", "url" => "task/draft/"));
				
			// submit создания задачи
		} elseif (isset($_POST["submit"])) {

			if ( (isset($_GET["oid"])) and ($_GET["oid"] != "") ) {
				$oid = $_GET["oid"];
			} elseif (isset($_POST["selObjHid"])) {
				$oid = $_POST["selObjHid"];
			} else {
				$oid = 0;
			}

			$_POST["task"] = $_POST["textfield"];
			unset($_POST["textfield"]);

			if ($tid = $this->registry["task"]->addTask($oid, $_POST)) {
				$this->registry["task"]->spamUsers("Новая задача", $tid);

				$this->view->refresh(array("timer" => "1", "url" => "task/show/" . $tid . "/"));
			} else {
				$this->view->setMainContent("<p style='margin: 30px 0 0 50px; color: red'>Заполните текст задачи!</span>");

				$this->view->refresh(array("timer" => "1", "url" => "task/add/?oid=" . $oid . "&date=" . $_GET["date"]));
			}
			// submit создания задачи с владки "пользователи"
		} elseif (isset($_GET["addUsersTask"])) {

			$rusers = $this->registry["user"]->getUsers();

			$issRusers = array(); $k = 0;
			foreach($_GET as $key=>$part) {
					
				if ($key == "ruser") {
					foreach($part as $val) {
						$row = $this->registry["user"]->getUserInfo($val);
							
						$k++;
						 
						$issRusers[$k]["desc"] = '<p><span style="font-size: 11px; margin-right: 10px;" id="udesc[' . $row["uid"] . ']">' . $row["name"] . ' ' . $row["soname"] . '</span>';
						$issRusers[$k]["desc"] .= '<input id="uhid[' . $row["uid"] . ']" type="hidden" name="ruser[]" value="' . $row["uid"] . '" /></p>';
					}
				}
					
				if ($key == "gruser") {
					foreach($part as $val) {
						$gname = $this->registry["user"]->getSubgroupName($val);
							
						$k++;
							
						$issRusers[$k]["desc"] = '<p style="font-size: 11px; margin-right: 10px">' . $gname . '<input type="hidden" name="gruser[]" value="' . $val . '" /></p>';

					}
				}
					
				if ($key == "rall") {
					$k++;

					$issRusers[$k]["desc"] = '<p style="font-size: 11px; margin-right: 10px">Все<input type="hidden" name="rall" value="1" /></p>';
				}
			}

			$this->view->tt_add(array("oid" => "", "obj" => "", "now_date" => date("Y-m-d"), "now_time" => date("H:i:s"), "issRusers" => $issRusers));

		} else {

			$rusers = $this->registry["user"]->getUsers();

			$object = new Object();
			if (isset($_GET["oid"])) {
				$oid = $_GET["oid"];
				$obj = $object->getShortObject($_GET["oid"]);
			} else {
				$oid = "";
				$obj = "";
			}

			if (isset($_GET["date"])) {
				$data[0]["startdate"] = date("Y", strtotime($_GET["date"])) . "-" . date("m", strtotime($_GET["date"])) . "-" . date("d", strtotime($_GET["date"]));
				$data[0]["starttime"] = "00:00:00";
			} else {
				$data[0]["startdate"] = date("Y-m-d");
				$data[0]["starttime"] = date("H:i:s");
			}
			
			$data[0]["deadline"] = 1;

			$group = null;
			if(isset($_GET["group"])) {
				$group = $_GET["group"];
			}

			$this->view->tt_add(array("oid" => $oid, "rusers" => $rusers, "obj" => $obj, "data" => $data, "group" => $group));

		}

		$this->view->showPage();
	}
}
?>
