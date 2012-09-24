<?php
class Controller_Photo_Save extends Modules_Controller {
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
			
			$this->file = new Model_Photosave($this->config);
			
			$this->abspDir = $this->registry['path']['root'] . "/" . $this->registry['path']['photo'];
			$this->abs_thumbDir = $this->registry['path']['root'] . "/" . $this->registry['path']['photo'] . "_thumb/";
			
			$sPath = $this->abspDir;
	        $_thumbPath = $this->abs_thumbDir;
			
			$result = $this->file->handleUpload($sPath, $_thumbPath, $did);
		}
	}
}
?>
