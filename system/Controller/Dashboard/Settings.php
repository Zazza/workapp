<?php
class Controller_Dashboard_Settings extends Engine_Controller {
	public function index() {
		$this->view->setTitle("Настройки уведомлений");
		
		$dashboard = new Model_Dashboard();
		
		$this->view->dashboard_settings(array());
	}
}
?>