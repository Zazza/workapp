<?php

/**
 * This file is part of the Workapp project.
 *
 * Object Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Objects\Controller\Objects;

use Otms\Modules\Objects\Controller\Objects;
use Otms\Modules\Objects\Model;

/**
 * Controller\Objects\Sub class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Sub extends Objects {

	public function index() {
		$this->view->setTitle("Просмотр объектов");
		
		$this->view->setLeftContent($this->view->render("left_objects", array()));

		if (isset($this->args[1])) {

			if (isset($_GET["page"])) {
				if (is_numeric($_GET["page"])) {
					if (!$this->object->setPage($_GET["page"])) {
						$this->__call("objects", "list");
					}
				}
			}

			$ai = new Model\Ai();
			$forms = $ai->getForms();

			$this->object->links = "objects/" . $this->args[0] . "/" . $this->args[1] . "/";
			
			$sort = & $_SESSION["sort"];
			
			if (isset($_GET["clear"])) {
				unset($sort["sort"]);
			}
			
			if ( (isset($_POST)) and (count($_POST) > 0) ) {
				$sort_flag = true;
				$sort = $_POST;
				$data = $this->object->getObjectsSearch($this->args[1], $_POST);
			} else if ($sort["sort"] != "") {
				$sort_flag = true;
				$data = $this->object->getObjectsSearch($this->args[1], $sort);
			} else {
				$sort_flag = false;
				$data = $this->object->getObjects($this->args[1]);
			}
			
			$template = new Model\Template();
			
			$arr_objs = array(); $i=0;
			foreach($data as $part) {
				$arr_objs[$i] = $this->object->getShortObject($part["id"]);
				$arr_objs[$i]["ai"] = $ai->getAdvancedInfo($part["id"]);
				$i++;
			}
			
			if (isset($this->registry["module_mail"])) {
				$mail = true;
			} else {
				$mail = false;
			}
			
			$fields = $template->getTypeTemplate($this->args[1]);
			
			$this->view->objects_subgroups(array("objs" => $arr_objs, "mail" => $mail, "forms" => $forms, "gid" => $this->args[1], "fields" => $fields, "sort_flag" => $sort_flag));
			
			//Отобразим пейджер
			if (count($this->object->pager) != 0) {
				$this->view->pager(array("pages" => $this->object->pager));
			}
		}

		$this->view->showPage();
	}
	
}
?>
