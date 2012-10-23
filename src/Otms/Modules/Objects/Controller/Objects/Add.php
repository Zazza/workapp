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
 * Controller\Objects\Add class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Add extends Objects {

	public function index() {
		$this->view->setTitle("Добавить объект");
		
		$this->view->setLeftContent($this->view->render("left_objects", array()));

		$object = new Model\Object();

		if (isset($_POST["submit"])) {
			$object->addObject($_POST);

			$this->view->refresh(array("timer" => "1", "url" => "objects/sub/" . $this->args[1] . "/"));

		} else {
			if (isset($_GET["p"])) {
				$contact = & $_SESSION["contact"];
				if ((isset($contact["email"])) and ($contact["email"] != null)) {
					$email = $contact["email"];
				} else {
					$email = null;
				}
				$this->view->objects_add(array("pname" => $_GET["p"], "email" => $email));
			}
		}

		$this->view->showPage();
	}
}
?>