<?php

/**
 * This file is part of the Workapp project.
 *
 * Task Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Task\Controller\Task;

use Otms\Modules\Task\Controller\Task;

/**
 * Controller\Task\Attach class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Attach extends Task {

	function index() {
		if (isset($_GET["filename"])) {
			$filename = $_GET["filename"];
			if (!strpos($filename, "/")) {
					
				if (isset($_GET["tid"])) {
					if ( (isset($_GET["remote"])) and ($_GET["remote"] == 1) ) {
						$fn = $this->registry["task"]->getMailFile($_GET["tid"], $filename);
						$file = $this->registry["rootPublic"] . $this->registry["path"]["attaches"] . $fn;
					} else {
						$fn = $this->registry["task"]->getFile($_GET["tid"], $filename);
						$file = $this->registry["rootPublic"] . $this->registry["path"]["upload"] . $fn;
					}
				} elseif (isset($_GET["did"])) {
					$fn = $this->registry["task"]->getDraftFile($_GET["did"], $filename);
					$file = $this->registry["rootPublic"] . $this->registry["path"]["upload"] . $fn;
				} elseif (isset($_GET["tdid"])) {
					if ( (isset($_GET["remote"])) and ($_GET["remote"] == 1) ) {
						$fn = $this->registry["task"]->getCommentMailFile($_GET["tdid"], $filename);
						$file = $this->registry["rootPublic"] . $this->registry["path"]["attaches"] . $fn;
					} else {
						$fn = $this->registry["task"]->getCommentFile($_GET["tdid"], $filename);
						$file = $this->registry["rootPublic"] . $this->registry["path"]["upload"] . $fn;
					}
				}

				if (file_exists($file)) {
					$filename = str_replace(" ", "_", $filename);
					
					header ("Content-Type: application/octet-stream");
					header ("Accept-Ranges: bytes");
					header ("Content-Length: " . filesize($file));
					header ("Content-Disposition: attachment; filename=" . $filename);

					readfile($file);
				} else {
					$this->view->setTitle("Файл не найден");
					$this->view->setMainContent("<p style='text-align: center'>Файл не найден</p>");

					$this->view->refresh(array("timer" => "1", "url" => "task/"));

					$this->view->showPage();
				}
			}
		}
	}
}
?>