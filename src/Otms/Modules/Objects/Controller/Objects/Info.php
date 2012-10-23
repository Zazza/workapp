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
 * Controller\Objects\Info class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Info extends Objects {

	public function index() {
		$this->view->setTitle("Информация");
		
		$this->view->setLeftContent($this->view->render("left_objects", array()));

		$object = new Model\Object();
		$ai = new Model\Ai();
		 
		if ( (isset($_GET["oid"])) and (is_numeric($_GET["oid"])) ) {
			if (isset($this->args[1])) {
				if ($this->args[1] == "add") {
					$this->view->objects_addinfo(array("oid" => $_GET["oid"]));
				}
			}
		}
		 
		if ( (isset($_GET["oaid"])) and (is_numeric($_GET["oaid"])) ) {
			if (isset($this->args[1])) {
				if ($this->args[1] == "edit") {
					 
					$arr = $ai->getTags($_GET["oaid"]);
					$advInfo["tags"] = implode(", ", $arr);
					$advInfo["adv"] = $ai->getAdvanced($_GET["oaid"]);

					$this->view->objects_editinfo(array("aoid" => $_GET["oaid"], "ai" => $advInfo));
				}
			}
		}

		$this->view->showPage();
	}
}
?>