<?php
class Controller_Users_Addgroup extends Controller_Users {

	public function index() {
		if ($this->registry["ui"]["admin"]) {

			$this->view->setTitle("Пользователи");
			
			$this->view->setLeftContent($this->view->render("left_users", array()));

			if (isset($_POST['addgroup'])) {
				$this->registry["user"]->addGroups($_POST["new_group"]);

				$this->view->refresh(array("timer" => "1", "url" => "users/"));
			} else {
				$this->view->users_addgroup();
			}
		}
	}
}
?>