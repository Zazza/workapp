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
 * Model\Save class
 *
 * Сохранение загружаемых фйлов
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Save extends Model {
	/**
	 * Настройки модуля
	 *
	 * @var array
	 */
	private $_config;
	
	/**
	 * Реальное имя файла
	 * @var string
	 */
	private $filename = NULL;
	
	/**
	 * MD5 имя файла в ФС
	 * 
	 * @var string
	 */
	public $md5 = NULL;
	
	function __construct($config) {
		parent::__construct($config);
		$this->_config = $config;
	}
	

	/**
	 * Сохранение файла в БД и ФС
	 * 
	 * @return boolean (TRUE)
	 */
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

	/**
	 * Получить расширение файла
	 * 
	 * @return string
	 */
	function getExt() {
		$this->filename = $_FILES['Filedata']['name'];
		$this->md5 = md5($this->filename . date("YmdHis"));

		$ext = mb_substr($this->filename, mb_strrpos($this->filename, ".")+1);
		return $ext;
	}

	/**
	 * Получить размер файла
	 * 
	 * @return int
	 */
	function getSize() {
		return $_FILES['Filedata']['size'];
	}
	
	/**
	 * Инициализация сохранения, вызывается из SWF
	 * 
	 * @param string $uploadDirectory
	 * @param string $_thumbPath
	 * @return array
	 *    array('success' => true)
	 *    array('error' => '')
	 */
	function handleUpload($uploadDirectory, $_thumbPath) {		 
        if (!is_writable($uploadDirectory)){
            return array('error' => "Server error. Write in a directory is impossible!");
        }

        $ext = $this->getExt();

        if ($this->save()) {
            if ( (strtolower($ext) == "gif") or (strtolower($ext) == "png") or (strtolower($ext) == "jpg") or (strtolower($ext) == "jpeg") ) {
                $thumb = new Thumb($this->config);
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
