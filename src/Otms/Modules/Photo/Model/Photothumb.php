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

/**
 * Model\File class
 *
 * Создание preview для изображений
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Photothumb extends Model {
	/**
	 * Настройки модуля
	 *
	 * @var array
	 */
	private $_config;
	
	function __construct($config) {
		parent::__construct($config);
		$this->_config = $config;
	}
	
	/**
	 * Создание превью
	 *
	 * @param string $src - абсолютный путь до MD5 файла
	 * @param string $dest - аабсолютный путь до MD5 превью
	 * @return boolean (TRUE)
	 */
    function img_resize($src, $dest) {
        if (!file_exists($src)) { return false; };
        
        $size = getimagesize($src);
        
        if ($size === false) { return false; };
        
        $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
        
        $icfunc = "imagecreatefrom" . $format;
        if (!function_exists($icfunc)) { return false; };
 
        $x_ratio = $this->_config["photo"]["pre_width"] / $size[0];
        $y_ratio = $this->_config["photo"]["pre_height"] / $size[1];
        
        
        $ratio       = min($x_ratio, $y_ratio);
        $use_x_ratio = ($x_ratio == $ratio);
        
        $new_width   = $use_x_ratio  ? $this->_config["photo"]["pre_width"]  : floor($size[0] * $ratio);
        $new_height  = !$use_x_ratio ? $this->_config["photo"]["pre_height"] : floor($size[1] * $ratio);
        $new_left    = $use_x_ratio  ? 0 : floor(($this->_config["photo"]["pre_width"] - $new_width) / 2);
        $new_top     = !$use_x_ratio ? 0 : floor(($this->_config["photo"]["pre_height"] - $new_height) / 2);
        
        $isrc = $icfunc($src);
        $idest = imagecreatetruecolor($this->_config["photo"]["pre_width"], $this->_config["photo"]["pre_height"]);
        
        imagefill($idest, 0, 0, $this->_config["photo"]["rgb"]);
        imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0,
        $new_width, $new_height, $size[0], $size[1]);
        
        imagejpeg($idest, $dest, $this->_config["photo"]["quality"]);
        
        imagedestroy($isrc);
        imagedestroy($idest);
        
        return true;
    }
}
?>
