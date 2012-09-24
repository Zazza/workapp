<?php
class Model_Photofile extends Engine_Model {
	private $_groups = null;
	private $_users = null;
	private $_desc = null;	
	private $_rec = array();
	private $_tree = null;
	private $_curdir = 0;
	
	function __construct() {
		parent::__construct($this->registry);

		if (isset($this->registry["post"]["did"])) {
			$this->_curdir = $this->registry["post"]["did"];
		}
	}
	
	// ATTACH
	public function attachFromMD5($md5) {
		$sql = "SELECT f.filename AS `filename`, h.uid, r.right AS `right`
						FROM photo_fs AS f
						LEFT JOIN photo_fs AS f1 ON (f1.filename = f.filename)
						LEFT JOIN photo_fs_history AS h ON (h.fid = f.id)
						LEFT JOIN photo_fs_chmod AS r ON (r.fid = f1.id)
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
		FROM photo_fs AS f
		LEFT JOIN photo_fs AS f1 ON (f1.filename = f.filename)
		LEFT JOIN photo_fs_history AS h ON (h.fid = f.id)
		LEFT JOIN photo_fs_chmod AS r ON (r.fid = f1.id)
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
		        FROM photo_dirs AS fd
		        LEFT JOIN photo_dirs_chmod AS fdc ON (fdc.did = fd.id)
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
	
	function getFileParamsFromMd5($md5) {
		$sql = "SELECT f.id, f.filename, f.size, h.timestamp, f.pdirid, h.uid AS uid
	        FROM photo_fs AS f
	        LEFT JOIN photo_fs_history AS h ON (h.fid = f.id)
	        LEFT JOIN photo_fs AS f1 ON (f.filename = f1.filename)
	        WHERE f.md5 = :md5
	        LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":md5" => $md5);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
	
		$right[0]["count"] = 0;
	
		$sql = "SELECT COUNT(id) AS count FROM photo_fs_chmod WHERE fid = :fid LIMIT 1";
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $data[0]["id"]);
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
	        FROM photo_fs AS f
	        LEFT JOIN photo_fs_history AS h ON (h.fid = f.id)
	        WHERE f.filename = :filename AND f.pdirid = :pdirid
	        ORDER BY f.id DESC
	        LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":filename" => $filename, ":pdirid" => $curdir);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
	
		$right[0]["count"] = 0;
	
		$sql = "SELECT COUNT(id) AS count FROM photo_fs_chmod WHERE fid = :fid LIMIT 1";
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $data[0]["id"]);
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
		    		FROM photo_dirs AS d
		    		LEFT JOIN photo_dirs_chmod AS r ON (r.did = d.id)
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
	    		FROM photo_dirs AS d
	    		LEFT JOIN photo_dirs_chmod AS r ON (r.did = d.id)
	    		WHERE d.pid = :pid";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":pid" => $curdir);
		$res->execute($param);
		$dirs = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $dirs;
	}
	
	function getDirs($curdir) {
		$sql = "SELECT d.id, d.uid, d.name AS `name`, r.right AS `right`, d.close AS `close`
	    		FROM photo_dirs AS d
	    		LEFT JOIN photo_dirs_chmod AS r ON (r.did = d.id)
	    		WHERE d.pid = :pid AND d.close = 0";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":pid" => $curdir);
		$res->execute($param);
		$dirs = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $dirs;
	}
	
	function getAdminFiles($curdir) {
		$sql = "SELECT DISTINCT(f.id), f.md5, f.filename AS `name`, f.size, h.uid, h.timestamp, r.right AS `right`, f.close AS `close`
				FROM photo_fs AS f

				LEFT JOIN photo_fs_history AS h ON (h.fid = f.id)
				LEFT JOIN photo_fs_chmod AS r ON (r.fid = f.id)
					WHERE f.pdirid = :pid AND r.right != 'NULL'
				ORDER BY f.filename, f.id DESC";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":pid" => $curdir);
		$res->execute($param);
		$files = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $files;
	}
	
	function getAdminFilesSort($curdir) {
		$sphoto = & $_SESSION["photo"];
	
		$sql_inc = ""; $left_inc = "";
		$i = 0;
		if ( (isset($sphoto["tag"])) and (count($sphoto["tag"]) > 0) ) {
				
			foreach($sphoto["tag"] as $part) {
				$sql_inc .= " AND fpt" . $i . ".tag = '" . $part . "' ";
				$left_inc .= " LEFT JOIN photo_photo_tags AS fpt" . $i . " ON (fpt" . $i . ".fid = f.id) ";
					
				$i++;
			}
		}
	
		$i = 0;
		if ( (isset($sphoto["sel"])) and (count($sphoto["sel"]) > 0) ) {
			foreach($sphoto["sel"] as $part) {
				$sql_inc .= " AND fpd" . $i . ".desc = '" . $part . "' ";
				$left_inc .= " LEFT JOIN photo_photo_desc AS fpd" . $i . " ON (fpd" . $i . ".fid = f.id) ";
	
				$i++;
			}
		}
	
		$sql = "SELECT DISTINCT(f.id), f.md5, f.filename AS `name`, f.size, h.uid, h.timestamp, r.right AS `right`, f.close AS `close`
		FROM photo_fs AS f

		LEFT JOIN photo_fs_history AS h ON (h.fid = f.id)
		LEFT JOIN photo_fs_chmod AS r ON (r.fid = f.id)
		" . $left_inc . "
		WHERE r.right != 'NULL' " . $sql_inc . "
		ORDER BY f.filename, f.id DESC";
	
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$files = $res->fetchAll(PDO::FETCH_ASSOC);
	
		return $files;
	}
	
	function getAdminFilesFavorite($curdir) {
		$sql = "SELECT DISTINCT(f.id), f.md5, f.filename AS `name`, f.size, h.uid, h.timestamp, r.right AS `right`, f.close AS `close`
		FROM photo_fs AS f

		LEFT JOIN photo_fs_history AS h ON (h.fid = f.id)
		LEFT JOIN photo_fs_chmod AS r ON (r.fid = f.id)
		LEFT JOIN photo_favorite AS ff ON (ff.fid = f.id)
		WHERE r.right != 'NULL' AND ff.id != 'NULL'
		ORDER BY f.filename, f.id DESC";
	
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$files = $res->fetchAll(PDO::FETCH_ASSOC);
	
		return $files;
	}
	
	function getFiles($curdir) {
		$sql = "SELECT DISTINCT(f.id), f.md5, f.filename AS `name`, f.size, h.uid, h.timestamp, r.right AS `right`, f.close AS `close`
				FROM photo_fs AS f

				LEFT JOIN photo_fs_history AS h ON (h.fid = f.id)
				LEFT JOIN photo_fs_chmod AS r ON (r.fid = f.id)
				WHERE f.pdirid = :pid AND f.close = 0 AND r.right != 'NULL'
				ORDER BY f.filename";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":pid" => $curdir);
		$res->execute($param);
		$files = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $files;	
	}
	
	function getFilesSort($curdir) {
		$sphoto = & $_SESSION["photo"];
	
		$sql_inc = ""; $left_inc = "";
		$i = 0;
		if ( (isset($sphoto["tag"])) and (count($sphoto["tag"]) > 0) ) {
				
			foreach($sphoto["tag"] as $part) {
				$sql_inc .= " AND fpt" . $i . ".tag = '" . $part . "' ";
				$left_inc .= " LEFT JOIN photo_photo_tags AS fpt" . $i . " ON (fpt" . $i . ".fid = f.id) ";
					
				$i++;
			}
		}
	
		$i = 0;
		if ( (isset($sphoto["sel"])) and (count($sphoto["sel"]) > 0) ) {
			foreach($sphoto["sel"] as $part) {
				$sql_inc .= " AND fpd" . $i . ".desc = '" . $part . "' ";
				$left_inc .= " LEFT JOIN photo_photo_desc AS fpd" . $i . " ON (fpd" . $i . ".fid = f.id) ";
	
				$i++;
			}
		}
	
		$sql = "SELECT DISTINCT(f.id), f.md5, f.filename AS `name`, f.size, h.uid, h.timestamp, r.right AS `right`, f.close AS `close`
		FROM photo_fs AS f

		LEFT JOIN photo_fs_history AS h ON (h.fid = f.id)
		LEFT JOIN photo_fs_chmod AS r ON (r.fid = f.id)
		" . $left_inc . "
		WHERE f.close = 0 AND r.right != 'NULL' " . $sql_inc . "
		ORDER BY f.filename";
	
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$files = $res->fetchAll(PDO::FETCH_ASSOC);
	
		return $files;
	}
	
	function getFilesFavorite($curdir) {
		$sql = "SELECT DISTINCT(f.id), f.md5, f.filename AS `name`, f.size, h.uid, h.timestamp, r.right AS `right`, f.close AS `close`
		FROM photo_fs AS f

		LEFT JOIN photo_fs_history AS h ON (h.fid = f.id)
		LEFT JOIN photo_fs_chmod AS r ON (r.fid = f.id)
		LEFT JOIN photo_favorite AS ff ON (ff.fid = f.id)
		WHERE f.close = 0 AND r.right != 'NULL' AND ff.id != 'NULL'
		ORDER BY f.filename";
	
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$files = $res->fetchAll(PDO::FETCH_ASSOC);
	
		return $files;
	}
	
	function delfile($md5) {
		$sql = "UPDATE photo_fs SET `close` = '1' WHERE `md5` = :md5 LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":md5" => $md5);
		$res->execute($param);
	}
	
	function delfilereal($md5) {
		$curdir = $this->_curdir;
		
		$data = $this->getFileParamsFromMd5($md5);
		
		if (file_exists($this->registry["rootPublic"] . $this->registry["path"]["photo"] . $md5)) {
			unlink($this->registry["rootPublic"] . $this->registry["path"]["photo"] . $md5);
			unlink($this->registry["rootPublic"] . $this->registry["path"]["photo"] . "_thumb/" . $md5);
		}

		$sql = "DELETE FROM photo_fs WHERE `md5` = :md5 LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":md5" => $md5);
		$res->execute($param);
		
		$sql = "DELETE FROM photo_fs_chmod WHERE `fid` = :fid LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $data["id"]);
		$res->execute($param);
		
		$sql = "DELETE FROM photo_fs_history WHERE `fid` = :fid LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $data["id"]);
		$res->execute($param);
		
		$sql = "DELETE FROM photo_favorite WHERE `fid` = :fid LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $data["id"]);
		$res->execute($param);
		
		$sql = "DELETE FROM photo_photo_desc WHERE `fid` = :fid LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $data["id"]);
		$res->execute($param);
		
		$sql = "DELETE FROM photo_photo_tags WHERE `fid` = :fid LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $data["id"]);
		$res->execute($param);
	}
	
	function getTotalSize() {
		$curdir = $this->_curdir;
	
		$totalSize = 0;
	
		$sql = "SELECT f.filename, f.size, h.timestamp
			FROM photo_fs AS f
			LEFT JOIN photo_fs_history AS h ON (h.fid = f.id)
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
		$sql = "SELECT `pid` FROM photo_dirs WHERE `id` = :id LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $dirid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $data;
	}
	
	function getNameFromDir($dirid) {
		$sql = "SELECT `name` FROM photo_dirs WHERE `id` = :id LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $dirid);
		$res->execute($param);
		$dirname = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $dirname;
	}
	
	function getDirIdFromNameAndPid($dir, $pid) {
		$sql = "SELECT `id` FROM photo_dirs WHERE `name` = :name AND pid = :pid LIMIT 1";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":name" => $dir, "pid" => $pid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
			
		return $data;
	}
	
	function createDir($curdir, $dirName) {
		$sql = "SELECT count(id) AS count FROM photo_dirs WHERE `name` = :name AND `pid` = :pid LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":pid" => $curdir, ":name" => $dirName);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		if ($data[0]["count"] == 0) {
			$sql = "INSERT INTO photo_dirs (uid, `pid`, `name`) VALUES (:uid, :pid, :name)";
			 
			$res = $this->registry['db']->prepare($sql);
			$param = array(":uid" => $this->registry["ui"]["id"], ":pid" => $curdir, ":name" => htmlspecialchars($dirName));
			$res->execute($param);
				
			$did = $this->registry['db']->lastInsertId();
				
			$sql = "INSERT INTO photo_dirs_chmod (did, `right`) VALUES (:did, :json)";
			 
			$res = $this->registry['db']->prepare($sql);
			$param = array(":did" => $did, ":json" => '{"frall":"2"}');
			$res->execute($param);
			
			return true;
		} else {
			return false;
		}
	}
	
	function rmDir($curdir) {
		$sql = "UPDATE photo_dirs SET close = 1 WHERE id = :pid AND close = 0 LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":pid" => $curdir);
		$res->execute($param);
	}
	
	private function _recursDir($id) {
		$sql = "SELECT id FROM photo_dirs WHERE `pid` = :pid";
		
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
			$sql = "DELETE FROM photo_dirs WHERE id = :id";
			
			$res = $this->registry['db']->prepare($sql);
			$param = array(":id" => $part);
			$res->execute($param);
			
			$sql = "DELETE FROM photo_dirs_chmod WHERE did = :id";
			
			$res = $this->registry['db']->prepare($sql);
			$param = array(":id" => $part["id"]);
			$res->execute($param);
			
			$this->_delFileFromDirReal($part);
		}
	}
	
	private function _delFileFromDirReal($pid) {
		$sql = "SELECT id, filename FROM photo_fs WHERE pdirid = :pid";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":pid" => $pid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($data as $part) {
			$sql = "DELETE FROM photo_fs WHERE `filename` = :filename AND pdirid = :pdirid";
			
			$res = $this->registry['db']->prepare($sql);
			$param = array(":filename" => $part["filename"], ":pdirid" => $pid);
			$res->execute($param);
				
			$sql = "DELETE FROM photo_fs_chmod WHERE `fid` = :fid";
				
			$res = $this->registry['db']->prepare($sql);
			$param = array(":fid" => $part["id"]);
			$res->execute($param);
				
			$sql = "DELETE FROM photo_fs_history WHERE `fid` = :fid";
				
			$res = $this->registry['db']->prepare($sql);
			$param = array(":fid" => $part["id"]);
			$res->execute($param);
		}
	}
	
	function moveFiles($curdir, $md5, $buffer) {
		$sql = "UPDATE photo_fs SET pdirid = :dir WHERE `md5` = :md5 AND pdirid = :curdir AND close = 0";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":dir" => $curdir, ":md5" => $md5, ":curdir" => $buffer);
		$res->execute($param);
	}
	
	private function _showRecursDid($current, $id) {
		$sql = "SELECT pid FROM photo_dirs WHERE id = :id LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		if ($data[0]["pid"] == 0) {
			return true;
		} else if ($data[0]["pid"] == $current) {
			return false;
		} else {
			return $this->_showRecursDid($current, $data[0]["pid"]);
		}
	}
	
	function moveDir($curdir, $did) {
		if ($curdir != $did) {
			if ($this->_showRecursDid($did, $curdir)) {
				$sql = "UPDATE photo_dirs SET pid = :pid WHERE id = :id AND close = 0";
				
				$res = $this->registry['db']->prepare($sql);
				$param = array(":pid" => $curdir, ":id" => $did);
				$res->execute($param);
			}
		}
	}
	
	function getFileHistory($md5, $curdir) {
		$sql = "SELECT h.timestamp AS `timestamp`, h.uid, fs1.md5
	        FROM photo_fs AS fs
	        LEFT JOIN photo_fs AS fs1 ON (fs.filename = fs1.filename)
	        LEFT JOIN photo_fs_history AS h ON (h.fid = fs1.id)
	        WHERE fs.md5 = :md5 AND fs.pdirid = :pdirid AND fs1.pdirid = :pdirid
	        ORDER BY h.timestamp DESC";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":md5" => $md5, ":pdirid" => $curdir);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
	
		return $data;
	}

	function addFileText($fid, $text) {
		$sql = "INSERT INTO photo_text SET fid = :fid, uid = :uid, `text` = :text";
		 
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $fid, ":uid" => $this->registry["ui"]["id"], ":text" => nl2br(htmlspecialchars($text)));
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
	        FROM photo_fs AS fs
	        LEFT JOIN photo_fs_chmod AS r ON (r.fid = fs.id)
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
	        FROM photo_dirs_chmod
	        WHERE did = :did
	        LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":did" => $did);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
	
		return $data;
	}
	
	function addFileRight($fid, $json) {
		$sql = "UPDATE photo_fs_chmod SET `right` = :json WHERE fid = :fid LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $fid, ":json" => $json);
		$res->execute($param);
	}
	
	function addDirRight($did, $json) {
		$sql = "UPDATE photo_dirs_chmod SET `right` = :json WHERE did = :did LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":did" => $did, ":json" => $json);
		$res->execute($param);
	}

	function getCurDirName($curdir) {
		$sql = "SELECT `name`
	    	FROM photo_dirs
	    	WHERE id = :pdirid AND close = 0
	    	LIMIT 1";
		 
		$res = $this->registry['db']->prepare($sql);
		$param = array(":pdirid" => $curdir);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		 
		return $data;
	}

	function getDesc() {
		return $this->_desc;
	}
	// END FM AJAX
	
	function restoreFile($md5) {
		$sql = "UPDATE photo_fs SET `close` = '0' WHERE md5 = :md5 LIMIT 1";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":md5" => $md5);
		$res->execute($param);
	}
	
	function restoreDir($id) {
		$sql = "UPDATE photo_dirs SET `close` = '0' WHERE id = :id LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
	}
	
	function showTree($pid, $admin = false) {
		if ($admin) {
			$sql = "SELECT id,name,pid FROM photo_dirs WHERE pid = :pid ORDER BY name";
		} else {
			$sql = "SELECT id,name,pid FROM photo_dirs WHERE pid = :pid AND close = 0 ORDER BY name";
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
				$this->_tree .= "<span class='folder' title='d_" . $part["id"] . "'><a class='tbranch' href='" . $this->registry["uri"] . "photo/?id=" . rawurlencode($part["id"]) . "'>" . $part["name"] . "</a></span>";
				$this->showTree($id1, $admin);
			}
			$this->_tree .= "</ul>";
		}
	}
	
	function getTree() {
		return $this->_tree;
	}
	
	function dirRename($did, $name) {
		$sql = "UPDATE photo_dirs SET `name` = :name WHERE id = :did LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":did" => $did, ":name" => htmlspecialchars($name));
		$res->execute($param);
	}
	
	function fileRename($md5, $newname) {
		$sql = "UPDATE photo_fs SET filename = :newname WHERE md5 = :md5 LIMIT 1";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":md5" => $md5, ":newname" => htmlspecialchars($newname));
		$res->execute($param);
	}
}
?>
