<?php
class Model_Photo extends Engine_Model {
	public function __construct() {
		parent::__construct($this->registry);
		
		$this->file = new Model_Photofile();
	}
	
	public function setDesc($md5, $desc, $x1, $x2, $y1, $y2, $ws) {
		$size = getimagesize($this->registry["rootPublic"] . $this->registry["path"]["photo"] . $md5);
		
		$scale = $ws / $size[0];
		
		$row = $this->file->getFileParamsFromMd5($md5);
		
		if ($row["id"] > 0) {
			$sql = "INSERT INTO photo_photo_desc (`fid`, `desc`, `x1`, `x2`, `y1`, `y2`) VALUES (:fid, :desc, :x1, :x2, :y1, :y2)";
	
			$res = $this->registry['db']->prepare($sql);
			$param = array("fid" => $row["id"], ":desc" => $desc, ":x1" => round($x1 / $scale), ":x2" => round($x2 / $scale), ":y1" => round($y1 / $scale), ":y2" => round($y2 / $scale));
			$res->execute($param);
		}
	}
	
	public function setTag($md5, $tag) {
		$row = $this->file->getFileParamsFromMd5($md5);
	
		if ($row["id"] > 0) {
			$sql = "INSERT INTO photo_photo_tags (`fid`, `tag`) VALUES (:fid, :tag)";
	
			$res = $this->registry['db']->prepare($sql);
			$param = array("fid" => $row["id"], ":tag" => $tag);
			$res->execute($param);
		}
	}
	
	public function getDesc($md5) {
		$size = getimagesize($this->registry["rootPublic"] . $this->registry["path"]["photo"] . $md5);
		
		$row = $this->file->getFileParamsFromMd5($md5);
	
		$data = array();
		
		if ($row["id"] > 0) {
			$sql = "SELECT `id`, `desc`, `x1`, `x2`, `y1`, `y2` FROM photo_photo_desc WHERE fid = :fid";
	
			$res = $this->registry['db']->prepare($sql);
			$param = array("fid" => $row["id"]);
			$res->execute($param);
			$data = $res->fetchAll(PDO::FETCH_ASSOC);
		}
		
		if (count($data) > 0) {
			for($i=0; $i<count($data); $i++) {
				$data[$i]["width"] = $size[0];
				$data[$i]["height"] = $size[1];
			}

			return $data;
		} else {
			return false;
		}
	}
	
	public function getTags($md5) {
		$row = $this->file->getFileParamsFromMd5($md5);
	
		$data = array();
	
		if ($row["id"] > 0) {
			$sql = "SELECT `id`, `tag` FROM photo_photo_tags WHERE fid = :fid";
	
			$res = $this->registry['db']->prepare($sql);
			$param = array("fid" => $row["id"]);
			$res->execute($param);
			$data = $res->fetchAll(PDO::FETCH_ASSOC);
		}
	
		if (count($data) > 0) {
			return $data;
		} else {
			return false;
		}
	}
	
	public function delTag($id) {
		$sql = "DELETE FROM photo_photo_tags WHERE id = :id";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array("id" => $id);
		$res->execute($param);
	}
	
	public function delDesc($id) {
		$sql = "DELETE FROM photo_photo_desc WHERE id = :id";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array("id" => $id);
		$res->execute($param);
	}
	
	public function getNumNotes($md5) {
		$row = $this->file->getFileParamsFromMd5($md5);
		
		if ($row["id"] > 0) {
			$sql = "SELECT count(`id`) AS count FROM photo_text WHERE fid = :fid";
		
			$res = $this->registry['db']->prepare($sql);
			$param = array("fid" => $row["id"]);
			$res->execute($param);
			$data = $res->fetchAll(PDO::FETCH_ASSOC);
		}
		
		if (count($data) > 0) {
			return $data[0]["count"];
		} else {
			return '0';
		}
	}
	
	public function getFileText($md5) {
		$sql = "SELECT t.uid, t.text AS `text`, t.timestamp AS `timestamp`
		FROM photo_fs AS fs
		LEFT JOIN photo_text AS t ON (t.fid = fs.id)
		WHERE fs.md5 = :md5
		ORDER BY timestamp DESC";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":md5" => $md5);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
	
		return $data;
	}
	
	public function getAllSels() {
		$sql = "SELECT `desc` FROM photo_photo_desc GROUP BY `desc` ORDER BY `desc`";
		
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($data) > 0) {
			return $data;
		} else {
			return false;
		}
	}
	
	public function getAllTags() {
		$sql = "SELECT `tag` FROM photo_photo_tags GROUP BY `tag` ORDER BY `tag`";
		
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($data) > 0) {
			return $data;
		} else {
			return false;
		}
	}
	
	public function favorite($md5) {
		$sql = "SELECT f.id, COUNT(ff.id) AS count
		FROM photo_favorite AS ff
		LEFT JOIN photo_fs AS f ON (f.id = ff.fid)
		WHERE f.md5 = :md5
		LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":md5" => $md5);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		if ($data[0]["count"] == 1) {
			$sql = "DELETE
			FROM photo_favorite
			WHERE fid = :fid
			LIMIT 1";
			
			$res = $this->registry['db']->prepare($sql);
			$param = array(":fid" => $data[0]["id"]);
			$res->execute($param);
			
			return false;
		} else {
			$row = $this->file->getFileParamsFromMd5($md5);
			
			$sql = "INSERT INTO photo_favorite (fid, uid) VALUES (:fid, :uid)";
			
			$res = $this->registry['db']->prepare($sql);
			$param = array(":fid" => $row["id"], ":uid" => $this->registry["ui"]["id"]);
			$res->execute($param);
			
			return true;
		}
	}
}
?>