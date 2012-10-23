<?php

/**
 * This file is part of the Workapp project.
 *
 * Object Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Objects\Controller\Objects;

use Otms\Modules\Objects\Controller\Objects;
use Otms\Modules\Objects\Model;

/**
 * Controller\Objects\Show class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Show extends Objects {
	
	public function index() {            
        $this->view->setTitle("Объект");
        
        $this->view->setLeftContent($this->view->render("left_objects", array()));

        $object = new Model\Object();
        $ai = new Model\Ai();
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