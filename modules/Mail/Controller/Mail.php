<?php
class Controller_Mail extends Modules_Controller {
	protected $folders = null;
	protected $enableCheck = false;

	public function __construct($config) {
		parent::__construct($config);
		
		$mailClass = new Model_Mail();
		$this->folders = $mailClass->getFolders();
	}
	
	public function index() {
		$this->view->setTitle("Почта");
		
		$this->view->setLeftContent($this->view->render("left_mail", array("folders" => $this->folders, "enableCheck" => $this->enableCheck)));
		
		$mailClass = new Model_Mail();
		
		$obj = false;
		
		if (isset($_GET["folder"])) {
			$mailClass->getSortsByFolderId($_GET["folder"]);
				
			$mails = $mailClass->getMailsSort();
		} else if (isset($_GET["oid"])) {
			$obj = true;
			$mails = $mailClass->getObjMails($_GET["oid"]);
			$this->view->setMainContent("<p class='title'>Просмотр почтовой переписки: " . $mailClass->getContact() . "</p>");
		} else {
			$mails = $mailClass->getMails();
		}
		
		$this->view->mail_index(array("mails" => $mails, "obj" => $obj));
		
		$this->view->showPage();
	}
}
?>