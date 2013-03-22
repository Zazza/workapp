<?php

/**
 * This file is part of the Workapp project.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\System\Controller\Users;

use Otms\System\Controller\Users;

class EditGroup extends Users {

	public function index() {
		if ($this->registry["ui"]["admin"]) {

			$this->view->setTitle("Users");
			
			$this->view->setLeftContent($this->view->render("left_users", array()));
			 
			if (isset($this->args[1])) {
				$gname = $this->registry["user"]->getGroupName($this->args[1]);
				 
				if (isset($_POST['editgroup'])) {
					$this->registry["user"]->editGroupName($this->args[1], $_POST["group"]);
					 
					$this->view->refresh(array("timer" => "1", "url" => "users/"));
				} else {
					$this->view->users_editgroup(array("gname" => $gname));
				}
			}
		}
	}
}