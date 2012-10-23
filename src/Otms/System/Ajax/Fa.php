<?php

/**
 * This file is part of the Workapp project.
*
* (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
*
*/

namespace Otms\System\Ajax;

use Engine\Ajax;
use Otms\System\Model;

/**
 * Fa (file attach) Ajax class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */

class Fa extends Ajax {
	/**
	 * private переменная для доступа к Model\FASave
	* @var object new Model\FASave()
	*/
    private $file;

    /**
     * Абсолютный путь до директории /upload/
     * 
     * @var string
     */
    private $abspDir = null;
    
    /**
     * Абсолютный путь до директории /upload/_thumb/
    *
    * @var string
    */
    private $abs_thumbDir = null;
	
    /**
     * Конструктор
     * Определение $this->file
     * Определение путей $this->abspDir и $this->abs_thumbDir
     */
    function __construct() {
        parent::__construct();     

        if (isset($_GET['qqfafile'])) {
            $this->file = new Model\FASave();
        } else {
            $this->file = false; 
        }
        
        $this->abspDir = $this->registry['path']['root'] . "/" . $this->registry['path']['upload'];
        $this->abs_thumbDir = $this->registry['path']['root'] . "/" . $this->registry['path']['upload'] . "_thumb/";
    }
    
    /**
     * Проверки перед сохранением
     * 
     * @param string $uploadDirectory - абсолютный путь до директории /upload/
     * @param string $_thumbPath - абсолютный путь до директории /upload/_thumb/
     * 
     * Если ошибок нет выполняет $this->file->save()
     * или
     * @return array array('error')
     */
    function handleUpload($uploadDirectory, $_thumbPath) {		 
        if (!is_writable($uploadDirectory)){
            return array('error' => "Ошибка сервера. Запись в директорию невозможен! " . $this->abspDir);
        }
        
        if (!$this->file){
            return array('error' => 'Нет файлов для загрузки');
        }
        
        $size = $this->file->getSize();

        if ($size == 0) {
            return array('error' => 'Пустой файл или директория');
        }
        
        if ($size > $this->registry["fa"]["sizeLimit"]) {
			if (($this->registry["fa"]["sizeLimit"] / 1024) > 1) {
				$tsize = round($this->registry["fa"]["sizeLimit"] / 1024, 2) . " Кб";
			} else {
				$tsize = round($this->registry["fa"]["sizeLimit"], 2) . " Б";
			};
			
			if (($tsize / 1024) > 1) {
				$tsize = round($tsize / 1024, 2) . " Мб";
			};
			
            return array('error' => 'Файл слишком большой! Установлен лимит на максимальный размер загружаемого файла: ' . $tsize);
        }
        
        $this->file->getName();

        if ($this->file->save()) {
            return array('success'=> true);
        } else {
            return array('error'=> 'Не получается сохранить файл.' .
                'Загрузка отменена, ошибка сервера');
        }
        
    }

    /**
     * Эта функция вызывается при ajax запросе
     * Инициализирует сохранение файла
     * 
     * Возвращает текст ошибки (если есть)
     */
	function save() {
		$sPath = $this->abspDir;
        $_thumbPath = $this->abs_thumbDir;
		
		$result = $this->handleUpload($sPath, $_thumbPath);

		return htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	}
}