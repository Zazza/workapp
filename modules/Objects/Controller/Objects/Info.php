<?php
class Controller_Objects_Info extends Controller_Objects {

	public function index() {
		$this->view->setTitle("Информация");
		
		$this->view->setLeftContent($this->view->render("left_objects", array()));

		$object = new Model_Object();
		$ai = new Model_Ai();
		 
		if ( (isset($_GET["oid"])) and (is_numeric($_GET["oid"])) ) {
			if (isset($this->args[1])) {
				if ($this->args[1] == "add") {
					$this->view->objects_addinfo(array("oid" => $_GET["oid"]));
				}
			}
		}
		 
		if ( (isset($_GET["oaid"])) and (is_numeric($_GET["oaid"])) ) {
			if (isset($this->args[1])) {
				if ($this->args[1] == "edit") {
					 
					$arr = $ai->getTags($_GET["oaid"]);
					$advInfo["tags"] = implode(", ", $arr);
					$advInfo["adv"] = $ai->getAdvanced($_GET["oaid"]);

					$this->view->objects_editinfo(array("aoid" => $_GET["oaid"], "ai" => $advInfo));
				}
			}
		}

		$this->view->showPage();
	}
}
?>