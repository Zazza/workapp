<?php
class Controller_Settings_Datatypes extends Modules_Controller {
	public function index() {
		if ($this->registry["ui"]["admin"]) {
			$this->view->setTitle("Типы данных");
			
			$this->view->setLeftContent($this->view->render("left_settings", array()));

			if (isset($this->get["id"])) {
	        	$this->view->settings_objects_datafield(array("id" => $this->get["id"]));
	        } else {        
		        $templates = new Model_Template();
		        $datatypes = $templates->getDataTypes();
		
		        $this->view->settings_objects_datatype(array("datatypes" => $datatypes));
	        }
		}
	}
}
?>