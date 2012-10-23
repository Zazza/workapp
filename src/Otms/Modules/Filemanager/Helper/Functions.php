<?php

/**
 * This file is part of the Workapp project.
 *
 * Filemanager Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Filemanager\Helper;

use Engine\Modules;
use Otms\Modules\Filemanager\Model\File;

/**
 * Filemanager Helper class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Functions extends Modules\Functions {
	/**
	 * Создать директорию
	 * 
	 * @param array
	 *    @param[0] - ID текущей директории
	 *    @param[1] - имя новой директории
	 * @return int $did - ID созданной директории
	 */
	function createdir($param) {
		$mfile = new File($this->config);
	
		$mfile->createDir($param[0], $param[1]);
		$did = $mfile->getDid();
		$mfile->addDirRight($did, '{"frall":"2"}');
	
		return $did;
	}
}
?>