<?php
class Controller_Mail_Attach extends Controller_Mail {

	function index() {
		$mailClass = new Model_Mail();

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
					$this->view->setTitle("Файл не найден");
					$this->view->setMainContent("<p style='text-align: center'>Файл не найден</p>");

					$this->view->refresh(array("timer" => "1", "url" => "mail/"));

					$this->view->showPage();
				}
			}
		}
	}
}
?>