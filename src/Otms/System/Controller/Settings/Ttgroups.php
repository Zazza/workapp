<?php

/**
 * This file is part of the Workapp project.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\System\Controller\Settings;

use Engine\Controller;

class Ttgroups extends Controller {	
	public function index() {
        if ($this->registry["ui"]["admin"]) {
        	
        	$this->view->setTitle("Проекты");
        	
        	$this->view->setLeftContent($this->view->render("left_settings", array()));

	        if (isset($this->args[1])) {
            	if($this->args[1] == "add") {
            	   	if (isset($_POST['submit_group'])) {
	            	    $this->registry["task"]->addGroups($_POST["group"]);
	                
	                	$this->view->refresh(array("timer" => "1", "url" => "settings/ttgroups/"));
	        		} else {
	        			$this->view->settings_groups_addgrouptt();
	        		}
                } elseif ($this->args[1] == "edit") {
                	if (isset($this->args[2])) {
                    
                    	if(isset($_POST['submit_group'])) {
                        	$this->registry["task"]->editGroupName($this->args[2], $_POST["group"]);
                                
                            $this->view->refresh(array("timer" => "1", "url" => "settings/ttgroups/"));
                        } else {
                            
                        	$name = $this->registry["task"]->getGroupName($this->args[2]);
                            
                            $this->view->settings_groups_editgrouptt(array("id" => $this->args[2], "name" => $name));
                        }
                    }
                }
            } else {
            	$groups = $this->registry["task"]->getGroups();
            	
            	$this->view->settings_groups_tt(array("group" => $groups));
            }
        }
	}
}