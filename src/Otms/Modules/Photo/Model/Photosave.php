<?php

/**
 * This file is part of the Workapp project.
 *
 * Photo Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Photo\Model;

use Engine\Modules\Model;
use PDO;

/**
 * Model\Save class
 *
 * Сохранение загружаемых фйлов
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */

class Photosave extends Model {
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
	 * ID текущей директории
	 * 
	 * @var int
	 */
	private $_did = 0;
	
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
		$sql = "INSERT INTO photo_fs (`md5`, `filename`, `pdirid`, `size`) VALUES (:md5, :filename, :curdir, :size)";
		 
		$res = $this->registry['db']->prepare($sql);
		$param = array(":md5" => $this->md5, ":filename" => $this->filename, ":curdir" => $this->_did, ":size" => $this->getSize());
		$res->execute($param);
			
		$fid = $this->registry['db']->lastInsertId();
			
		$sql = "INSERT INTO photo_fs_chmod (fid, `right`) VALUES (:fid, :json)";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $fid, ":json" => '{"frall":"2"}');
		$res->execute($param);

		$sql = "INSERT INTO photo_fs_history (fid, uid) VALUES (:fid, :uid)";
		 
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $fid, ":uid" => $this->registry["ui"]["id"]);
		$res->execute($param);

		$target = $this->registry['path']['root'] . "/" . $this->registry['path']['photo'] . $this->md5;
		
		move_uploaded_file($_FILES['Filedata']['tmp_name'], $target);

		return true;
	}

	/**
	 * Получить расширение файла
	 *
	 * @return string
	 */
	function getExt() {
		$ext = end(explode('.', strtolower($_FILES['Filedata']['name'])));
		
		$this->filename = $_FILES['Filedata']['name'];
		$this->md5 = md5($this->filename . date("YmdHis")) . "." . $ext;

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
	 * @param int $did - ID текущей директории
	 * @return array
	 *    array('success' => true)
	 *    array('error' => '')
	 */
	function handleUpload($uploadDirectory, $_thumbPath, $did = 0) {
		$this->_did = $did;
		
        if (!is_writable($uploadDirectory)){
            return array('error' => "Server error. Write in a directory is impossible!");
        }

        $ext = $this->getExt();

        if ($this->save()) {
            if ( (strtolower($ext) == "gif") or (strtolower($ext) == "png") or (strtolower($ext) == "jpg") or (strtolower($ext) == "jpeg") ) {
                $thumb = new Photothumb($this->_config);
                $thumb->img_resize($uploadDirectory . $this->md5, $_thumbPath . $this->md5);
            };
            
            return array('success' => true);
        } else {
            return array('error' => 'It is impossible to save the file.' .
                'Cancelled, server error');
        }
        
    }
}
?>
