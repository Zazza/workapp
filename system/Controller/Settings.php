<?php
class Controller_Settings extends Modules_Controller {
	public function index() {
		if ($this->registry["ui"]["admin"]) {
			$this->view->setLeftContent($this->view->render("left_settings", array()));
			
			$this->view->settings_index();
		}
	}
}
?>