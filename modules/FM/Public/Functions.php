<?php
class FM_Public_Functions extends Modules_Functions {
	function renderFM() {
		return $this->view->render("content", array());
	}
	
	function createdir($param) {
		$mfile = new Model_File($this->config);
	
		$mfile->createDir($param[0], $param[1]);
		$did = $mfile->getDid();
		$mfile->addDirRight($did, '{"frall":"2"}');
	
		return $did;
	}
}
?>