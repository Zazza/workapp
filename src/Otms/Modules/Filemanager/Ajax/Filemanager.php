<?php

/**
 * This file is part of the Workapp project.
 *
 * Filemanager Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Filemanager\Ajax;

use Otms\Modules\Objects\Model\Object;

use Engine\Modules\Ajax;
use Otms\System\Model\User;
use Otms\Modules\Filemanager\Model;

/**
 * Ajax\Filemanager class
 * 
 * Основной для Filemanager класс, т.к. все действия filemanager вызываются AJAX
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Filemanager extends Ajax {
	/**
	 * Настройки модуля
	 *
	 * @var array
	 */
	private $_config;
	
	/**
	 * Содержит параметры файла
	 * 
	 * @var array
	 */
    private $_file = array();
    
    /**
     * Содержит параметры директории
     *
     * @var array
     */
    private $_dir = array();
    
    /**
     * Дерево диреткорий
     *
     * @var string
     */
    private $_tree = null;
    
    /**
     * new User()
     *
     * @var object
     */
    private $_muser;
    
    /**
     * new Model\File($config)
     *
     * @var object
     */
    private $_mfile;

     /**
     * Текущая директория
     *
     * @var int
     */
    private $_curdir = 0;
    
    /**
     * Файл для действия над ним
     *
     * @var string
     */
    private $_access_fname = null;
    
    /**
     * MD5 файла для действия над ним
     *
     * @var string
     */
    private $_access_md5 = null;
    
    function __construct($config) {
    	parent::__construct($config);
    	$this->config = $config;

    	$fm = & $_SESSION["fm"];
    	
    	if (isset($this->registry["post"]["did"])) {
    		$this->_curdir = $this->registry["post"]["did"];
    		$fm["dir"] = $this->_curdir;
    	} else {
    		if (isset($fm["dir"])) {
    			$this->_curdir = $fm["dir"];
    		}
    	}

    	$this->_muser = new User();
    	$this->_mfile = new Model\File($config);
    }

    /**
     * Получить параметры файла по MD5
     * 
     * @param string $md5
     * Результат array $this->_file
     */
	function getFileParamsFromMd5($md5) {
		$this->_file = $this->_mfile->getFileParamsFromMd5($md5);
	}
	
	/**
	 * Получить параметры файла по его имени
	 *
	 * @param string $filename
	 * Результат array $this->_file
	 */
	function getFileParamsFromName($filename) {
		$this->_file = $this->_mfile->getFileParamsFromName($filename);
	}
	
	/**
	 * Получить параметры директории по ID
	 *
	 * @param int $did
	 * Результат array $this->_dir
	 */
	function getDirParams($did) {
		$this->_dir = $this->_mfile->getDirParams($did);
	}
	
	/**
	 * Вернуть MD5 файла по его имени
	 * 
	 * @param array
	 *    string $params["name"]
	 * @return string
	 */
	function getFileName($params) {
		$name = rawurldecode($params["name"]);
		$array = $this->getFileParamsFromName($name);
		
		return $this->_file["md5"];
	}
	
	/**
	 * Перейти в просмотр ФС в режиме "администратор"
	 * 
	 * @return string $this->files()
	 */
	function admin() {
		$fm = & $_SESSION["fm"];
		if ( (isset($fm["admin"])) and ($fm["admin"]) ) {
			$fm["admin"] = false;
		} else {
			$fm["admin"] = true;
		}
		
		
		return $this->files();
	}

	/**
	 * Просмотр файлов в текущей директории
	 * 
	 * @return string (html twig render) .../fm/content.tpl
	 */
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
		
        if ( (isset($fm["admin"])) and ($fm["admin"]) ) {
    		$dirs = $this->_mfile->getAdminDirs($curdir);
        } else {
    		$dirs = $this->_mfile->getDirs($curdir);
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
			$files = $this->_mfile->getAdminFiles($curdir);
		} else {
			$files = $this->_mfile->getFiles($curdir);
		}
        
        if ($curdir == 0) {
        	$shPath = "/";
        } else {
        	$shPath = $fm["dirname"];
        	$pid = $this->_mfile->getPidFromDir($curdir);
        	$up[0] = array("name" => "..", "pid" => $pid[0]["pid"]);
        	$res_dirs = array_merge($up, $res_dirs);
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
				$res_files[$k]["ico"] = $this->_mfile->setIcon($ext, $files[$i]["md5"]);
				
				$res_files[$k]["share"] = $files[$i]["share"];
				
				$size = $files[$i]["size"];
				$total += $size;
				if (($size / 1024) > 1) { $size = round($size / 1024, 2) . "&nbsp;Kb"; } else { $size = round($size, 2) . "&nbsp;Б"; };
	            if (($size / 1024) > 1) { $size = round($size / 1024, 2) . "&nbsp;Mb"; };
	            $res_files[$k]["size"] = $size;
	            
				$res_files[$k]["date"] = date("H:i d-m-Y",  strtotime($files[$i]["timestamp"]));
				
				$k++;
			}
		}

        if (($total / 1024) > 1) { $total = round($total / 1024, 2) . "&nbsp;Kb"; } else { $total = round($total, 2) . "&nbsp;Б"; };
    	if (($total / 1024) > 1) { $total = round($total / 1024, 2) . "&nbsp;Mb"; };

        return $this->view->render("fm_content", array("admin" => $fm["admin"], "shPath" => $shPath, "dirs" => $res_dirs, "_thumb" => $this->registry['path']['upload'] . "_thumb/", "files" => $res_files, "totalsize" => $total, "curdir" => $this->_curdir));
    }
    
    /**
     * Получить полную информацию о файле для контекстного меню файла
     * 
     * @param array
     *    string $params["fname"]
     * @return string (html twig render) .../fm/content.tpl
     */
    function getfinfo($params) {
    	$fname = $params["fname"];
    	
    	$curdir = $this->_curdir;
	
    	// get md5
    	$this->getFileParamsFromName($fname);
    	$row["md5"] = $this->_file["md5"];
    	
    	// access
    	$this->_access_md5 = $row["md5"];
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
    	
    	//share
    	if ($this->_mfile->share($row["md5"])) {
    		$row["share"] = $this->_mfile->getDesc();
    	}
    	
    	//text
    	$data = $this->_mfile->getFileText($row["md5"], $curdir);    	
    	$row["text"] = "";
    	foreach($data as $part) {
    		if ($part["uid"] != null) {
    			$uid = $this->_muser->getUserInfo($part["uid"]);
    			$row["text"] .= $this->view->render("fm_ftext", array("text" => $part["text"], "date" => $part["timestamp"], "uid" => $uid));
    		}
    	}
    	
    	//history
    	$data = $this->_mfile->getFileHistory($row["md5"], $curdir);
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
    	$row["chmod"] .= $this->view->render("fm_tree", array("group" => $groups, "list" => $this->_tree, "md5" => $row["md5"], "mode" => $mode));
    	
    	return json_encode($row);
    }
    
    /**
     * Пометить файл как удалённый
     * 
     * @param array
     *    string $params["fname"]
     */
	function delfile($params) {
		$this->_access_fname = $params["fname"];
		
		$mode = $this->_getUserFileChmod();
		
		if ($mode == 2) {
			$this->_mfile->delfile($this->_access_fname);
		}
	}
	
	/**
	 * Реальное удаление файла
	 *
	 * @param array
	 *    string $params["fname"]
	 */
	function delfilereal($params) {
		$fname = $params["fname"];
		$this->_mfile->delfilereal($fname);
	}
	
	/**
     * Пометить директорию как удалённая
     * 
     * @param array
     *    int $params["did"]
     */
	function deldir($params) {
		$did = mb_substr($params["did"], 2);

		$mode = $this->_getUserDirChmod($did);

		if ($mode == 2) {
			$this->_mfile->rmDir($did);
		}
	}
	
	/**
	 * Реальное удаление директории
	 *
	 * @param array
	 *    int $params["did"]
	 */
	function deldirreal($params) {
		$did = mb_substr($params["did"], 2);
		$this->_mfile->rmDirReal($did);
	}
	
	/**
	 * Вернуть имя директории
	 * 
	 * @param array
	 *    int $params["did"]
	 * @return string
	 */
	function getDirName($params) {
		$did = mb_substr($params["did"], 2);
		$mode = $this->getDirParams($did);
		return $this->_dir["name"];
	}
	
	/**
	 * Переименовать директорию
	 * @param array
	 *    int $params["did"]
	 *    string $params["name"]
	 */
	function dirRename($params) {
		$did = mb_substr($params["did"], 2);
		$name = $params["name"];

		$mode = $this->_getUserDirChmod($did);
	
		if ($mode == 2) {
			$this->_mfile->dirRename($did, $name);
		}
	}
	
	/**
	 * Переименовать файл
	 * 
	 * @param array
	 *    $params["oldname"]
	 *    $params["newname"]
	 */
	function fileRename($params) {
		$oldname = urldecode($params["oldname"]);
		$newname = urldecode($params["newname"]);
		 
		$curdir = $this->_curdir;
		
		$this->_access_fname = $oldname;
		$mode = $this->_getUserFileChmod();
		
		if ($mode == 2) {
			$this->_mfile->fileRename($curdir, $oldname, $newname);
		}
	}
	
	/**
	 * Возвращает суммарный размер файлов в текущей директории
	 * 
	 * @return string
	 */
	function getTotalSize() {
		return $this->_mfile->getTotalSize();
	}
    
	/**
	 * Создать директорию
	 * 
	 * @param array $params
	 *    $params["dirName"]
	 * @return string $this->files()
	 * @return string "error"
	 */
    function createDir($params) {
        $dirName = $params["dirName"];
        
        $curdir = $this->_curdir;
        
        $flag = $this->_mfile->createDir($curdir, $dirName);
        
        if ($flag) {
        	return $this->files();
        } else {
        	return "error";
        }
    }

    /**
     * Восстановить помеченные как удалённые файлы и директории
     * 
     * @param array
     *    array $params["file"]
     *    array $params["dir"]
     * @return string $this->files()
     */
    function restore($params) {
    	$curdir = $this->_curdir;
    	
    	if (isset($params["file"])) {
    		foreach($params["file"] as $part) {
    			$this->_mfile->restoreFile($part, $curdir);
    		}
    	}
    	if (isset($params["dir"])) {
    		foreach($params["dir"] as $part) {
    			$this->_mfile->restoreDir($part);
    		}
    	}
    	
    	return $this->files();
    }
    
    /**
     * Скопировать файлы и директории
     * 
     * @param array
     *    array $params["file"]
     *    array $params["dir"]
     * @return string json $row["count"] = $this->_mfile->countBuffer()
     */
    function copyFiles($params) {
    	$curdir = $this->_curdir;
        
        $buffer = & $_SESSION["clip"];
        
        $res = false;
        
        if (isset($params["file"])) {
        	if (isset($buffer["files"])) {
        		foreach($params["file"] as $part) {
        			if (!in_array($part, $buffer["files"])) {
        				$buffer["files"][] = $part;
        			}
        		}
        	} else {
        		$buffer["files"] = $params["file"];
        	}
        } else {
        	if (!isset($buffer["files"])) {
        		$buffer["files"] = array();
        	}
        }
        if (isset($params["dir"])) {
        	if (isset($buffer["dirs"])) {
        		foreach($params["dir"] as $part) {
        			if (!in_array($part, $buffer["dirs"])) {
        				$buffer["dirs"][] = $part;
        			}
        		}
        	} else {
        		$buffer["dirs"] = $params["dir"];
        	}
        } else {
        	if (!isset($buffer["dirs"])) {
        		$buffer["dirs"] = array();
        	}
        }

        $row["count"] = $this->_mfile->countBuffer();
        
        return json_encode($row);
    }
    
    /**
     * Переместить файлы и директории
     * 
     * @param array
     *    array $params["file"]
     *    array $params["dir"]
     * @return string $this->files()
     */
    function moveFiles() {
    	$curdir = $this->_curdir;
    	
    	$buffer = & $_SESSION["clip"];

    	if (isset($buffer["files"])) {
    		if ($buffer["files"] != 0) {
		        foreach($buffer["files"] as $part) {
		        	$md5 = $this->_mfile->getMD5FromID($part);
		        	$this->_access_md5 = $md5;
		        	$mode = $this->_getUserFileChmod();
	
		        	if ($mode == 2) {
		        		$this->_mfile->moveFiles($curdir, $md5);
		        	}
		        }
    		}
    	}
    	if (isset($buffer["dirs"])) {
    		if ($buffer["dirs"] != 0) {
	    		foreach($buffer["dirs"] as $part) {
	    			$dmode = $this->_getUserDirChmod($part);
	    			
	    			if ($dmode == "2") {
	    				$this->_mfile->moveDir($curdir, $part);
	    			}
	    		}
    		}
    	}
        
    	unset($_SESSION["clip"]);
    	
        return $this->files();
    }
    
    /**
     * Проверка существования файла, если файл существует возможности его перезаписи
     * 
     * @param array
     *    string $params["file"]
     * @return string "0"
     * @return string "1"
     */
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
        $mode = $this->_getUserFileChmod();
        
        $now = $this->_muser->getNowSize();
        $quota = $this->_muser->getUserQuota();
        if ($now >= $quota) {
        	$limit = 1;
        } else {
        	$limit = 0;
        }

        if ( ($mode == -1) or ($mode == 2) ) {
        	if ($dmode == 2) {
        		if (!$limit) {
        			$this->_mfile->issetFile($file, $curdir);
        			return "1";
        		} else {
        			return "0";
        		}
        	} else {
        		return "0";
        	}
        } else {
        	return "0";
        }
    }

    /**
     * Добавить текстовую заметку к файлу
     * 
     * @param array
     *    string $params["text"]
     *    string $params["md5"]
     * @return string (twig render) .../fm/ftext.tpl
     */
    function addFileText($params) {
    	$text = $params["text"];
    	$md5 = $params["md5"];
    	
    	$this->getFileParamsFromMd5($md5);
    	$fid = $this->_file["id"]; 
    	
    	$this->_mfile->addFileText($fid, $text);
    	
    	return $this->view->render("fm_ftext", array("text" => nl2br($text), "date" => date("Y-m-d H:i:s")));
    }

    /**
     * Рекурсивная функция для построения дерева пользователей
     * 
     * @param array $arr
     * Результат в string $this->_tree
     */
	private function print_array($arr) {
		if (!is_array($arr)) {
			return;
		}

		while(list($key, $val) = each($arr)) {
			if (!is_array($val)) {
				if ($val == null) {
					$val = "пусто";
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
    
	/**
	 * Вернуть права доступа на файл
	 * 
	 * @param array
	 *    string $params["md5"]
	 * @return array
	 */
    function getUsersChmod($params) {
    	$md5 = $params["md5"];
    	
    	$data = $this->_mfile->getUsersChmod($md5);

        return $data[0]["right"];
    }
    
    /**
     * Вернуть права доступа на директорию
     *
     * @param array
     *    int $params["did"]
     * @return array
     */
    function getUsersDirChmod($params) {
    	$did = $params["did"];
    	
		$data = $this->_mfile->getUsersDirChmod($did);

        return $data[0]["right"];
    }
    
    /**
     * Сохранить права доступа для файла
     * 
     * @param array
     *    string $params["json"]
     *    string $params["md5"]
     */
    function addFileRight($params) {
    	$json = $params["json"];
    	$md5 = $params["md5"];
    	
    	$this->getFileParamsFromMd5($md5);
    	
    	$this->_mfile->addFileRight($this->_file["min_id"], $json);
    }
    
    /**
     * Сохранить права доступа для директории
     * 
     * @param array
     *    string $params["json"]
     *    string $params["did"]
     */
    function addDirRight($params) {
    	$json = $params["json"];
    	$did = $params["did"];
    	
    	$this->_mfile->addDirRight($did, $json);
    }
    
    /**
     * Показать права доступа на директорию
     * 
     * @param array
     *    int $params["did"]
     * @return string (twig render) .../fm/dtree.tpl
     */
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

		return $this->view->render("fm_dtree", array("group" => $groups, "list" => $this->_tree, "did" => $did, "owner" => $this->_dir["owner"], "mode" => $mode));
    }
    
    /**
     * Получить имя текущей директории
     * 
     * @return string|NULL
     */
    function getCurDirName() {
    	$curdir = $this->_curdir;

    	$data = $this->_mfile->getCurDirName($curdir);
    	
    	if (isset($data[0]["name"])) {
    		return $data[0]["name"] . "/";
    	} else {
    		return null;
    	}
    }

    /**
     * "Расшарить" файл
     * 
     * @param array
     *    string $params["md5"]
     * @return array
     */
	function share($params) {
    	$md5 = $params["md5"];
    	
    	if ($this->_mfile->share($md5)) {
    		$this->_mfile->delShare($md5);
    		
    		$this->getFileParamsFromMd5($md5);
    		$row["fid"] = $this->_file["max_id"];
    		$row["action"] = "unshare";
    	} else {
    		$this->getFileParamsFromMd5($md5);
    		
    		if ($this->_file["pdirid"] == 0) {
    			$fname = $this->_file["filename"];
    		} else {
    			$fname = "(" . $this->_file["pdirid"] . ")" . $this->_file["filename"];
    		}

    		$this->_mfile->addShare($md5, $fname);
    		
    		$row["fid"] = $this->_file["max_id"];
    		$row["desc"] = $fname;
    		$row["action"] = "share";
    	}
    	
    	return json_encode($row);
    }
    
    /**
     * return $this->_getUserFileChmod()
     */
    function getUserFileChmod() {
    	return $this->_getUserFileChmod();
    }
    
    /**
     * Получить права доступа на файл
     * 
     * @return string "-1" - ошибка
     * @return string "2" - полный доступ
     * @return int:
     *    0 - доступа нет
     *    1 - только чтение
     *    2 - полный доступ
     */
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
    
    /**
     * Получить права доступа на директорию
     *
     * @return string "-1" - ошибка
     * @return string "2" - полный доступ
     * @return int:
     *    0 - доступа нет
     *    1 - только чтение
     *    2 - полный доступ
     */
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
    
    /**
     * Показать файловое дерево
     * 
     * @return string (twig render) .../block/left.tpl
     */
    function getTree() {
    	$fm = & $_SESSION["fm"];
    	if ($fm["admin"]) {
    		$this->_mfile->showTree(0, true);
    	} else {
    		$this->_mfile->showTree(0, false);
    	}
    	return $this->view->render("block_left", array("tree" => $this->_mfile->getTree()));
    }
    
    /**
     * Получить содержимое буфера
     * 
     * @return array
     *    string $row["buffer"]
     *    int $row["count"]
     */
    public function getClip() {
    	$buffer = & $_SESSION["clip"];
    	$clip = "";
    	if(isset($buffer["files"])) {
    		foreach($buffer["files"] as $part) {
    			$part = $this->_mfile->getFileNameFromID($part);
    			$part = htmlspecialchars($part);
    			if (mb_strlen($part) > 20) {
    				$shortname = mb_substr($part, 0, 10) . ".." . mb_substr($part, mb_strrpos($part, ".")-1, mb_strlen($part)-mb_strrpos($part, ".")+1);
    			} else {
    				$shortname = $part;
    			}
    			$clip .= "<li><span class='buf_file' title='" . $part . "'>" . $shortname . "</span></li>";
    		}
    	}
    	if(isset($buffer["dirs"])) {
    		foreach($buffer["dirs"] as $part) {
    			$part = htmlspecialchars($part);
    			$this->getDirParams($part);
    			$shortname = mb_substr($this->_dir["name"], 0, 12);
    			$part = $this->_dir["name"];
    			$clip .= "<li><span class='buf_folder' title='" . $part . "'>" .  $shortname . "</span></li>";
    		}
    	}
    	
    	if (strlen($clip) > 0) {
    		$row["buffer"] = "<li style='text-align: center'><a onclick='clearBuffer()' style='cursor: pointer'>очистить</a></li>" . $clip;
    	} else {
    		$row["buffer"] = "";
    	}
    	$row["count"] = $this->_mfile->countBuffer();
    	
    	return json_encode($row);
    }
    
    /**
     * Очистить буфер
     */
    public function clearBuffer() {
    	$buffer = & $_SESSION["clip"];
    	
    	$buffer["files"] = array();
    	$buffer["dirs"] = array();
    }
    
    /**
     * Показать пользовательскую квоту
     * 
     * @return string (twig render) .../users/quota.tpl
     */
    public function getUserQuota() {
    	$now_big = $now = $this->_mfile->getNowSize();
    
    	if (($now / 1024) > 1) {
    		$now = round($now / 1024, 2) . "&nbsp;Kb";
    	} else { $now = round($now, 2) . "&nbsp;B";
    	};
    	if (($now / 1024) > 1) {
    		$now = round($now / 1024, 2) . "&nbsp;Mb";
    	};
    
    	$quota_big = $quota = $this->_mfile->getUserQuota();
    
    	if (($quota / 1024) > 1) {
    		$quota = round($quota / 1024, 2) . "&nbsp;Kb";
    	} else { $quota = round($quota, 2) . "&nbsp;B";
    	};
    	if (($quota / 1024) > 1) {
    		$quota = round($quota / 1024, 2) . "&nbsp;Mb";
    	};
    
    	$percent = round($now_big / $quota_big * 100, 0);
    	return $this->view->render("users_quota", array("now" => $now, "quota" => $quota, "percent" => $percent));
    }
    
    /**
     * Показать содержимое буфера
     * 
     * @return string (json array)
     *    $row[$i]["name"] - имя файла директории
     *    $row[$i]["pdirid"] - ID родительской директории
     */
    public function fromBuffer() {
    	$buffer = & $_SESSION["clip"];
    	$i = 0;
    	
    	if (isset($buffer["files"])) {
    		foreach($buffer["files"] as $part) {
    			$pdirid = $this->_mfile->getFilePdiridFromID($part);
    			$part = $this->_mfile->getFileNameFromID($part);    			
    			$part = htmlspecialchars($part);
    			if (mb_strlen($part) > 20) {
    				$shortname = mb_substr($part, 0, 10) . ".." . mb_substr($part, mb_strrpos($part, ".")-1, mb_strlen($part)-mb_strrpos($part, ".")+1);
    			} else {
    				$shortname = $part;
    			}
    			
    			$row[$i]["name"] = $shortname;
    			$row[$i]["pdirid"] = $pdirid;
    			
    			$i++;
    		}
    	}
    	
    	return json_encode($row);
    }
}