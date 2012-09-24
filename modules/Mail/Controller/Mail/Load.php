<?php
class Controller_Mail_Load extends Controller_Mail {

	function index() {
		$mid = $_GET["mid"];
		$part = $_GET["part"]-1;
		
		$mail = array();
		$content = null;

		$mailClass = new Model_Mail();

		if ( (isset($_GET["out"])) and ($_GET["out"]) ) {
			$mail = $mailClass->getMailOutFromId($mid);
			$out = 1;
		} else {
			$mail = $mailClass->getMailFromId($mid);
			$out = 0;
		}

		if (count($mail) > 0) {
			if ( (isset($mail[$part]["type"])) and ($mail[$part]["type"] == "text") ) {
				$content = "<pre>" . $mail[$part]["text"] . "</pre>";
			}
			if ( (isset($mail[$part]["type"])) and ($mail[$part]["type"] == "html") ) {
				$content = $mail[$part]["text"];
			}
		
			if ($content != null) {
				echo $this->view->render("mail_mailbody", array("content" => $content, "mid" => $mid, "part" => $part+1, "out" => $out));
			}
			
			exit();
		}
	}
}
?>