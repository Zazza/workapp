<?php

/**
 * This file is part of the Workapp project.
 *
 * Filemanager Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Filemanager\Controller\Filemanager;

use Engine\Modules\Controller;
use Otms\Modules\Filemanager\Model;

/**
 * Controller\Filemanager\Save class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Save extends Controller {
    private $file;

    private $abspDir = null;
    private $abs_thumbDir = null;

	function index() {
		if (isset($_FILES)) {
			$this->file = new Model\Save($this->config);
			
			$this->abspDir = $this->registry['path']['root'] . "/" . $this->registry['path']['upload'];
			$this->abs_thumbDir = $this->registry['path']['root'] . "/" . $this->registry['path']['upload'] . "_thumb/";
			
			$sPath = $this->abspDir;
	        $_thumbPath = $this->abs_thumbDir;
			
			$result = $this->file->handleUpload($sPath, $_thumbPath);
		}
	}
}