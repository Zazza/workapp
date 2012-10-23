<?php

/**
 * This file is part of the Workapp project.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\System\Controller;

use Engine\Controller;
use Otms\System\Model\Ui;
use Otms\System\Model\Validate;

class Profile extends Controller {

	public function index() {
		$this->view->setTitle("Учётная запись");

		$ui = new Ui();

		if (isset($_POST["upload_avatar"])) {
			$err = $ui->saveAvatar($_FILES["filename"]);
			if ($err != null) {
				$this->view->setMainContent("<div style='border: 1px solid red; background-color: #faa; padding: 4px 10px; margin-bottom: 20px; width: 400px'>" . $err . "</div>");
			} else {
				$this->registry->remove("auth");
				$this->registry->remove("ui");
				$this->registry->remove("getNumMeTasks");
				$this->registry->remove("getNumTasks");
				$loginSession = & $_SESSION["login"];
				if (isset($loginSession["id"])) {
					$ui->getInfo($loginSession);
				}
			}
		}

		if (isset($_POST['editprofile'])) {
			$data = $this->registry["ui"];
			 
			$validate = new Validate();

			$err = array();
			if ($_POST["login"] != $this->registry["ui"]["login"]) {
				if ($txt = $validate->login($_POST["login"])) { $err[] = $txt; };
			}
			if ($txt = $validate->email($_POST["email"])) { $err[] = $txt; };
			if ($txt = $validate->name($_POST["name"])) { $err[] = $txt; };
			if ($txt = $validate->soname($_POST["soname"])) { $err[] = $txt; };
			if ($data["pass"] != $_POST["pass"]) {
				if ($txt = $validate->password($_POST["pass"])) { $err[] = $txt; };
			}

			if (count($err) == 0) {

				if (!isset($_POST["notify"])) {
					$notify = 0;
				} else {
					$notify = 1;
				}
				
				if (isset($_POST["email_for_task"])) {
					$email_for_task = 1;
				} else {
					$email_for_task = 0;
				}

				$uid = $ui->editUser($this->registry["ui"]["id"], $_POST["login"], $_POST["name"], $_POST["soname"], $_POST["signature"], $_POST["email"], $notify, $_POST["time_notify"], $email_for_task);
				if ($data["pass"] != $_POST["pass"]) {
					$ui->editUserPass($this->registry["ui"]["id"], $_POST["pass"]);
				}

				$ui->editAdvUser($_POST["icq"], $_POST["skype"], $_POST["adres"], $_POST["phone"]);

				$this->view->refresh(array("timer" => "1", "url" => "profile/"));
			} else {
				$this->view->profile(array("err" => $err, "post" => $_POST));
			}
		} else {
			$data = $this->registry["ui"];

			$this->view->profile(array("post" => $data));
		}
    }
}