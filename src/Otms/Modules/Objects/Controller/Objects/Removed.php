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
 * Controller\Objects\Removed class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Removed extends Objects {

	public function index() {
		if ($this->registry["ui"]["admin"]) {
			$this->view->setTitle("Viewing of remote objects");
			
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
				
				$data = $this->object->getTrashObjects($this->args[1]);
				
				$template = new Model\Template();
				
				$arr_objs = array(); $i=0;
				foreach($data as $part) {
					$arr_objs[$i] = $this->object->getShortObject($part["id"]);
					$arr_objs[$i]["ai"] = $ai->getAdvancedInfo($part["id"]);
					$i++;
				}
				
				$fields = $template->getTypeTemplate($this->args[1]);
				
				$this->view->objects_subgroupstrash(array("objs" => $arr_objs, "forms" => $forms, "gid" => $this->args[1], "fields" => $fields));
				
				//Отобразим пейджер
				if (count($this->object->pager) != 0) {
					$this->view->pager(array("pages" => $this->object->pager));
				}
			}
		}

		$this->view->showPage();
	}
	
}
?>
