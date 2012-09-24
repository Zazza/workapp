<?php
class Model_Save extends Modules_Model {
	private $_config;
	private $filename = null;
	public $md5 = null;
	
	function __construct($config) {
		parent::__construct($config);
		$this->_config = $config;
	}
	

	function save() {
		$fm = & $_SESSION["fm"];
    	if (!isset($fm["dir"])) {
    		$curdir = 0;
    	} else {
    		$curdir = $fm["dir"];
    	}

		$sql = "SELECT COUNT(id) AS count FROM fm_fs WHERE `filename` = :filename AND pdirid = :pdirid LIMIT 1";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":filename" => $this->filename, ":pdirid" => $curdir);
		$res->execute($param);
		$isset = $res->fetchAll(PDO::FETCH_ASSOC);

		$sql = "INSERT INTO fm_fs (`md5`, `filename`, `pdirid`, `size`) VALUES (:md5, :filename, :curdir, :size)";
		 
		$res = $this->registry['db']->prepare($sql);
		$param = array(":md5" => $this->md5, ":filename" => $this->filename, ":curdir" => $curdir, ":size" => $this->getSize());
		$res->execute($param);
			
		$fid = $this->registry['db']->lastInsertId();
			
		if ($isset[0]["count"] == 0) {
			$sql = "INSERT INTO fm_fs_chmod (fid, `right`) VALUES (:fid, :json)";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":fid" => $fid, ":json" => '{"frall":"2"}');
			$res->execute($param);
		}

		$sql = "INSERT INTO fm_fs_history (fid, uid) VALUES (:fid, :uid)";
		 
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $fid, ":uid" => $this->registry["ui"]["id"]);
		$res->execute($param);

		$target = $this->registry['path']['root'] . "/" . $this->registry['path']['upload'] . $this->md5;
		
		move_uploaded_file($_FILES['Filedata']['tmp_name'], $target);

		return true;
	}

	function getExt() {
		$this->filename = $_FILES['Filedata']['name'];
		$this->md5 = md5($this->filename . date("YmdHis"));

		//$ext = end(explode('.', strtolower($_FILES['Filedata']['name'])));
		$ext = mb_substr($this->filename, mb_strrpos($this->filename, ".")+1);
		return $ext;
	}

	function getSize() {
		return $_FILES['Filedata']['size'];
	}
	
	function handleUpload($uploadDirectory, $_thumbPath, $replaceOldFile = FALSE) {		 
        if (!is_writable($uploadDirectory)){
            return array('error' => "Server error. Write in a directory is impossible!");
        }

        $ext = $this->getExt();

        if ($this->save()) {
            if ( (strtolower($ext) == "gif") or (strtolower($ext) == "png") or (strtolower($ext) == "jpg") or (strtolower($ext) == "jpeg") ) {
                $thumb = new Model_Thumb($this->config);
                $thumb->img_resize($uploadDirectory . $this->md5, $_thumbPath . $this->md5, 150, 120);
            };
            
            return array('success' => true);
        } else {
            return array('error' => 'It is impossible to save the file.' .
                'Cancelled, server error');
        }
        
    }
}
?>
