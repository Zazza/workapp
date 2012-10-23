<?php

/**
 * This file is part of the Workapp project.
 *
 * Photo Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Photo\Controller\Photo;

use Engine\Modules\Controller;
use Otms\Modules\Photo\Model;

/**
 * Controller\Photo\Save class
 * 
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Save extends Controller {
    private $file;

    private $abspDir = null;
    private $abs_thumbDir = null;

	function index() {
		if (isset($_FILES)) {
			if (isset($this->registry["get"]["id"])) {
				$did = $this->registry["get"]["id"];
			} else {
				$did = 0;
			}
			
			$this->file = new Model\Photosave($this->config);
			
			$this->abspDir = $this->registry['path']['root'] . "/" . $this->registry['path']['photo'];
			$this->abs_thumbDir = $this->registry['path']['root'] . "/" . $this->registry['path']['photo'] . "_thumb/";
			
			$sPath = $this->abspDir;
	        $_thumbPath = $this->abs_thumbDir;
			
			$result = $this->file->handleUpload($sPath, $_thumbPath, $did);
		}
	}
}
?>
