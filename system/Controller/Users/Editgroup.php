<?php
class Controller_Users_EditGroup extends Controller_Users {

	public function index() {
		if ($this->registry["ui"]["admin"]) {

			$this->view->setTitle("Пользователи");
			
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
?>