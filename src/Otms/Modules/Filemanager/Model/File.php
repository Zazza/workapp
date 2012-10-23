<?php

/**
 * This file is part of the Workapp project.
 *
 * Filemanager Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Filemanager\Model; 

use Engine\Modules\Model;
use PDO;

/**
 * Model\File class
 * 
 * Главный класс - модель, обеспечивающий работу файлового менеджера
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class File extends Model {
	/**
	 * Настройки модуля
	 * 
	 * @var array
	 */
	private $_config;
	
	/**
	 * Группа, которой принадлежит файл
	 * 
	 * @var array
	 */
	private $_groups = null;
	
	/**
	 * Пользователь, которому принадлежит файл
	 * 
	 * @var array
	 */
	private $_users = null;
	
	/**
	 * Путь до share файла
	 * 
	 * @var string
	 */
	private $_desc = null;

	/**
	 * Приватная переменная для рекурсивного удаления файлов
	 * @var array
	 */
	private $_rec = array();
	
	/**
	 * Дерево файлов
	 * 
	 * @var string
	 */
	private $_tree = null;
	
	/**
	 * ID текущей директории
	 * 
	 * @var int
	 */
	private $_curdir = 0;
	
	/**
	 * ID директории
	 * 
	 * @var int
	 */
	private $_did = 0;
	
	/**
	 * Иконка файла, в зависимости от расширения
	 * 
	 * @var array
	 */
	private $_MIME = array(
			array("name" => "img", "ico" => "preview", "ext" => array("jpg", "jpeg", "gif", "png", "bmp")),
			array("name" => "doc", "ico" => "msword.png", "ext" => array("doc", "docx", "rtf", "oft")),
			array("name" => "pdf", "ico" => "pdf.png", "ext" =>  array("pdf", "djvu")),
			array("name" => "txt", "ico" => "text.png", "ext" =>  array("txt")),
			array("name" => "flv", "ico" => "flash.png", "ext" =>  array("flv")),
			array("name" => "exe", "ico" => "executable.png", "ext" =>  array("exe", "com", "bat")),
			array("name" => "xls", "ico" => "excel.png", "ext" =>  array("xls", "xlsx")),
			array("name" => "mp3", "ico" => "audio.png", "ext" =>  array("mp3", "wav", "flac")),
			array("name" => "html", "ico" => "html.png", "ext" =>  array("html", "htm", "php", "js")),
			array("name" => "zip", "ico" => "compress.png", "ext" =>  array("zip", "rar", "7z", "tar", "bz2", "gz"))
	);
	
	function __construct($config) {
		parent::__construct($config);
		$this->config = $config;

		if (isset($this->registry["post"]["did"])) {
			$this->_curdir = $this->registry["post"]["did"];
		}
	}

	/**
	 * Присвоить файлу иконку или превью, если есть
	 * 
	 * @param string $ext
	 * @param string $md5
	 */
	public function setIcon($ext, $md5) {
		$ico = $this->registry["uri"] . "img/filemanager/ftypes/unknown.png";
		
		for($i=0; $i<count($this->_MIME); $i++) {
			if (in_array(mb_strtolower($ext), $this->_MIME[$i]["ext"])) {
				$ico = $this->_MIME[$i]["ico"];
				if ($ico == "preview") {
					$ico = $this->registry['path']['upload'] . "_thumb/" . $md5;
				} else {
					$ico = $this->registry["uri"] . "img/filemanager/ftypes/" . $ico;
				}
			}
		}
		
		return $ico;
	}
	
	/**
	 * Получить md5 имя файла из пути к share ресурсу
	 * 
	 * @param string $filename
	 * @return array
	 *    $data[0]["md5"]
	 */
	public function getMD5FromName($filename) {
		$sql = "SELECT s.md5 AS `md5`
					FROM fm_share AS s
					WHERE s.desc = :filename
					LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":filename" => $filename);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $data;
	}

	
	/**
	 * Получить реальное имя файла, владельца и права на него
	 * 
	 * @param string $md5
	 * @return array
	 */
	public function attachFromMD5($md5) {
		$sql = "SELECT f.filename AS `filename`, h.uid, r.right AS `right`
						FROM fm_fs AS f
						LEFT JOIN fm_fs AS f1 ON (f1.filename = f.filename)
						LEFT JOIN fm_fs_history AS h ON (h.fid = f.id)
						LEFT JOIN fm_fs_chmod AS r ON (r.fid = f1.id)
						WHERE f.md5 = :md5 AND r.right != 'NULL'
						LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":md5" => $md5);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $data;
	}
	
	/**
	 * Получить реальное имя файла, владельца и права на него
	 * 
	 * @param string $filename
	 * @param int $curdir
	 * @return array
	 */
	public function attachFromName($filename, $curdir) {
		 $sql = "SELECT f.md5 AS `md5`, h.uid, r.right AS `right`
		FROM fm_fs AS f
		LEFT JOIN fm_fs AS f1 ON (f1.filename = f.filename)
		LEFT JOIN fm_fs_history AS h ON (h.fid = f.id)
		LEFT JOIN fm_fs_chmod AS r ON (r.fid = f1.id)
		WHERE f.filename = :filename AND f.pdirid = :pdirid
		ORDER BY f.id DESC
		LIMIT 1";
        
        $res = $this->registry['db']->prepare($sql);
		$param = array(":filename" => $filename, ":pdirid" => $curdir);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
	
		return $data;
	}
	
	/**
	 * Получить информацию о директории
	 * 
	 * @param int $did
	 * @return array
	 */
	function getDirParams($did) {
		$sql = "SELECT fd.id, fd.uid, fd.name AS `name`, fdc.right, users.login AS owner
		        FROM fm_dirs AS fd
		        LEFT JOIN fm_dirs_chmod AS fdc ON (fdc.did = fd.id)
		        LEFT JOIN users ON (users.id = fd.uid)
		        WHERE fd.id = :did
		        LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":did" => $did);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		if (count($data) > 0) {
			return $data[0];
		} else {
			return false;
		}
	}
	
	/**
	 * Получить полную информацию о файле, включая его изменения
	 * 
	 * @param string $md5
	 * @return array
	 */
	function getFileParamsFromMd5($md5) {
		$sql = "SELECT f.id, f.filename, f.size, h.timestamp, MIN(f1.id) AS min_id, MAX(f1.id) AS max_id, f.pdirid, h.uid AS uid
	        FROM fm_fs AS f
	        LEFT JOIN fm_fs_history AS h ON (h.fid = f.id)
	        LEFT JOIN fm_fs AS f1 ON (f.filename = f1.filename)
	        WHERE f.md5 = :md5
	        LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":md5" => $md5);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
	
		$right[0]["count"] = 0;
	
		$sql = "SELECT COUNT(id) AS count FROM fm_fs_chmod WHERE fid = :fid LIMIT 1";
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $data[0]["min_id"]);
		$res->execute($param);
		$right = $res->fetchAll(PDO::FETCH_ASSOC);
	
		if ($right[0]["count"] == 1) {
			$data[0]["right"] = true;
		} else {
			$data[0]["right"] = false;
		}
	
		return $data[0];
	}
	
	/**
	 * Получить полную информацию о файле, включая его изменения

	 * @param string $filename
	 *    $this->_curdir - ID текущей директории
	 * @return array
	 */
	function getFileParamsFromName($filename) {
		$curdir = $this->_curdir;
	
		$sql = "SELECT f.id, f.md5, f.size, h.timestamp, f.pdirid, h.uid AS uid
	        FROM fm_fs AS f
	        LEFT JOIN fm_fs_history AS h ON (h.fid = f.id)
	        WHERE f.filename = :filename AND f.pdirid = :pdirid
	        ORDER BY f.id DESC
	        LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":filename" => $filename, ":pdirid" => $curdir);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		$sql = "SELECT MIN(f.id) AS min_id, MAX(f.id) AS max_id
		FROM fm_fs AS f
		WHERE f.filename = :filename AND f.pdirid = :pdirid
		LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":filename" => $filename, ":pdirid" => $curdir);
		$res->execute($param);
		$p = $res->fetchAll(PDO::FETCH_ASSOC);
		
		$data[0]["min_id"] = $p[0]["min_id"];
		$data[0]["max_id"] = $p[0]["max_id"];
	
		$right[0]["count"] = 0;
	
		$sql = "SELECT COUNT(id) AS count FROM fm_fs_chmod WHERE fid = :fid LIMIT 1";
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $data[0]["min_id"]);
		$res->execute($param);
		$right = $res->fetchAll(PDO::FETCH_ASSOC);
	
		if ($right[0]["count"] == 1) {
			$data[0]["right"] = true;
		} else {
			$data[0]["right"] = false;
		}
	
		return $data[0];
	}
	
	/**
	 * Получить права на директорию
	 * 
	 * @param int $curdir
	 * @return array
	 */
	function getRight($curdir) {
		$sql = "SELECT d.uid, r.right AS `right`
		    		FROM fm_dirs AS d
		    		LEFT JOIN fm_dirs_chmod AS r ON (r.did = d.id)
		    		WHERE d.id = :id
		    		LIMIT 1";
			
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $curdir);
		$res->execute($param);
		$right = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $right;
	}
	
	/**
	 * Получить полную админскую информацию о директории
	 * 
	 * @param int $curdir
	 * @return array
	 */
	function getAdminDirs($curdir) {
		$sql = "SELECT d.id, d.uid, d.name AS `name`, r.right AS `right`, d.close AS `close`
	    		FROM fm_dirs AS d
	    		LEFT JOIN fm_dirs_chmod AS r ON (r.did = d.id)
	    		WHERE d.pid = :pid";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":pid" => $curdir);
		$res->execute($param);
		$dirs = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $dirs;
	}
	
	/**
	 * Получить полную (не админскую) информацию о директории
	 * 
	 * @param int $curdir
	 * @return array
	 */
	function getDirs($curdir) {
		$sql = "SELECT d.id, d.uid, d.name AS `name`, r.right AS `right`, d.close AS `close`
	    		FROM fm_dirs AS d
	    		LEFT JOIN fm_dirs_chmod AS r ON (r.did = d.id)
	    		WHERE d.pid = :pid AND d.close = 0";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":pid" => $curdir);
		$res->execute($param);
		$dirs = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $dirs;
	}
	
	/**
	 * Получить полную админскую информацию о файлах в текущей директории
	 *
	 * @param int $curdir
	 * @return array
	 */
	function getAdminFiles($curdir) {
		$sql = "SELECT DISTINCT(f.id), f.md5, f.filename AS `name`, f.size, h.uid, h.timestamp, r.right AS `right`, f.close AS `close`, s.desc AS share
				FROM fm_fs AS f
				LEFT JOIN fm_fs AS f1 ON (f1.filename = f.filename)
				LEFT JOIN fm_fs_history AS h ON (h.fid = f.id)
				LEFT JOIN fm_fs_chmod AS r ON (r.fid = f1.id)
				LEFT OUTER JOIN fm_share AS s ON (s.md5 = f.md5)
				WHERE f.pdirid = :pid AND r.right != 'NULL'
				AND f.id IN 
				(
					SELECT MAX(id) FROM fm_fs GROUP BY filename, pdirid ORDER BY id DESC
				)
				GROUP BY f.filename
				ORDER BY f.filename, f.id DESC";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":pid" => $curdir);
		$res->execute($param);
		$files = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $files;
	}
	
	/**
	 * Получить полную (не админскую) информацию о файлах в текущей директории
	 *
	 * @param int $curdir
	 * @return array
	 */
	function getFiles($curdir) {
		$sql = "SELECT DISTINCT(f.id), f.md5, f.filename AS `name`, f.size, h.uid, h.timestamp, r.right AS `right`, f.close AS `close`, s.desc AS share
				FROM fm_fs AS f
				LEFT JOIN fm_fs AS f1 ON (f1.filename = f.filename)
				LEFT JOIN fm_fs_history AS h ON (h.fid = f.id)
				LEFT JOIN fm_fs_chmod AS r ON (r.fid = f1.id)
				LEFT OUTER JOIN fm_share AS s ON (s.md5 = f.md5)
				WHERE f.pdirid = :pid AND f.close = 0 AND r.right != 'NULL'
				GROUP BY f.filename
				ORDER BY f.filename";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":pid" => $curdir);
		$res->execute($param);
		$files = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $files;	
	}
	
	/**
	 * Пометить файл как удалённый
	 * 
	 * @param string $fname
	 *    $this->_curdir
	 */
	function delfile($fname) {
		$curdir = $this->_curdir;

		$sql = "UPDATE fm_fs SET `close` = '1' WHERE `filename` = :filename AND pdirid = :pdirid";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":filename" => $fname, ":pdirid" => $curdir);
		$res->execute($param);
	}
	
	/**
	 * Реальное удаление файла из ФС
	 * 
	 * @param string $fname
	 *    $this->_curdir
	 */
	function delfilereal($fname) {
		$curdir = $this->_curdir;
		
		$data = $this->getFileParamsFromName($fname);
		
		if (file_exists($this->registry["rootPublic"] . $this->registry["path"]["upload"] . $data["md5"])) {
			unlink($this->registry["rootPublic"] . $this->registry["path"]["upload"] . $data["md5"]);
			unlink($this->registry["rootPublic"] . $this->registry["path"]["upload"] . "_thumb/" . $data["md5"]);
		}

		$sql = "DELETE FROM fm_fs WHERE `filename` = :filename AND pdirid = :pdirid";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":filename" => $fname, ":pdirid" => $curdir);
		$res->execute($param);
		
		$sql = "DELETE FROM fm_fs_chmod WHERE `fid` = :fid";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $data["id"]);
		$res->execute($param);
		
		$sql = "DELETE FROM fm_fs_history WHERE `fid` = :fid";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $data["id"]);
		$res->execute($param);
	}
	
	/**
	 * Получить суммарный размер файлов в текущей директории
	 *    close = 0
	 * 
	 * @return string
	 */
	function getTotalSize() {
		$curdir = $this->_curdir;
	
		$totalSize = 0;
	
		$sql = "SELECT f.filename, f.size, h.timestamp
			FROM fm_fs AS f
			LEFT JOIN fm_fs_history AS h ON (h.fid = f.id)
			WHERE f.pdirid = :pid AND f.close = 0";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":pid" => $curdir);
		$res->execute($param);
		$files = $res->fetchAll(PDO::FETCH_ASSOC);
	
		for($i=0; $i<count($files); $i++) {
			$totalSize += $files[$i]["size"];
		}
	
		if (($totalSize / 1024) > 1) {
			$totalSize = round($totalSize / 1024, 2) . "&nbsp;Кб";
		} else { $totalSize = round($totalSize, 2) . "&nbsp;Б";
		};
		if (($totalSize / 1024) > 1) {
			$totalSize = round($totalSize / 1024, 2) . "&nbsp;Мб";
		};
	
		return $totalSize;
	}
	
	/**
	 * Получить ID родительской директории
	 * 
	 * @param int $dirid
	 * @return array
	 *    $data[0]["pid"]
	 */
	function getPidFromDir($dirid) {
		$sql = "SELECT `pid` FROM fm_dirs WHERE `id` = :id LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $dirid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $data;
	}
	
	/**
	 * Получить имя директории
	 * 
	 * @param int $dirid
	 * @return array
	 *    $data[0]["name"]
	 */
	function getNameFromDir($dirid) {
		$sql = "SELECT `name` FROM fm_dirs WHERE `id` = :id LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $dirid);
		$res->execute($param);
		$dirname = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $dirname;
	}
	
	/**
	 * Получить ID директории по её имени и ID родительской директории
	 * 
	 * @param string $dir - имя директории
	 * @param int $pid - ID родительской директории
	 * @return array
	 *    $data[0]["id"]
	 */
	function getDirIdFromNameAndPid($dir, $pid) {
		$sql = "SELECT `id` FROM fm_dirs WHERE `name` = :name AND pid = :pid LIMIT 1";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":name" => $dir, "pid" => $pid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
			
		return $data;
	}
	
	/**
	 * Создать директорию
	 * 
	 * @param int $curdir - ID текущей директории
	 * @param string $dirName - имя новой директории
	 * @return boolean
	 */
	function createDir($curdir, $dirName) {
		$sql = "SELECT count(id) AS count FROM fm_dirs WHERE `name` = :name AND `pid` = :pid LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":pid" => $curdir, ":name" => $dirName);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		if ($data[0]["count"] == 0) {
			$sql = "INSERT INTO fm_dirs (uid, `pid`, `name`) VALUES (:uid, :pid, :name)";
			 
			$res = $this->registry['db']->prepare($sql);
			$param = array(":uid" => $this->registry["ui"]["id"], ":pid" => $curdir, ":name" => htmlspecialchars($dirName));
			$res->execute($param);
				
			$did = $this->registry['db']->lastInsertId();
			$this->_did = $did;
				
			$sql = "INSERT INTO fm_dirs_chmod (did, `right`) VALUES (:did, :json)";
			 
			$res = $this->registry['db']->prepare($sql);
			$param = array(":did" => $did, ":json" => '{"frall":"2"}');
			$res->execute($param);
			
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Getter $this->_did
	 */
	function getDid() {
		return $this->_did;
	}
	
	/**
	 * Пометить директорию как удалённую
	 * 
	 * @param int $curdir
	 */
	function rmDir($curdir) {
		$sql = "UPDATE fm_dirs SET close = 1 WHERE id = :pid AND close = 0 LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":pid" => $curdir);
		$res->execute($param);
	}
	
	/**
	 * Рекурсивно пройтись по директории
	 * 
	 * @param int $id
	 * Результат в array $this->_rec
	 */
	private function _recursDir($id) {
		$sql = "SELECT id FROM fm_dirs WHERE `pid` = :pid";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":pid" => $id);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($data) > 0) {
			foreach($data as $part) {
				$this->_rec[] = $id;
				$this->_recursDir($part["id"]);
			}
		} else {
			$this->_rec[] = $id;
		}
	}
	
	/**
	 * Реальное рекурсивное удаление директории и файлов
	 * 
	 * @param int $did
	 */
	function rmDirReal($did) {
		$this->_recursDir($did);
		$this->_rec = array_unique($this->_rec);
		foreach($this->_rec as $part) {
			$sql = "DELETE FROM fm_dirs WHERE id = :id";
			
			$res = $this->registry['db']->prepare($sql);
			$param = array(":id" => $part);
			$res->execute($param);
			
			$sql = "DELETE FROM fm_dirs_chmod WHERE did = :id";
			
			$res = $this->registry['db']->prepare($sql);
			$param = array(":id" => $part["id"]);
			$res->execute($param);
			
			$this->_delFileFromDirReal($part);
		}
	}
	
	/**
	* Реальное удаление файлов в директории
	*
	* @param int $pid - ID директории
	*/
	private function _delFileFromDirReal($pid) {
		$sql = "SELECT id, filename FROM fm_fs WHERE pdirid = :pid";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":pid" => $pid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($data as $part) {
			$this->_curdir = $pid;
			$this->delfilereal($part["filename"]);
			/*
			$sql = "DELETE FROM fm_fs WHERE `filename` = :filename AND pdirid = :pdirid";
			
			$res = $this->registry['db']->prepare($sql);
			$param = array(":filename" => $part["filename"], ":pdirid" => $pid);
			$res->execute($param);
				
			$sql = "DELETE FROM fm_fs_chmod WHERE `fid` = :fid";
				
			$res = $this->registry['db']->prepare($sql);
			$param = array(":fid" => $part["id"]);
			$res->execute($param);
				
			$sql = "DELETE FROM fm_fs_history WHERE `fid` = :fid";
				
			$res = $this->registry['db']->prepare($sql);
			$param = array(":fid" => $part["id"]);
			$res->execute($param);
			*/
		}
	}

	/**
	* Перемещение файла.
	* Смена директории для $md5 файла
	*
	* @param int $curdir - ID текущей директории, куда выполняется перемещение
	* @param string $md5
	*/
	function moveFiles($curdir, $md5) {
		$sql = "UPDATE fm_fs SET pdirid = :dir WHERE `md5` = :md5 LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":dir" => $curdir, ":md5" => $md5);
		$res->execute($param);
	}
	
	/**
	* !!!
	*
	* @param int $current
	* @param int $id
	* @return boolean
	*/
	private function _showRecursDid($current, $id) {
		$sql = "SELECT pid FROM fm_dirs WHERE id = :id LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		if (isset($data[0]["pid"])) {
			if ($data[0]["pid"] == 0) {
				return true;
			} else if ($data[0]["pid"] == $current) {
				return false;
			} else {
				return $this->_showRecursDid($current, $data[0]["pid"]);
			}
		} else {
			return true;
		}
	}
	
	/**
	* Перемещение директории.
	*
	* @param int $curdir - ID текущей директории, куда происходит перемещение
	* @param int $did - ID перемещаемой директории
	*/
	function moveDir($curdir, $did) {
		if ($curdir != $did) {
			if ($this->_showRecursDid($did, $curdir)) {
				$sql = "UPDATE fm_dirs SET pid = :pid WHERE id = :id AND close = 0";
				
				$res = $this->registry['db']->prepare($sql);
				$param = array(":pid" => $curdir, ":id" => $did);
				$res->execute($param);
			}
		}
	}
	
	/**
	* Пометка файла как удалённый.
	*
	* @param string $file
	* @param int $curdir
	*/
	function issetFile($file, $curdir) {
		$sql = "UPDATE fm_fs SET `close` = 1 WHERE `filename` = :filename AND pdirid = :pdirid";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":filename" => $file, ":pdirid" => $curdir);
		$res->execute($param);
	}
	
	/**
	* Получить имя файла по его ID
	*
	* @param int $fid
	* @return string
	*/
	function getFileNameFromID($fid) {
		$sql = "SELECT filename FROM fm_fs WHERE id = :fid";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $fid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $data[0]["filename"];
	}
	
	/**
	* Получить ID родительской директории по ID файла
	*
	* @param int $fid
	* @return int
	*/
	function getFilePdiridFromID($fid) {
		$sql = "SELECT pdirid FROM fm_fs WHERE id = :fid";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $fid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		return $data[0]["pdirid"];
	}
	
	/**
	* Получить историю изменения файла.
	*
	* @param string $md5
	* @param int $curdir
	* @return array
	*/
	function getFileHistory($md5, $curdir) {
		$sql = "SELECT h.timestamp AS `timestamp`, h.uid, fs1.md5
	        FROM fm_fs AS fs
	        LEFT JOIN fm_fs AS fs1 ON (fs.filename = fs1.filename)
	        LEFT JOIN fm_fs_history AS h ON (h.fid = fs1.id)
	        WHERE fs.md5 = :md5 AND fs.pdirid = :pdirid AND fs1.pdirid = :pdirid
	        ORDER BY h.timestamp DESC";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":md5" => $md5, ":pdirid" => $curdir);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
	
		return $data;
	}
	
	/**
	* Получить описание файла.
	*
	* @param string $md5
	* @param int $curdir
	* @return array
	*/
	function getFileText($md5, $curdir) {
		$sql = "SELECT t.uid, t.text AS `text`, t.timestamp AS `timestamp`
	    	FROM fm_fs AS fs
	        LEFT JOIN fm_fs AS fs1 ON (fs.filename = fs1.filename)
	        LEFT JOIN fm_text AS t ON (t.fid = fs1.id)
	        WHERE fs.md5 = :md5 AND fs.pdirid = :pdirid AND fs1.pdirid = :pdirid
	    	ORDER BY timestamp DESC";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":md5" => $md5, ":pdirid" => $curdir);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
	
		return $data;
	}
	
	/**
	* Добавить текстовое описание к файлу
	*
	* @param int $fid
	* @param string $text
	*/
	function addFileText($fid, $text) {
		$sql = "INSERT INTO fm_text SET fid = :fid, uid = :uid, `text` = :text";
		 
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $fid, ":uid" => $this->registry["ui"]["id"], ":text" => htmlspecialchars(nl2br($text)));
		$res->execute($param);
	}
	
	/**
	* Получить права для контекстного меню
	*/
	function getFileChmod() {
		$sql = "SELECT ug.id AS pid, ug.name AS pname, usg.id AS sid, usg.name AS sname
	        FROM users_group AS ug
	        LEFT JOIN users_subgroup AS usg ON (usg.pid = ug.id)
	        ORDER BY ug.id";
	
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$this->_groups = $res->fetchAll(PDO::FETCH_ASSOC);
	
		$sql = "SELECT u.id, ug.id AS gid, ug.name AS gname
	        FROM users AS u
	        LEFT JOIN users_priv AS up ON (up.id = u.id)
	        LEFT JOIN users_subgroup AS ug ON (ug.id = up.group)
	        GROUP BY up.id";
	
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$this->_users = $res->fetchAll(PDO::FETCH_ASSOC);
	}
	
	/**
	* Getter $this->_groups
	*/
	function getGroups() {
		return $this->_groups;
	}
	
	/**
	* Getter $this->_users
	*/
	function getUsers() {
		return $this->_users;
	}
	

	/**
	* Получить права доступа на файл
	*
	* @param string $md5
	* @return array
	*/
	function getUsersChmod($md5) {
		$sql = "SELECT r.right AS `right`
	        FROM fm_fs AS fs
	        LEFT JOIN fm_fs AS fs1 ON (fs1.filename = fs.filename)
	        LEFT JOIN fm_fs_chmod AS r ON (r.fid = fs1.id)
	        WHERE fs.md5 = :md5
	        LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":md5" => $md5);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
	
		return $data;
	}
	
	/**
	* Получить права доступа на диреткорию
	*
	* @param int $did
	* @return array
	*/
	function getUsersDirChmod($did) {
		$sql = "SELECT `right`
	        FROM fm_dirs_chmod
	        WHERE did = :did
	        LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":did" => $did);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
	
		return $data;
	}
	
	/**
	* Обновить права на файл
	*
	* @param int $fid
	* @param string $json - JSON права
	*/
	function addFileRight($fid, $json) {
		$sql = "UPDATE fm_fs_chmod SET `right` = :json WHERE fid = :fid LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $fid, ":json" => $json);
		$res->execute($param);
	}
	
	/**
	* Обновить права на директорию
	*
	* @param int $did
	* @param string $json - JSON права
	*/
	function addDirRight($did, $json) {
		$sql = "UPDATE fm_dirs_chmod SET `right` = :json WHERE did = :did LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":did" => $did, ":json" => $json);
		$res->execute($param);
	}

	/**
	* Получить имя директории
	*
	* @param int $curdir - ID директории
	* @return array
	*/
	function getCurDirName($curdir) {
		$sql = "SELECT `name`
	    	FROM fm_dirs
	    	WHERE id = :pdirid AND close = 0
	    	LIMIT 1";
		 
		$res = $this->registry['db']->prepare($sql);
		$param = array(":pdirid" => $curdir);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		 
		return $data;
	}
	
	/**
	* Получить путь до расшаренного файл
	*
	* @param string $md5
	* @return boolean
	*    $this->_desc - описание, получить getDesc()
	*/
	function share($md5) {
		$sql = "SELECT COUNT(id) AS count, `desc`
	    	    	FROM fm_share
	    	    	WHERE md5 = :md5
	    	    	LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":md5" => $md5);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
	
		if ($row[0]["count"]) {
			$this->_desc = $row[0]["desc"];
	
			return true;
		} else {
			return false;
		}
	}
	
	/**
	* Getter $this->_desc
	*/
	function getDesc() {
		return $this->_desc;
	}
	
	/**
	* Добавить шару
	*
	* @param string $md5
	* @param string $fname - путь до шары (URL)
	*/
	function addShare($md5, $fname) {
		$sql = "INSERT INTO fm_share (`md5`, `desc`) VALUES (:md5, :desc)";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":md5" => $md5, ":desc" => $fname);
		$res->execute($param);
	}
	
	/**
	* Удалить шару
	*
	* @param string $md5
	*/
	function delShare($md5) {
		$sql = "DELETE FROM `fm_share` WHERE `md5` = :md5 LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":md5" => $md5);
		$res->execute($param);
	}

	/**
	* Получить MD5 файла по имени файла и родительской директории
	* @param string $filename
	* @param int $dir - ID директории
	* @return string
	*/
	function getMD5FromFnameDir($filename, $dir) {
		$sql = "SELECT md5
		FROM fm_fs
		WHERE filename = :filename AND pdirid = :dir
		LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":filename" => $filename, ":dir" => $dir);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $data[0]["md5"];
	}
	
	/**
	* Получить MD5 файла по его ID
	*
	* @param int $fid
	* @return string
	*/
	function getMD5FromID($fid) {
		$sql = "SELECT md5
			FROM fm_fs
			WHERE id = :fid
			LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $fid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
	
		return $data[0]["md5"];
	}
	
	/**
	* Восстановить файл по имени и родительской директории
	*
	* @param string $filename
	* @param int $pid
	*/
	function restoreFile($filename, $pid) {
		$sql = "UPDATE fm_fs SET `close` = '0' WHERE filename = :filename AND pdirid = :pdirid ORDER BY id DESC LIMIT 1";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":filename" => $filename, ":pdirid" => $pid);
		$res->execute($param);
	}
	
	/**
	* Восстановить директорию по её ID
	*
	* @param int $id
	*/
	function restoreDir($id) {
		$sql = "UPDATE fm_dirs SET `close` = '0' WHERE id = :id LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
	}
	
	/**
	* Рекурсивная функция - построение полного дерева директорий
	*
	* @param int $pid - ID текущей диреткории
	* @param boolean $admin
	* Результат в $this->_tree
	*/
	function showTree($pid, $admin = false) {
		if ($admin) {
			$sql = "SELECT id,name,pid FROM fm_dirs WHERE pid = :pid ORDER BY name";
		} else {
			$sql = "SELECT id,name,pid FROM fm_dirs WHERE pid = :pid AND close = 0 ORDER BY name";
		}
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":pid" => $pid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
	
		if (count($data) > 0) {
			$this->_tree .= "<ul>";
			foreach($data as $part) {
				$id1 = $part["id"];
				$this->_tree .= "<li>";
				$this->_tree .= "<span class='folder' title='d_" . $part["id"] . "'><a class='tbranch' href='" . $this->registry["uri"] . "filemanager/?id=" . rawurlencode($part["id"]) . "'>" . $part["name"] . "</a></span>";
				$this->showTree($id1, $admin);
			}
			$this->_tree .= "</ul>";
		}
	}
	
	/**
	* Getter $this->_tree
	*/
	function getTree() {
		return $this->_tree;
	}
	
	/**
	* Переименовать директорию
	*
	* @param int $did
	* @param string name
	*/
	function dirRename($did, $name) {
		$sql = "UPDATE fm_dirs SET `name` = :name WHERE id = :did LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":did" => $did, ":name" => htmlspecialchars($name));
		$res->execute($param);
	}
	
	/**
	* Переименовать файл
	*
	* @param int $curdir
	* @param string $oldname
	* @param string $newname
	*/
	function fileRename($curdir, $oldname, $newname) {
		$sql = "UPDATE fm_fs SET filename = :newname WHERE filename = :oldname AND pdirid = :curdir";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":curdir" => $curdir, ":oldname" => htmlspecialchars($oldname), ":newname" => htmlspecialchars($newname));
		$res->execute($param);
	}
	
	/**
	* Получить количество файлов и директорий в буфере
	*
	* @return int
	*/
	public function countBuffer() {
		$count = 0;
		 
		$buffer = & $_SESSION["clip"];
		if(isset($buffer["files"])) {
			if ($buffer["files"] > 0) {
				foreach($buffer["files"] as $part) {
					$count++;
				}
			}
		}
		if(isset($buffer["dirs"])) {
			if ($buffer["dirs"] > 0) {
				foreach($buffer["dirs"] as $part) {
					$count++;
				}
			}
		}
		 
		return $count;
	}
	
	/**
	* Получить занимаемое простарнство в ФС текущим пользователем
	*
	* @return int
	*/
	function getNowSize() {
		$sql = "SELECT SUM(f.size) AS sum
		FROM fm_fs AS f
		LEFT JOIN fm_fs_history AS h ON (h.fid = f.id)
		WHERE h.uid = :id";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $this->registry["ui"]["id"]);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
	
		return $data[0]["sum"];
	}
	
	/**
	* Получить установленную для текущего пользователя квоту
	*
	* @return int
	*/
	function getUserQuota() {
		$sql = "SELECT quota FROM users WHERE id = :id LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $this->registry["ui"]["id"]);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
	
		return $data[0]["quota"];
	}
	
	/**
	 * Получить список пользователей с их квотами и занимаемым ими дисковым пространством
	 * 
	 * @return array
	 */
	function getTotal() {
		$sql = "SELECT SUM(size) AS sum FROM fm_fs";
	
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
	
		$data["all"] = $row[0]["sum"];
	
		$sql = "SELECT SUM(f.size) AS sum, u.login AS `login`, u.quota AS `quota`
		FROM fm_fs AS f
		LEFT JOIN fm_fs_history AS h ON (h.fid = f.id)
		LEFT JOIN users AS u ON (u.id = h.uid)
		GROUP BY h.uid
		ORDER BY sum DESC";
	
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
	
		$data["users"] = $row;
	
		return $data;
	}
}
?>
