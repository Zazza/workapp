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
 * Controller\Filemanager\Download class
 * 
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Download extends Controller {

	public function index() {
		$mfile = new Model\File($this->config);
		
		if (isset($_GET["filename"])) {
			$filename = $_GET["filename"];
			
			$data = $mfile->getMD5FromName($filename);

			if (count($data) > 0) {
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
		
		exit();
	}
}