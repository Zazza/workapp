<?php

/**
 * This file is part of the Workapp project.
 *
 * Mail Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Mail\Controller\Mail;

use Otms\Modules\Mail\Controller\Mail;
use Otms\Modules\Mail\Model;

/**
 * Controller\Mail\Attach class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Attach extends Mail {

	function index() {
		$mailClass = new Model\Mail();

		if (isset($_GET["filename"])) {
			$filename = $_GET["filename"];
			if (!strpos($filename, "/")) {
					
				$mid = $_GET["mid"];

				if ( (isset($_GET["type"])) and ($_GET["type"] == "out") ) {
					$fn = $mailClass->getFileOut($mid, $filename);
					$file = $this->registry["rootPublic"] . $this->registry["path"]["upload"] . $fn;
				} else {
					$fn = $mailClass->getFile($mid, $filename);
					$file = $this->registry["rootPublic"] . $this->registry["path"]["attaches"] . $fn;
				}

				if (file_exists($file)) {
					$filename = str_replace(" ", "_", $filename);
					
					header ("Content-Type: application/octet-stream");
					header ("Accept-Ranges: bytes");
					header ("Content-Length: " . filesize($file));
					header ("Content-Disposition: attachment; filename=" . $filename);

					readfile($file);
				} else {
					$this->view->setTitle("File not found");
					$this->view->setMainContent("<p style='text-align: center'>File not found</p>");

					$this->view->refresh(array("timer" => "1", "url" => "mail/"));

					$this->view->showPage();
				}
			}
		}
	}
}
?>