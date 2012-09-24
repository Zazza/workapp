<?php
class Controller_Tt_Page extends Controller_Tt {
	public function index() {
        header("Location: " . $this->registry["uri"] . "tt/index/");
    }
}
?>