<?php
class Controller_Ajax_Tt extends Modules_Ajax {
    public function delGroup($params) {
        $gid = $params["gid"];
        
        $this->registry["tt"]->delGroup($gid);
    }
    
    public function delTemplate($params) {
        $id = $params["id"];
        
        $tpl = new Model_Template();
        $tpl->delTemplate($id);
    }

    public function getInfo($params) {
        $id = $params["id"];
        
        $object = new Model_Object();
        $data = $object->getObject($id);

        echo $this->view->render("objectInfo", array("data" => $data));
    }
    
    public function addAdvancedNote($params) {
    	$title = $params["title"];
    	$text = $params["text"];
    	$tags = htmlspecialchars($params["tags"]);
    
    	if ( ($title != "") and ($text != "") and ($tags != "") ) {
	    	$advinfo = new Model_Ai();
	    
	    	$oaid = $advinfo->addAdvanced("0", $title, $text);
	    
	    	$arr = explode(",", $tags);
	    	$arr = array_unique($arr);
	    	foreach($arr as $part) {
	    		$tag = trim($part);
	    		if ($tag != "") {
	    			$advinfo->addTags($oaid, $tag);
	    		}
	    	}
    	} else {
    		echo 'false';
    	}
    }

    public function addAdvanced($params) {
        $id = $params["id"];
        $title = $params["title"];
        $text = $params["text"];
        $tags = htmlspecialchars($params["tags"]);
        
        $advinfo = new Model_Ai();

        $oaid = $advinfo->addAdvanced($id, $title, $text);
        
        $arr = explode(",", $tags);
		$arr = array_unique($arr);
        foreach($arr as $part) {
            $tag = trim($part);
            if ($tag != "") {
                $advinfo->addTags($oaid, $tag);
            }
        }
    }
    
    public function delAdv($params) {
        $id = $params["id"];
        
        $advinfo = new Model_Ai();
        
        $advinfo->delAdvanced($id);
    }
    
    public function editAdvanced($params) {
        $oid = $params["oid"];
        $title = $params["title"];
        $text = $params["text"];
        $tags = htmlspecialchars($params["tags"]); echo $oid . " " . $title . " " . $text . " " . $tags;
        
        $advinfo = new Model_Ai();
        
        $advinfo->editAdvanced($oid, $title, $text);
        $advinfo->changeTags($oid, $tags);
    }
    
    public function findObj($params) {
        $tfind = $params["find"];
        
        $find = new Model_Find();
        $object = new Model_Object();
        
        $findSess = & $_SESSION["find"];
        
        if (isset($tfind)) {
            $findSess["string"] = $tfind;
        } else {
            if (!isset($findSess["string"])) {
                $findSess["string"] = "";
            }
        }
        
        if (isset($findSess["string"])) {
            
            $text = substr($findSess["string"], 0, 64);
			$text = explode(" ", $text);

            $tfind = $find->findObjects($text);
            
            if (count($tfind) == 0) {
                echo "<p style='color: #777; margin-left: 20px'>Ничего не найдено</p>";
            }
   
            foreach ($tfind as $part) {
                $obj = $object->getShortObject($part["id"]);
                foreach($obj as $val) {
                    $row[$val["field"]] = $val["val"];
                }
                
                $id = $obj[0]["id"];

                echo $this->view->render("tt_setObj", array("data" => $row, "id" => $id));
            }
        }
    }
    
    public function addComment($params) {
    	$this->registry["tt"]->uid = $this->registry["ui"]["id"];
    	
        $tid = $params["tid"];
        $text = $params["text"];
        $status = $params["status"];
        $post["attaches"] = json_decode($params["json"] , true);

        $this->registry["tt"]->addComment($tid, $text, $status, $post, false);
        
        $this->registry["tt"]->spamUsers("Новый комментарий", $tid);
    }
    
    public function closeTask($params) {
        $tid = $params["tid"];
        
        $this->registry["tt"]->closeTask($tid);
        
        $this->registry["tt"]->spamUsers("Задача закрыта", $tid);
    }

    public function delDraft($params) {
    	$did = $params["did"];
    	
    	$this->registry["tt"]->delDraft($did);
    }
    
    public function setSortMyTt($params) {
    	$sort = $params["sort"];
    	$id = $params["id"];
    	
    	$sortmytt = & $_SESSION["sortmytt"];
    	$sortmytt["sort"] = $sort;
    	$sortmytt["id"] = $id;
    }
    
	public function getListUsers() {
		$class_users = new Components_Users();
		$tree = $class_users->users_tree();
		echo $this->view->render("tt_utree", array("list" => $tree));
    }
    
    public function getDelegateUsers() {
    	$class_users = new Components_Users();
    	$tree = $class_users->onlyUsers_tree();
    	echo $this->view->render("tt_onlyUtree", array("list" => $tree));
    }
    
    public function getTask($params) {
    	$id = $params["id"];
    	
    	$author = array(); $ruser = array();
    	
    		if ($data = $this->registry["tt"]->getTask($id)) {
    	
    			if (count($data) > 0) {
    				$numComments = $this->registry["tt"]->getNumComments($id);
    				$newComments = $this->registry["tt"]->getNewCommentsFromTid($id);
    					
    				$lastCommentDate = $this->registry["tt"]->addTaskView($id);
    	
    				if ($data[0]["remote_id"] == 0) {
    					$author = $this->registry["user"]->getUserInfo($data[0]["who"]);
    				} else {
    					$author = $this->registry["tt_user"]->getRemoteUserInfo($data[0]["who"]);
    				}
    	
    				foreach($data as $part) {
    					if (isset($part["uid"])) {
    						if ($part["uid"] != 0) {
    							$user = $this->registry["user"]->getUserInfo($part["uid"]);
    	
    							$ruser[] = "<a style='cursor: pointer' onclick='getUserInfo(" . $part["uid"] . ")'>" . $user["name"] . " " . $user["soname"] . "</a>";
    						}
    					}
    	
    					if (isset($part["rgid"])) {
    						if ($part["rgid"] != 0) {
    							$ruser[] = "<span style='color: #5D7FA6'><b>" . $this->registry["user"]->getSubgroupName($part["rgid"]) . "</b></span>";
    						}
    					}
    	
    					if ($part["all"] == 1) {
    						$ruser[] = "<span style='color: #D9A444'><b>Все</b></span>";
    					}
    				}
    	
    				$group = null;
    				if ($data[0]["gid"] != "0") {
    					$group = $this->registry["tt"]->getGroupName($data[0]["gid"]);
    				}
    	
    				$object = new Model_Object();
    				$ai = new Model_Ai();
    				$forms = $ai->getForms();
    	
    				$cuser = $this->registry["user"]->getUserInfo($data[0]["cuid"]);
    					
    				$notObj = false;
    				if ($obj = $object->getShortObject($data[0]["oid"])) {
    					$notObj = true;
    	
    					$numTroubles = $object->getNumTroubles($data[0]["oid"]);
    					$advInfo = $ai->getAdvancedInfo($data[0]["oid"]);
    					$numAdvInfo = $ai->getNumAdvancedInfo($data[0]["oid"]);
    	
    					$rObject = $this->registry["module_objects"]->renderObject($this->registry["ui"], $obj, $advInfo, $numAdvInfo, $forms, $numTroubles, $group);
    					$this->view->setMainContent($rObject);
    				}
    	
    				echo $this->view->render("tt_task", array("data" => $data, "author" => $author, "ruser" => $ruser, "cuser" => $cuser, "numComments" => $numComments, "newComments" => $newComments, "notObj" => $notObj, "obj" => $obj));
    	
    				$comments = $this->registry["tt"]->getComments($id);
    				if (count($comments) > 0) {
    					echo "<div style='padding: 10px 0 20px 40px; font-size: 14px; color: #999'>Комментарии:</div>";
    				}
    				foreach ($comments as $part) {
    					if ($part["date"] >= $lastCommentDate) {
    						$new = true;
    					} else { $new = false;
    					}
    					echo $this->view->render("tt_comment", array("comment" => $part, "data" => $data, "new" => $new));
    				}
    					
    				if (!$data[0]["close"]) {
    					$status = $this->registry["tt"]->getCommentsStatus();
    					echo $this->view->render("tt_formcomment", array("tid" => $id, "status" => $status));
    				}
    			} else {
    				echo "<p>Задача не найдена</p>";
    			}
    		} else {
    			echo "<p>Задача не найдена</p>";
    		}
    }
}
?>