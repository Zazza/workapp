<?php
class Controller_Fa_Attach extends Controller_Fa {
	
	function index() {
		if (isset($_GET["md5"])) {
			if (!strpos($_GET["md5"], "/")) {
				$sql = "SELECT f.filename AS `filename`, h.uid, r.right AS `right`
				FROM fm_fs AS f
				LEFT JOIN fm_fs AS f1 ON (f1.filename = f.filename)
				LEFT JOIN fm_fs_history AS h ON (h.fid = f.id)
				LEFT JOIN fm_fs_chmod AS r ON (r.fid = f1.id)
				WHERE f.md5 = :md5 AND r.right != 'NULL'
				LIMIT 1";
		        
		        $res = $this->registry['db']->prepare($sql);
				$param = array(":md5" => $_GET["md5"]);
				$res->execute($param);
				$data = $res->fetchAll(PDO::FETCH_ASSOC);
				
				$flag = false;
				if ($this->registry["ui"]["id"] == $data[0]["uid"]) {
					$flag = true;
				}
				if (!$flag) {
					$right = json_decode($data[0]["right"], true);
					foreach($right as $key=>$val) {
						if ($key == "frall") {
							if ($val == "true") {
								$flag = true;
							}
						}
						
						if ($key == "fg" . $this->registry["ui"]["gid"]) {
							if ($val == "true") {
								$flag = true;
							}
						}
						
						if ($key == "user" . $this->registry["ui"]["id"]) {
							if ($val == "true") {
								$flag = true;
							}
						}
					}
				}
				
				if ($flag) {
					$fn = $_GET["md5"];
					
					$file = $this->registry["rootPublic"] . $this->registry["path"]["upload"] . $fn;
					
					if (file_exists($file)) {
						$data[0]["filename"] = str_replace(" ", "_", $data[0]["filename"]);
						
						header ("Content-Type: application/octet-stream");
						header ("Accept-Ranges: bytes");
						header ("Content-Length: " . filesize($file));
						header ("Content-Disposition: attachment; filename=" . $data[0]["filename"]);
		
						readfile($file);
					}
				}
			}
		}
		
		if (isset($_GET["filename"])) {
			$filename = $_GET["filename"];
			if (!strpos($filename, "/")) {
				
				$fm = & $_SESSION["fm"];
				if (isset($fm["dir"])) {
		        	$curdir = $fm["dir"];
		        } else {
					$curdir = 0;
				}

		        $sql = "SELECT f.md5 AS `md5`, h.uid, r.right AS `right`
				FROM fm_fs AS f
				LEFT JOIN fm_fs AS f1 ON (f1.filename = f.filename)
				LEFT JOIN fm_fs_history AS h ON (h.fid = f.id)
				LEFT JOIN fm_fs_chmod AS r ON (r.fid = f1.id)
				WHERE f.filename = :filename AND f.pdirid = :pdirid AND f.close = 0
				LIMIT 1";
		        
		        $res = $this->registry['db']->prepare($sql);
				$param = array(":filename" => $filename, ":pdirid" => $curdir);
				$res->execute($param);
				$data = $res->fetchAll(PDO::FETCH_ASSOC);
				
				$flag = false;
				if ($this->registry["ui"]["id"] == $data[0]["uid"]) {
					$flag = true;
				}
				if (!$flag) {
					$right = json_decode($data[0]["right"], true);
					foreach($right as $key=>$val) {
						if ($key == "frall") {
							if ($val == "true") {
								$flag = true;
							}
						}
						
						if ($key == "fg" . $this->registry["ui"]["gid"]) {
							if ($val == "true") {
								$flag = true;
							}
						}
						
						if ($key == "user" . $this->registry["ui"]["id"]) {
							if ($val == "true") {
								$flag = true;
							}
						}
					}
				}
				
				if ($flag) {				
					$fn = $data[0]["md5"];
	
					$file = $this->registry["rootPublic"] . $this->registry["path"]["upload"] . $fn;
	
					if (file_exists($file)) {
						$filename = str_replace(" ", "_", $filename);
						
						header ("Content-Type: application/octet-stream");
						header ("Accept-Ranges: bytes");
						header ("Content-Length: " . filesize($file));
						header ("Content-Disposition: attachment; filename=" . $filename);
	
						readfile($file);
					}
				}
			}
		}
	}
}
?>