<?php
class Controller_Fm_Save extends Modules_Controller {
    private $file;

    private $abspDir = null;
    private $abs_thumbDir = null;

	function index() {
		if (isset($_FILES)) {
			$this->file = new Model_Save($this->config);
			
			$this->abspDir = $this->registry['path']['root'] . "/" . $this->registry['path']['upload'];
			$this->abs_thumbDir = $this->registry['path']['root'] . "/" . $this->registry['path']['upload'] . "_thumb/";
			
			$sPath = $this->abspDir;
	        $_thumbPath = $this->abs_thumbDir;
			
			$result = $this->file->handleUpload($sPath, $_thumbPath);
		}
	}
}
?>
