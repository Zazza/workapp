<?php
class Model_File extends Modules_Model {
	private $_config;
	private $_groups = null;
	private $_users = null;
	private $_desc = null;	
	private $_rec = array();
	private $_tree = null;
	private $_curdir = 0;
	
	private $_did = 0;
	
	function __construct($config) {
		parent::__construct($config);
		$this->config = $config;

		if (isset($this->registry["post"]["did"])) {
			$this->_curdir = $this->registry["post"]["did"];
		}
	}
	
	// ICO MIME TYPE
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

	public function setIcon($ext, $md5) {
		$ico = $this->registry["path"]["modules"] . "FM/img/ftypes/unknown.png";
		
		for($i=0; $i<count($this->_MIME); $i++) {
			if (in_array(mb_strtolower($ext), $this->_MIME[$i]["ext"])) {
				$ico = $this->_MIME[$i]["ico"];
				if ($ico == "preview") {
					$ico = $this->registry['path']['upload'] . "_thumb/" . $md5;
				} else {
					$ico = $this->registry["path"]["modules"] . "FM/img/ftypes/" . $ico;
				}
			}
		}
		
		return $ico;
	}
	// END ICO MIME TYPE
	
	// SHARE
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
	// END SHARE
	
	// ATTACH
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
	// END ATTACH
	
	// FM AJAX
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

		return $data[0];
	}
	
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
	
	function delfile($fname) {
		$curdir = $this->_curdir;

		$sql = "UPDATE fm_fs SET `close` = '1' WHERE `filename` = :filename AND pdirid = :pdirid";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":filename" => $fname, ":pdirid" => $curdir);
		$res->execute($param);
	}
	
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
	
	function getPidFromDir($dirid) {
		$sql = "SELECT `pid` FROM fm_dirs WHERE `id` = :id LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $dirid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $data;
	}
	
	function getNameFromDir($dirid) {
		$sql = "SELECT `name` FROM fm_dirs WHERE `id` = :id LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $dirid);
		$res->execute($param);
		$dirname = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $dirname;
	}
	
	function getDirIdFromNameAndPid($dir, $pid) {
		$sql = "SELECT `id` FROM fm_dirs WHERE `name` = :name AND pid = :pid LIMIT 1";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":name" => $dir, "pid" => $pid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
			
		return $data;
	}
	
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
	
	function getDid() {
		return $this->_did;
	}
	
	function rmDir($curdir) {
		$sql = "UPDATE fm_dirs SET close = 1 WHERE id = :pid AND close = 0 LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":pid" => $curdir);
		$res->execute($param);
	}
	
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
	
	private function _delFileFromDirReal($pid) {
		$sql = "SELECT id, filename FROM fm_fs WHERE pdirid = :pid";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":pid" => $pid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($data as $part) {
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
		}
	}

	function moveFiles($curdir, $md5) {
		$sql = "UPDATE fm_fs SET pdirid = :dir WHERE `md5` = :md5 LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":dir" => $curdir, ":md5" => $md5);
		$res->execute($param);
	}
	
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
	
	function issetFile($file, $curdir) {
		$sql = "UPDATE fm_fs SET `close` = 1 WHERE `filename` = :filename AND pdirid = :pdirid";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":filename" => $file, ":pdirid" => $curdir);
		$res->execute($param);
	}
	
	function getFileNameFromID($fid) {
		$sql = "SELECT filename FROM fm_fs WHERE id = :fid";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $fid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $data[0]["filename"];
	}
	
	function getFilePdiridFromID($fid) {
		$sql = "SELECT pdirid FROM fm_fs WHERE id = :fid";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $fid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		return $data[0]["pdirid"];
	}
	
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
	
	function addFileText($fid, $text) {
		$sql = "INSERT INTO fm_text SET fid = :fid, uid = :uid, `text` = :text";
		 
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $fid, ":uid" => $this->registry["ui"]["id"], ":text" => htmlspecialchars(nl2br($text)));
		$res->execute($param);
	}
	
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
	
	function getGroups() {
		return $this->_groups;
	}
	
	function getUsers() {
		return $this->_users;
	}
	

	
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
	
	function addFileRight($fid, $json) {
		$sql = "UPDATE fm_fs_chmod SET `right` = :json WHERE fid = :fid LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $fid, ":json" => $json);
		$res->execute($param);
	}
	
	function addDirRight($did, $json) {
		$sql = "UPDATE fm_dirs_chmod SET `right` = :json WHERE did = :did LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":did" => $did, ":json" => $json);
		$res->execute($param);
	}

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
	
	function getDesc() {
		return $this->_desc;
	}
	
	function addShare($md5, $fname) {
		$sql = "INSERT INTO fm_share (`md5`, `desc`) VALUES (:md5, :desc)";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":md5" => $md5, ":desc" => $fname);
		$res->execute($param);
	}
	
	function delShare($md5) {
		$sql = "DELETE FROM `fm_share` WHERE `md5` = :md5 LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":md5" => $md5);
		$res->execute($param);
	}
	// END FM AJAX
	
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
	
	function restoreFile($filename, $pid) {
		$sql = "UPDATE fm_fs SET `close` = '0' WHERE filename = :filename AND pdirid = :pdirid ORDER BY id DESC LIMIT 1";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":filename" => $filename, ":pdirid" => $pid);
		$res->execute($param);
	}
	
	function restoreDir($id) {
		$sql = "UPDATE fm_dirs SET `close` = '0' WHERE id = :id LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
	}
	
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
				$this->_tree .= "<span class='folder' title='d_" . $part["id"] . "'><a class='tbranch' href='" . $this->registry["uri"] . "fm/?id=" . rawurlencode($part["id"]) . "'>" . $part["name"] . "</a></span>";
				$this->showTree($id1, $admin);
			}
			$this->_tree .= "</ul>";
		}
	}
	
	function getTree() {
		return $this->_tree;
	}
	
	function dirRename($did, $name) {
		$sql = "UPDATE fm_dirs SET `name` = :name WHERE id = :did LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":did" => $did, ":name" => htmlspecialchars($name));
		$res->execute($param);
	}
	
	function fileRename($curdir, $oldname, $newname) {
		$sql = "UPDATE fm_fs SET filename = :newname WHERE filename = :oldname AND pdirid = :curdir";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":curdir" => $curdir, ":oldname" => htmlspecialchars($oldname), ":newname" => htmlspecialchars($newname));
		$res->execute($param);
	}
	
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
	
	function getUserQuota() {
		$sql = "SELECT quota FROM users WHERE id = :id LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $this->registry["ui"]["id"]);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
	
		return $data[0]["quota"];
	}
	
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
