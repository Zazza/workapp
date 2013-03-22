<?php

/**
 * This file is part of the Workapp project.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\System\Controller\Find;

use Otms\System\Controller\Find;
use Otms\Modules\Objects\Model\Object;
use Otms\Modules\Objects\Model\Ai;
use Otms\System\Model;

class Adv extends Find {

	public function index() {
		
        $this->view->setTitle("Search");
        
        $this->view->setLeftContent($this->view->render("left_find", array("num" => $this->numFind)));
       
        $find = new Model\Find();
        $object = new Object();
        $ai = new Ai();
        
        if (isset($this->findSess["string"])) {
            
            $this->view->setMainContent("<p style='font-weight: bold; margin-bottom: 20px'>Поиск: " . $this->findSess["string"] . "</p>");

            if (isset($this->args[1])) {
    			if ( ($this->args[1] == "page") and (isset($this->args[2])) ) {
    				if (!$find->setPage($this->args[2])) {
    					$this->__call("objects", "index");
    				}
    			}
    		}
    		
    		$find->links = "/" . $this->args[0] . "/";
            
            $text = substr($this->findSess["string"], 0, 64);
			$text = explode(" ", $text);

            $findArr = $find->findAdvs($text);
            
            if (!isset($this->args[1]) or ($this->args[1] == "page"))  {
                
                foreach($findArr as $part) {
                    
                    $numTroubles = $object->getNumTroubles($part["id"]);
                    $obj = $object->getShortObject($part["id"]);
                    $advInfo = $ai->getAdvancedInfo($part["id"]);
                    $numAdvInfo = $ai->getNumAdvancedInfo($part["id"]);
                    $objects = $this->registry["module_objects"]->renderObject($this->registry["ui"], $obj, $advInfo, $forms, $numAdvInfo, $numTroubles);
                    $this->view->setMainContent($objects);
                }
            
                //Отобразим пейджер
    			if (count($find->pager) != 0) {
    				$this->view->pager(array("pages" => $find->pager));
    			}
            }
        }
    }
}