<?php

/**
 * This file is part of the Workapp project.
*
* (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
*
*/

namespace Otms\System\Model;

use Engine\Model;

/**
 * Resource Model class
 *
 * Файл для работы с файлами-ресурсами
 * Нужен для получения содержимого файлов-ресурсов расположенных в недоступном на хостинге месте
 * (Расположенном в директории, где не разрешён прямой публичный доступ)
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */

class Resource extends Model {
	/**
	 * Получает content файла ресурса для нужного модуля
	 * Если доступен memcache сохраняет content
	 * 
	 * @param string $module - имя модуля
	 * @param string $resfile - имя файла ресурса (css или js)
	 * @return string
	 */
	function getContent($module, $resfile) {
		$this->memcached->set('resource:' . $module . ':' . $resfile);
		if (!$this->memcached->load()) {
			$file = $this->registry["rootPublic"] . "../" . $this->registry['path']['src'] . '/Otms/Modules/' . $this->registry['get']['module'] . '/' . $this->registry['get']['file'];
		
			$content = NULL;
			$file_handle = fopen($file, "r");
			while (!feof($file_handle)) {
				$line = fgets($file_handle);
				$content .= $line;
			}
			fclose($file_handle);
				
			if (file_exists($file)) {
				$this->memcached->save($content);
			}				
		} else {
			$content = $this->memcached->get();
		}
		
		
		return $content;
	}
}