<?php
class Controller_Users_Adduser extends Controller_Users {

	public function index() {
		if ($this->registry["ui"]["admin"]) {

			$this->view->setTitle("Пользователи");
			
			$this->view->setLeftContent($this->view->render("left_users", array()));

			if (isset($_POST['adduser'])) {
				$validate = new Model_Validate();
				 
				$err = array();
				if ($txt = $validate->login($_POST["login"])) { $err[] = $txt; };
				if ($txt = $validate->email($_POST["email"])) { $err[] = $txt; };
				if ($txt = $validate->name($_POST["name"])) { $err[] = $txt; };
				if ($txt = $validate->soname($_POST["soname"])) { $err[] = $txt; };
				if ($txt = $validate->password($_POST["pass"])) { $err[] = $txt; };
				
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

					$uid = $this->registry["user"]->addUser($_POST["login"], $_POST["pass"], $res_val, $_POST["name"], $_POST["soname"], $_POST["signature"], $_POST["email"], $notify, $_POST["time_notify"], $email_for_task);
					$this->registry["user"]->addUserPriv($uid, $_POST["priv"], $_POST["gid"]);
					 
					$this->view->refresh(array("timer" => "1", "url" => "users/"));
				} else {
					$group = $this->registry["user"]->getGroups();
					$this->view->users_adduser(array("group" => $group, "err" => $err, "post" => $_POST));
				}
			} else {
				$group = $this->registry["user"]->getGroups();
				$post["time_notify"] = "08:00:00";
				$this->view->users_adduser(array("group" => $group, "post" => $post));
			}
		}
	}
}
?>