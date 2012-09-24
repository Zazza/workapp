<?php
class Controller_Objects_Show extends Controller_Objects {
	
	public function index() {            
        $this->view->setTitle("Объект");
        
        $this->view->setLeftContent($this->view->render("left_objects", array()));

        $object = new Model_Object();
        $ai = new Model_Ai();
        $forms = $ai->getForms();
        
        if ($obj = $object->getShortObject($this->args[1])) {
        	
        	if (isset($this->registry["module_mail"])) {
        		$mail = true;
        	} else {
        		$mail = false;
        	}
            
            $numTroubles = $object->getNumTroubles($this->args[1]);
            $advInfo = $ai->getAdvancedInfo($this->args[1]);
            $numAdvInfo = $ai->getNumAdvancedInfo($this->args[1]);
            $this->view->objectMain(array("ui" => $this->registry["ui"], "mail" => $mail, "obj" => $obj, "advInfo" => $advInfo, "forms" => $forms, "numAdvInfo" => $numAdvInfo, "numTroubles" => $numTroubles));
        } else {
            $this->view->setMainContent("<p>Объект не найден</p>");
        }
        
        $this->view->showPage();
	}
}
?>