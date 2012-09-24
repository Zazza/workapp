<?php
class Controller_Ajax_Profile extends Engine_Ajax {
	function delAva($profile) {
		$ui = new Model_Ui();
		$ui->delAva();
	}
}
?>