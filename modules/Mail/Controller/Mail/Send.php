<?php
class Controller_Mail_Send extends Controller_Mail {

	function index() {
		$mailClass = new Model_Mail();
		
		$this->view->setTitle("Отправленные письма");
		
		$this->view->setLeftContent($this->view->render("left_mail", array("folders" => $this->folders, "enableCheck" => $this->enableCheck)));
		
		if (isset($_GET["oid"])) {
			$obj = true;
			$mails = $mailClass->getObjOutMails($_GET["oid"]);
			$this->view->setMainContent("<p class='title'>Просмотр отправленных писем: " . $mailClass->getContact() . "</p>");
		} else {
			$mails = $mailClass->getOutMails();
		}

		$this->view->mail_indexout(array("mails" => $mails));
		
		$this->view->showPage();
	}
}
?>