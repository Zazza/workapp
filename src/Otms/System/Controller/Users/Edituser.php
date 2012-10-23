<?php

/**
 * This file is part of the Workapp project.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\System\Controller\Users;

use Otms\System\Model\Validate;

use Otms\System\Controller\Users;

class Edituser extends Users {

	public function index() {
		if ($this->registry["ui"]["admin"]) {

			$this->view->setTitle("Пользователи");
			
			$this->view->setLeftContent($this->view->render("left_users", array()));

			if (isset($_POST['edituser'])) {
				$group = $this->registry["user"]->getGroups();
				$data = $this->registry["user"]->getUserInfo($this->args[1]);

				$validate = new Validate();
				 
				$err = array();
				if ($_POST["login"] != $data["login"]) {
					if ($txt = $validate->login($_POST["login"])) { $err[] = $txt; };
				}
				if ($txt = $validate->email($_POST["email"])) { $err[] = $txt; };
				if ($txt = $validate->name($_POST["name"])) { $err[] = $txt; };
				if ($txt = $validate->soname($_POST["soname"])) { $err[] = $txt; };
				if ($data["pass"] != $_POST["pass"]) {
					if ($txt = $validate->password($_POST["pass"])) { $err[] = $txt; };
				}
				
				if (!is_numeric($_POST["quota_val"])) {
					$res_val = 100;
				} else {
					$val = $_POST["quota_val"];
				}
				if ($_POST["quota_unit"] == "mb") {
					$res_val = $val * 1024 * 1024;
				}
				if ($_POST["quota_unit"] == "gb") {
					$res_val = $val * 1024 * 1024 * 1024;
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
					 
					$uid = $this->registry["user"]->editUser($this->args[1], $_POST["login"], $res_val, $_POST["name"], $_POST["soname"], $_POST["signature"], $_POST["email"], $notify, $_POST["time_notify"], $email_for_task);
					if ($data["pass"] != $_POST["pass"]) {
						$this->registry["user"]->editUserPass($this->args[1], $_POST["pass"]);
					}
					 
					$this->registry["user"]->editUserPriv($this->args[1], $_POST["priv"], $_POST["gid"]);

					$this->view->refresh(array("timer" => "1", "url" => "users/"));
				} else {
					$_POST["uid"] = $data["uid"];
					$this->view->users_edituser(array("group" => $group, "err" => $err, "post" => $_POST));
				}
			} else {
				$data = $this->registry["user"]->getUserInfo($this->args[1]);
				
				if (isset($data["id"])) {
					$group = $this->registry["user"]->getGroups();
	
					if ($data["admin"]) {
						$data["priv"] = "admin";
					}
					
					$quota = $data["quota"];
					
					if (($quota / 1024 / 1024) > 1) {
						$data["quota_val"] = round($quota / 1024 / 1024, 2);
						$data["quota_unit"] = "mb";
					};
					if (($quota / 1024 / 1024 / 1024) > 1) {
						$data["quota_val"] = round($quota / 1024 / 1024 / 1024, 2);
						$data["quota_unit"] = "gb";
					};
					 
					$this->view->users_edituser(array("post" => $data, "group" => $group));
				} else {
					$this->view->refresh(array("timer" => "1", "url" => ""));
				}
			}
		}
	}
}