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
use Otms\System\Model;

class Tasks extends Find {

	public function index() {

        $this->view->setTitle("Search");
        
        $this->view->setLeftContent($this->view->render("left_find", array("num" => $this->numFind)));
       
        $find = new Model\Find();
        $object = new Object();
        
        if (isset($this->findSess["string"])) {
            
            $this->view->setMainContent("<p style='font-weight: bold; margin-bottom: 20px'>Поиск: " . $this->findSess["string"] . "</p>");

        	if (isset($_GET["page"])) {
    			if (is_numeric($_GET["page"])) {
    				if (!$find->setPage($_GET["page"])) {
    					$this->__call("find", "tasks");
    				}
    			}
    		}
    		
    		$find->links = "find/" . $this->args[0] . "/";
            
            $text = substr($this->findSess["string"], 0, 64);
			$text = explode(" ", $text);

            $findArr = $find->findTroubles($text);
            
            if (!isset($this->args[1]) or ($this->args[1] == "page"))  {
                
                foreach($findArr as $part) {
                    
                    if ($data = $this->registry["task"]->getTask($part["id"])) {
                    	$numComments = $this->registry["task"]->getNumComments($part["id"]);
                    	$newComments = $this->registry["task"]->getNewCommentsFromTid($part["id"]);
                    	
                    	if ($data[0]["remote_id"] == 0) {
                   			$author = $this->registry["user"]->getUserInfo($data[0]["who"]);
                    	} else {
                    		$author = $this->registry["task_user"]->getRemoteUserInfo($data[0]["who"]);
                    	}
                    	
                    	$ruser = array();
                    	
                    	foreach($data as $val) {
                    		if (isset($val["uid"])) {
                    			if ($val["uid"] != 0) {
                    				$user = $this->registry["user"]->getUserInfo($val["uid"]);
                     	
                    				$ruser[] = "<a style='cursor: pointer' onclick='getUserInfo(" . $val["uid"] . ")'>" . $user["name"] . " " . $user["soname"] . "</a>";
                    			}
                    		}
                    	
                    		if (isset($val["rgid"])) {
                    			if ($val["rgid"] != 0) {
                    				$ruser[] = "<span style='color: #5D7FA6'><b>" . $this->registry["user"]->getSubgroupName($val["rgid"]) . "</b></span>";
                    			}
                    		}
                    	
                    		if ($val["all"] == 1) {
                    			$ruser[] = "<span style='color: #D9A444'><b>Все</b></span>";
                    		}
                    	}
                    	
                    	$cuser = $this->registry["user"]->getUserInfo($data[0]["cuid"]);
                    	
                    	$notObj = true;
                    	if (!$obj = $object->getShortObject($data[0]["oid"])) {
                    		$notObj = false;
                    	}
                    	
                    	$content = $this->registry["module_task"]->renderTask($data, $author, $ruser, $cuser, $notObj, $obj, $numComments, $newComments);
                    	$this->view->setMainContent($content);
                    }
                }
            
                //Отобразим пейджер
    			if (count($find->pager) != 0) {
    				$this->view->pager(array("pages" => $find->pager));
    			}
            }
        }
    }
}