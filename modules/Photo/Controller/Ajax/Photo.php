<?php
class Controller_Ajax_Photo extends Modules_Ajax {
	private $_config;
    private $_file = array();
    private $_dir = array();
    private $_tree = null;
    private $_mphoto;
    private $_muser;
    private $_mfile;
    private $_desc = null;
    private $_curdir = 0;
    
    private $_access_fname = null;
    private $_access_md5 = null;
    
    function __construct($config) {
    	parent::__construct($config);

    	if (isset($this->registry["post"]["did"])) {
    		$this->_curdir = $this->registry["post"]["did"];
    	}

    	$this->_muser = new Model_User();
    	$this->_mfile = new Model_Photofile($config);
    	$this->_mphoto = new Model_Photo();
    }

	function getFileParamsFromMd5($md5) {
		$this->_file = $this->_mfile->getFileParamsFromMd5($md5);
	}
	
	function getFileParamsFromName($filename) {
		$this->_file = $this->_mfile->getFileParamsFromName($filename);
	}
	
	function getDirParams($did) {
		$this->_dir = $this->_mfile->getDirParams($did);
	}
	
	function getFileName($params) {
		$name = rawurldecode($params["name"]);
		$array = $this->getFileParamsFromName($name);
		
		echo $this->_file["md5"];
	}
	
	function admin() {
		$fm = & $_SESSION["fm"];
		if ( (isset($fm["admin"])) and ($fm["admin"]) ) {
			$fm["admin"] = false;
		} else {
			$fm["admin"] = true;
		}
		
		
		$this->files();
	}

    function files() {
        $fm = & $_SESSION["fm"];

		$curdir = $this->_curdir;
		
		if (!isset($fm["admin"])) {
			$fm["admin"] = false;
		}
		
		if ($curdir != 0) {
			$right = $this->_mfile->getRight($curdir);
		
			$flag = false;
			if ($this->registry["ui"]["id"] == $right[0]["uid"]) {
				$flag = true;
			}
			if (!$flag) {
				$right = json_decode($right[0]["right"], true);
				foreach($right as $key=>$val) {
					if ($key == "frall") {
						if ($val > 0) {
							$flag = true;
						}
					}
					
					if ($key == "fg" . $this->registry["ui"]["gid"]) {
						if ($val > 0) {
							$flag = true;
						}
					}
					
					if ($key == "user" . $this->registry["ui"]["id"]) {
						if ($val > 0) {
							$flag = true;
						}
					}
				}
			}
			
			if (!$flag) {
				$curdir = 0;
				$fm["dir"] = 0;
			}
		}
        
        $dirs = array(); $files = array();
        $total = 0;
        
        $sess = 0; $fav = 0;
        $sphoto = & $_SESSION["photo"];
        if (isset($sphoto["tag"])) {
        	if (count($sphoto["tag"]) > 0) {
        		$sess = 1;
        	}
        }
        if (isset($sphoto["sel"])) {
        	if (count($sphoto["sel"]) > 0) {
        		$sess = 1;
        	}
        }
        if (isset($sphoto["fav"])) {
        	if ($sphoto["fav"] == 1) {
        		$sess = 1;
        		$fav = 1;
        	}
        }
		
        if ( (isset($fm["admin"])) and ($fm["admin"]) ) {
        	if (!$sess) {
    			$dirs = $this->_mfile->getAdminDirs($curdir);
        	}
        } else {
        	if (!$sess) {
    			$dirs = $this->_mfile->getDirs($curdir);
        	}
        }
		
		$k = 0; $res_dirs = array();
		for($i=0; $i<count($dirs); $i++) {
			$flag = false;
			if ($this->registry["ui"]["id"] == $dirs[$i]["uid"]) {
				$flag = true;
			}
			if (!$flag) {
				$right = json_decode($dirs[$i]["right"], true);
				foreach($right as $key=>$val) {
					if ($key == "frall") {
						if ($val > 0) {
							$flag = true;
						}
					}
					
					if ($key == "fg" . $this->registry["ui"]["gid"]) {
						if ($val > 0) {
							$flag = true;
						}
					}
					
					if ($key == "user" . $this->registry["ui"]["id"]) {
						if ($val > 0) {
							$flag = true;
						}
					}
				}
			}
			
			if ($flag) {
				$res_dirs[$k] = $dirs[$i];
			}
			
			$k++;
		}

		if ( (isset($fm["admin"])) and ($fm["admin"]) ) {
			if ( ($sess) and (!$fav) ) {
				$files = $this->_mfile->getAdminFilesSort($curdir);
			} else if ( ($sess) and ($fav) ) {
				$files = $this->_mfile->getAdminFilesFavorite($curdir);
			} else {
				$files = $this->_mfile->getAdminFiles($curdir);
			}
		} else {
			if ( ($sess) and (!$fav) ) {
				$files = $this->_mfile->getFilesSort($curdir);
			} else if ( ($sess) and ($fav) ) {
				$files = $this->_mfile->getFilesFavorite($curdir);
			} else {
				$files = $this->_mfile->getFiles($curdir);
			}
		}

        if ( ($curdir == 0) and (!$sess) ) {
        	$shPath = "/";
        } else if ( ($curdir != 0) and (!$sess) ) {
        	$shPath = $fm["dirname"];
        	$pid = $this->_mfile->getPidFromDir($curdir);
        	$up[0] = array("name" => "..", "pid" => $pid[0]["pid"]);
        	$res_dirs = array_merge($up, $res_dirs);
        } else if ($fav) {
        	$shPath = "<span class='label label-warning' style='margin-right: 5px;'>Favorite<span> <a style='cursor: pointer; color: #FC7;' onclick='delFavorite()'>x</a></span></span>";
        } else {
        	$shPath = "Sort: ";
        	
        	if ( (isset($sphoto["tag"])) and (count($sphoto["tag"]) > 0) ) {
        		foreach($sphoto["tag"] as $part) {
        			$shPath .= "<span class='label label-success' style='margin-right: 5px;'>" . $part . "<span> <a style='cursor: pointer; color: #FC7;' onclick='delSortTag(\"" . $part . "\")'>x</a></span></span>";
        		}
        	}
        	
        	if ( (isset($sphoto["sel"])) and (count($sphoto["sel"]) > 0) ) {
        		foreach($sphoto["sel"] as $part) {
        			$shPath .= "<span class='label label-success' style='margin-right: 5px;'>" . $part . "<span> <a style='cursor: pointer; color: #FC7;' onclick='delSortSel(\"" . $part . "\")'>x</a></span></span>";
        		}
        	}
        }

        $k = 0; $res_files = array();
		for($i=0; $i<count($files); $i++) {
			$flag = false;
			if ($this->registry["ui"]["id"] == $files[$i]["uid"]) {
				$flag = true;
			}
			if (!$flag) {
				$right = json_decode($files[$i]["right"], true);
				foreach($right as $key=>$val) {
					if ($key == "frall") {
						if ($val > 0) {
							$flag = true;
						}
					}
					
					if ($key == "fg" . $this->registry["ui"]["gid"]) {
						if ($val > 0) {
							$flag = true;
						}
					}
					
					if ($key == "user" . $this->registry["ui"]["id"]) {
						if ($val > 0) {
							$flag = true;
						}
					}
				}
			}
			
			if ($flag) {
				$res_files[$k]["id"] = $files[$i]["id"];
				$res_files[$k]["close"] = $files[$i]["close"];
				$res_files[$k]["name"] = $files[$i]["name"];
				if (mb_strlen($res_files[$k]["name"]) > 20) {
					$res_files[$k]["shortname"] = mb_substr($res_files[$k]["name"], 0, 10) . ".." . mb_substr($res_files[$k]["name"], mb_strrpos($res_files[$k]["name"], ".")-1, mb_strlen($res_files[$k]["name"])-mb_strrpos($res_files[$k]["name"], ".")+1);
				} else {
					$res_files[$k]["shortname"] = $res_files[$k]["name"];
				}
				
				$ext = mb_substr($res_files[$k]["name"], mb_strrpos($res_files[$k]["name"], ".") + 1);
				$res_files[$k]["ico"] = $this->registry["path"]["photo"] . "_thumb/" . $files[$i]["md5"];
				$res_files[$k]["md5"] = $files[$i]["md5"];
				
				$size = $files[$i]["size"];
				$total += $size;
				if (($size / 1024) > 1) { $size = round($size / 1024, 2) . "&nbsp;Kb"; } else { $size = round($size, 2) . "&nbsp;Б"; };
	            if (($size / 1024) > 1) { $size = round($size / 1024, 2) . "&nbsp;Mb"; };
	            $res_files[$k]["size"] = $size;
	            
				$res_files[$k]["date"] = date("H:i d-m-Y",  strtotime($files[$i]["timestamp"]));
				
				$k++;
			}
		}
		
		$buffer = & $_SESSION["photo"];
		$clip = "";
    	if(isset($buffer["files"])) {
    		if ($buffer["files"] > 0) {
    			foreach($buffer["files"] as $part) {
    				$part = htmlspecialchars($part);
    				if (mb_strlen($part) > 20) {
    					$shortname = mb_substr($part, 0, 10) . ".." . mb_substr($part, mb_strrpos($part, ".")-1, mb_strlen($part)-mb_strrpos($part, ".")+1);
    				} else {
    					$shortname = $part;
    				}
    				$clip .= "<li><span class='buf_file' title='" . $part . "'>" . $shortname . "</span></li>";
    			}
    		}
    	}
    	if(isset($buffer["dirs"])) {
    		if ($buffer["dirs"] > 0) {
    			foreach($buffer["dirs"] as $part) {
    				$part = htmlspecialchars($part);
    				$this->getDirParams($part);
    				$shortname = mb_substr($this->_dir["name"], 0, 12);
    				$part = $this->_dir["name"];
    				$clip .= "<li><span class='buf_folder' title='" . $part . "'>" .  $shortname . "</span></li>";
    			}
    		}
    	}
        
        if (($total / 1024) > 1) { $total = round($total / 1024, 2) . "&nbsp;Kb"; } else { $total = round($total, 2) . "&nbsp;Б"; };
    	if (($total / 1024) > 1) { $total = round($total / 1024, 2) . "&nbsp;Mb"; };

        echo $this->view->render("fm_content", array("admin" => $fm["admin"], "clip" => $clip, "shPath" => $shPath, "dirs" => $res_dirs, "_thumb" => $this->registry['path']['photo'] . "_thumb/", "files" => $res_files, "totalsize" => $total, "curdir" => $this->_curdir, "fsRes" => $this->view->render("fm_view", array())));
    }
    
    function getfinfo($params) {
    	$md5 = $params["md5"];
    	
    	$curdir = $this->_curdir;
	
    	// get md5
    	$this->getFileParamsFromMd5($md5);
     	
    	// access
    	$this->_access_md5 = $md5;
    	$mode = $this->_getUserFileChmod();
    	
    	//owner
    	$uid = $this->_muser->getUserInfo($this->_file["uid"]);
    	$row["owner"] = $uid["login"];
    	
    	//size
    	$size = $this->_file["size"];
    	if (($size / 1024) > 1) {
    		$size = round($size / 1024, 2) . "&nbsp;Kb";
    	} else { $size = round($size, 2) . "&nbsp;B";
    	};
    	if (($size / 1024) > 1) {
    		$size = round($size / 1024, 2) . "&nbsp;Mb";
    	};
    	$row["size"] = $size;
    	
    	//history
    	$data = $this->_mfile->getFileHistory($md5, $curdir);
    	$row["history"] = null;
    	foreach($data as $part) {
    		if ($part["uid"] != null) {
    			$uid = $this->_muser->getUserInfo($part["uid"]);
    			$row["history"] .= $this->view->render("fm_fhistory", array("md5" => $part["md5"], "date" => $part["timestamp"], "uid" => $uid));
    		}
    	}
    	
    	//chmod
    	$this->_mfile->getFileChmod();
    	 
    	$groups = $this->_mfile->getGroups();
    	$users = $this->_mfile->getUsers();
    	
    	$sortlist = array();
    	foreach($groups as $group) {
    		foreach($users as $user) {
    			if ( ($user["gname"] == $group["sname"]) and ($group["sname"] != null) ) {
    				$udata = $this->view->render("fm_data", array("data" => $this->_muser->getUserInfo($user["id"])));
    				$sortlist[$group["pname"]][$group["sid"]][] = $udata;
    			}
    		}
    	}
    	
    	$this->print_array($sortlist);
    	
    	$row["chmod"] = null;
    	$row["chmod"] .= $this->view->render("fm_tree", array("group" => $groups, "list" => $this->_tree, "md5" => $md5, "mode" => $mode));
    	
    	echo json_encode($row);
    }
    
    // FILE DELETE
	function delfile($params) {
		$this->_access_md5 = $params["fname"];
		
		$mode = $this->_getUserFileChmod();
		
		if ($mode == 2) {
			$this->_mfile->delfile($this->_access_md5);
		}
	}
	
	function delfilereal($params) {
		$fname = $params["fname"];
		$this->_mfile->delfilereal($fname);
	}
	
	// DIR DELETE
	function deldir($params) {
		$did = mb_substr($params["did"], 2);

		$mode = $this->_getUserDirChmod($did);

		if ($mode == 2) {
			$this->_mfile->rmDir($did);
		}
	}
	
	function deldirreal($params) {
		$did = mb_substr($params["did"], 2);
		$this->_mfile->rmDirReal($did);
	}
	
	function getDirName($params) {
		$did = mb_substr($params["did"], 2);
		$mode = $this->getDirParams($did);
		echo $this->_dir["name"];
	}
	
	function dirRename($params) {
		$did = mb_substr($params["did"], 2);
		$name = $params["name"];

		$mode = $this->_getUserDirChmod($did);
	
		if ($mode == 2) {
			$this->_mfile->dirRename($did, $name);
		}
	}
	
	function fileRename($params) {
		$md5 = urldecode($params["md5"]);
		$newname = urldecode($params["newname"]);

		$this->_access_md5 = $md5;
		$mode = $this->_getUserFileChmod();
		
		if ($mode == 2) {
			$this->_mfile->fileRename($md5, $newname);
		}
	}
	
	function getTotalSize() {
		echo $this->_mfile->getTotalSize();
	}
    
    function createDir($params) {
        $dirName = $params["dirName"];
        
        $curdir = $this->_curdir;
        
        $flag = $this->_mfile->createDir($curdir, $dirName);
        
        if ($flag) {
        	$this->files();
        } else {
        	echo "error";
        }
    }
    
    function rmDir($params) {
        $dirName = $params["dirName"];
        
        $curdir = $this->_curdir;
        
        $did = $this->_mfile->getDirIdFromNameAndPid($dirName, $curdir);
        $dmode = $this->_getUserDirChmod($did[0]["id"]);
        
        if ($dmode == "2") {
        	$this->_mfile->rmDir($curdir, $dirName);
        }

        $this->files();
    }
    
    function restore($params) {
    	if (isset($params["file"])) {
    		foreach($params["file"] as $part) {
    			$this->_mfile->restoreFile($part);
    		}
    	}
    	if (isset($params["dir"])) {
    		foreach($params["dir"] as $part) {
    			$this->_mfile->restoreDir($part);
    		}
    	}
    	
    	$this->files();
    }
    
    function copyFiles($params) {
    	$curdir = $this->_curdir;
        
        $buffer = & $_SESSION["photo"];
        $buffer["dir"] = $curdir;
        
        $res = false;
        
        if (isset($params["file"])) {
	    	$buffer["files"] = $params["file"];
	    	foreach($buffer["files"] as $part) {
	    		$this->getFileParamsFromMd5($part);
	    		if (mb_strlen($part) > 20) {	    			
	    			$shortname = mb_substr($this->_file["filename"], 0, 10) . ".." . mb_substr($this->_file["filename"], mb_strrpos($this->_file["filename"], ".")-1, mb_strlen($this->_file["filename"])-mb_strrpos($this->_file["filename"], ".")+1);
	    		} else {
	    			$shortname = $this->_file["filename"];
	    		}
	    		$res = true;
	    		echo "<li><span class='buf_file' title='" . $part . "'>" . $shortname . "</span></li>";
	    	}
        } else {
        	$buffer["files"] = "0";
        }
        if (isset($params["dir"])) {
        	$buffer["dirs"] = $params["dir"];
        	foreach($buffer["dirs"] as $part) {
        		$this->getDirParams($part);
    			$shortname = mb_substr($this->_dir["name"], 0, 12);
    			$res = true;
    			$part = $this->_dir["name"];
    			echo "<li><span class='buf_folder' title='" . $part . "'>" .  $shortname . "</span></li>";
        	}
        } else {
        	$buffer["dirs"] = "0";
        }
        
        if (!$res) {
        	echo "<li style='text-align: center'>empty</li>";
        }
    }
    
    function moveFiles() {
    	$curdir = $this->_curdir;
    	
    	$buffer = & $_SESSION["photo"];

    	if ( (isset($buffer["dir"])) and ($buffer["files"]) ) {
	        foreach($buffer["files"] as $part) {
	        	$this->_access_md5 = $part;
	        	$mode = $this->_getUserFileChmod();

	        	if ($mode == 2) {
	        		$this->_mfile->moveFiles($curdir, $part, $buffer["dir"]);
	        	}
	        }
    	}
    	if ( (isset($buffer["dir"])) and ($buffer["dirs"]) ) {
    		foreach($buffer["dirs"] as $part) {
    			$dmode = $this->_getUserDirChmod($part);
    			
    			if ($dmode == "2") {
    				$this->_mfile->moveDir($curdir, $part);
    			}
    		}
    	}
        
    	unset($_SESSION["photo"]);
    	
        $this->files();
    }
    
    function issetFile($params) {
    	$file = urldecode($params["file"]);

    	$fm = & $_SESSION["fm"];
    	if (!isset($fm["dir"])) {
    		$curdir = 0;
    	} else {
    		$curdir = $fm["dir"];
    	}
        
        $dmode = $this->_getUserDirChmod($curdir);
        
        $this->_access_fname = $file;
        
        $now = $this->_muser->getNowSize();
        $quota = $this->_muser->getUserQuota();
        if ($now >= $quota) {
        	$limit = 1;
        } else {
        	$limit = 0;
        }

        if ($dmode == 2) {
        	if (!$limit) {
        		echo "1";
        	} else {
        		echo "0";
        	}
        } else {
        	echo "0";
        }
    }

    function addFileText($params) {
    	$text = $params["text"];
    	$md5 = $params["md5"];
    	
    	$this->getFileParamsFromMd5($md5);
    	$fid = $this->_file["id"]; 
    	
    	$this->_mfile->addFileText($fid, $text);
    	
    	echo $this->view->render("fm_ftext", array("text" => nl2br($text), "date" => date("Y-m-d H:i:s")));
    }

	private function print_array($arr) {
		if (!is_array($arr)) {
			return;
		}

		while(list($key, $val) = each($arr)) {
			if (!is_array($val)) {
				if ($val == null) {
					$val = "empty";
				}

				$this->_tree .= "<ul><li><div style='margin: 0 0 0 10px'>" . $val . "</div></li></ul>";
			}
			if (is_array($val)) {
				if ($key != "0") {
					if(is_numeric($key)) {
						$gid = $this->_muser->getSubgroupName($key);
						$this->_tree .= "<ul><li><span class='folder'><label class='checkbox'><input type='checkbox' id='fg" . $key . "' name='fgruser[]' class='fgruser' value='" . $key . "' />&nbsp;" . $gid . "</label><span class='mode gmode' id='gmode_" . $key . "'><label style='margin-right: 5px' class='radio'><input type='radio' name='mode_g_" . $key . "' value='1' /> Read</label><label class='radio'><input type='radio' name='mode_g_" . $key . "' value='2' /> Write</label></span></span>";
					} else {
						$this->_tree .= "<ul><li><span class='folder'>&nbsp;" . $key . "</span>";
					}
				}

				$this->print_array($val);

				if ($key != "0") {
					$this->_tree .= "</li></ul>";
				}
			}
		}
	}
    
    function getUsersChmod($params) {
    	$md5 = $params["md5"];
    	
    	$data = $this->_mfile->getUsersChmod($md5);

        echo $data[0]["right"];
    }
    
    function getUsersDirChmod($params) {
    	$did = $params["did"];
    	
		$data = $this->_mfile->getUsersDirChmod($did);

        echo $data[0]["right"];
    }
    
    function addFileRight($params) {
    	$json = $params["json"];
    	$md5 = $params["md5"];
    	
    	$this->getFileParamsFromMd5($md5);
    	
    	$this->_mfile->addFileRight($this->_file["id"], $json);
    }
    
    function addDirRight($params) {
    	$json = $params["json"];
    	$did = $params["did"];
    	
    	$this->_mfile->addDirRight($did, $json);
    }
    
    function shDirRight($params) {
    	$did = mb_substr($params["did"], 2);
    	
    	$mode = $this->_getUserDirChmod($did);
    	
    	$this->_mfile->getFileChmod();
    	
    	$groups = $this->_mfile->getGroups();
    	$users = $this->_mfile->getUsers();

		$sortlist = array();
		foreach($groups as $group) {
			foreach($users as $user) {
				if ( ($user["gname"] == $group["sname"]) and ($group["sname"] != null) ) {
					$udata = $this->view->render("fm_data", array("data" => $this->_muser->getUserInfo($user["id"])));
					$sortlist[$group["pname"]][$group["sid"]][] = $udata;
				}
			}
		}

		$this->print_array($sortlist);

		echo $this->view->render("fm_dtree", array("group" => $groups, "list" => $this->_tree, "did" => $did, "owner" => $this->_dir["owner"], "mode" => $mode));
    }
    
    function getCurDirName() {
    	$curdir = $this->_curdir;

    	$data = $this->_mfile->getCurDirName($curdir);
    	
    	if (isset($data[0]["name"])) {
    		echo $data[0]["name"] . "/";
    	} else {
    		echo null;
    	}
    }
    
    function getUserFileChmod() {
    	echo $this->_getUserFileChmod();
    }
    
    private function _getUserFileChmod() {
    	$uid = $this->registry["ui"]["id"];
    	
    	if ($this->registry["ui"]["admin"]) {
    		return 2;
    	}
    	
    	$curdir = $this->_curdir;

    	if ($this->_access_md5 != null) {
    		$md5 = $this->_access_md5;
    	} else if ($this->_access_fname != null) {
    		$this->getFileParamsFromName($this->_access_fname);
    		if (isset($this->_file["md5"])) {
    			$md5 = $this->_file["md5"];
    		}
    	}
    	
    	// file not exist
    	if (!isset($md5)) { return "-1"; }
    	if ($md5 == null) { return "-1"; }
    	
    	$this->getFileParamsFromMd5($md5);
    	$owner_id = $this->_file["uid"];
    	if ($owner_id == $uid) {
    		return 2;
    	}
    	
    	$data = $this->_mfile->getUsersChmod($md5);
    	$data = json_decode($data[0]["right"], 1);

    	if (isset($data["frall"])) {
    		return $data["frall"];
    	}
    	
    	$uinfo = $this->_muser->getUserInfo($uid);
    	$gid = $uinfo["gid"];

    	foreach($data as $key=>$val) {
    		if (substr($key, 0, 2) == "fg") {
    			if (isset($data[$key])) {
    				if ($gid == substr($key, 2)) {
    					return $val;
    				}
    			}
    		}
    		
    		if (substr($key, 0, 4) == "user") {
    			if (isset($data[$key])) {
    				if ($uid == substr($key, 4)) {
    					return $val;
    				}
    			}
    		}
    	}
    }
    
    private function _getUserDirChmod($curdir) {
    	$this->getDirParams($curdir);
    	
    	$uid = $this->registry["ui"]["id"];

    	if ($this->registry["ui"]["admin"]) {
    		return 2;
    	}

    	// dir not exist
    	if (!isset($curdir)) {
    		return "-1";
    	} else if($curdir == 0) {
    		return 2;
    	}
    	
    	$owner_id = $this->_dir["uid"];
    	if ($owner_id == $uid) {
    		return 2;
    	}
    	 
    	$data = json_decode($this->_dir["right"], 1);
    
    	if (isset($data["frall"])) {
    		return $data["frall"];
    	}
    	 
    	$uinfo = $this->_muser->getUserInfo($uid);
    	$gid = $uinfo["gid"];
    
    	foreach($data as $key=>$val) {
    		if (substr($key, 0, 2) == "fg") {
    			if (isset($data[$key])) {
    				if ($gid == substr($key, 2)) {
    					return $val;
    				}
    			}
    		}
    
    		if (substr($key, 0, 4) == "user") {
    			if (isset($data[$key])) {
    				if ($uid == substr($key, 4)) {
    					return $val;
    				}
    			}
    		}
    	}
    }
    
    function getTree() {
    	$fm = & $_SESSION["fm"];
    	if ($fm["admin"]) {
    		$this->_mfile->showTree(0, true);
    	} else {
    		$this->_mfile->showTree(0, false);
    	}
    	echo $this->view->render("block_left", array("tree" => $this->_mfile->getTree()));
    }
    
    public function setDesc($params) {
    	$x1 = $params["x1"];
    	$x2 = $params["x2"];
    	$y1 = $params["y1"];
    	$y2 = $params["y2"];
    	$ws = $params["ws"];
    	$desc = $params["desc"];
    	$md5 = $params["md5"];
    
    	if ($x1 != "") {
    		$this->_mphoto->setDesc($md5, $desc, $x1, $x2, $y1, $y2, $ws);
    	} else {
    		$this->_mphoto->setTag($md5, $desc);
    	}
    
    	$desc = $this->_mphoto->getDesc($md5, $ws);
    	$tags = $this->_mphoto->getTags($md5);
    
    	echo $this->view->render("fm_desc", array("desc" => $desc, "tags" => $tags));
    }
    
    public function getDesc($params) {
    	$md5 = $params["md5"];
    
    	$desc = $this->_mphoto->getDesc($md5);
    	$tags = $this->_mphoto->getTags($md5);
    
    	echo $this->view->render("fm_desc", array("desc" => $desc, "tags" => $tags));
    }
    
    public function delTag($params) {
    	$id = $params["id"];
    
    	$this->_mphoto->delTag($id);
    }
    
    public function delDesc($params) {
    	$id = $params["id"];
    
    	$this->_mphoto->delDesc($id);
    }
    
    public function getNumNotes($params) {
    	$md5 = $params["id"];
    
    	echo $this->_mphoto->getNumNotes($md5);
    }
    
    public function getNotes($params) {
    	$md5 = $params["id"];
    
    	$data = $this->_mphoto->getFileText($md5);
    	$text = "";
    	foreach($data as $part) {
    		if ($part["uid"] != null) {
    			$uid = $this->_muser->getUserInfo($part["uid"]);
    			$text .= $this->view->render("fm_ftext", array("text" => $part["text"], "date" => $part["timestamp"], "uid" => $uid));
    		}
    	}
    	 
    	echo $text;
    }
    
    public function setSel($params) {
    	$word = $params["word"];
    
    	$sphoto = & $_SESSION["photo"];
    	$sphoto["sel"][] = $word;
    
    	unset($sphoto["fav"]);
    }
    
    public function setTag($params) {
    	$word = $params["word"];
    
    	$sphoto = & $_SESSION["photo"];
    	$sphoto["tag"][] = $word;
    
    	unset($sphoto["fav"]);
    }
    
    public function delSortTag($params) {
    	$word = $params["word"];
    
    	$sphoto = & $_SESSION["photo"];
    	for($i=0; $i<count($sphoto["tag"]); $i++) {
    		if ($sphoto["tag"][$i] == $word) {
    			unset($sphoto["tag"][$i]);
    		}
    	}
    }
    
    public function delSortSel($params) {
    	$word = $params["word"];
    
    	$sphoto = & $_SESSION["photo"];
    	for($i=0; $i<count($sphoto["sel"]); $i++) {
    		if ($sphoto["sel"][$i] == $word) {
    			unset($sphoto["sel"][$i]);
    		}
    	}
    }
    
    public function favorite($params) {
    	$md5 = $params["md5"];
    
    	if ($this->_mphoto->favorite($md5)) {
    		echo "<img src='" . $this->registry["uri"] . "img/add.png' alt='' /> Image add in favorite list";
    	} else {
    		echo "<img src='" . $this->registry["uri"] . "img/remove.png' alt='' /> Image delete from favorite list";
    	}
    }
    
    public function getFavorite() {
    	$sphoto = & $_SESSION["photo"];
    	$sphoto["fav"] = 1;
    
    	unset($sphoto["sel"]);
    	unset($sphoto["tag"]);
    }
    
    public function delFavorite() {
    	$sphoto = & $_SESSION["photo"];
    	unset($sphoto["fav"]);
    }
    
    public function delSort() {
    	$sphoto = & $_SESSION["photo"];
    	unset($sphoto["fav"]);
    	unset($sphoto["sel"]);
    	unset($sphoto["tag"]);
    }
}
?>
