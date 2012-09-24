<?php
class Controller_Index extends Engine_Controller {

	public function index() {
		header("Location: " . $this->registry["uri"] . "tt/");
	}
}
?>