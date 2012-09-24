<?php
class Controller_Settings_Ttgroups extends Modules_Controller {	
	public function index() {
        if ($this->registry["ui"]["admin"]) {
        	
        	$this->view->setTitle("Проекты");
        	
        	$this->view->setLeftContent($this->view->render("left_settings", array()));

	        if (isset($this->args[1])) {
            	if($this->args[1] == "add") {
            	   	if (isset($_POST['submit_group'])) {
	            	    $this->registry["tt"]->addGroups($_POST["group"]);
	                
	                	$this->view->refresh(array("timer" => "1", "url" => "settings/ttgroups/"));
	        		} else {
	        			$this->view->settings_groups_addgrouptt();
	        		}
                } elseif ($this->args[1] == "edit") {
                	if (isset($this->args[2])) {
                    
                    	if(isset($_POST['submit_group'])) {
                        	$this->registry["tt"]->editGroupName($this->args[2], $_POST["group"]);
                                
                            $this->view->refresh(array("timer" => "1", "url" => "settings/ttgroups/"));
                        } else {
                            
                        	$name = $this->registry["tt"]->getGroupName($this->args[2]);
                            
                            $this->view->settings_groups_editgrouptt(array("id" => $this->args[2], "name" => $name));
                        }
                    }
                }
            } else {
            	$groups = $this->registry["tt"]->getGroups();
            	
            	$this->view->settings_groups_tt(array("group" => $groups));
            }
        }
	}
}
?>