<?php

/**
 * This file is part of the Workapp project.
 *
 * Mail Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Mail\Controller;

use Engine\Modules\Controller;
use Otms\Modules\Mail\Model;

/**
 * Controller\Mail class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Mail extends Controller {
	protected $folders = null;
	protected $enableCheck = false;

	public function __construct($config) {
		parent::__construct($config);
		
		$mailClass = new Model\Mail();
		$this->folders = $mailClass->getFolders();
	}
	
	public function index() {
		$this->view->setTitle("Mail");
		
		$this->view->setLeftContent($this->view->render("left_mail", array("folders" => $this->folders, "enableCheck" => $this->enableCheck)));
		
		$mailClass = new Model\Mail();
		
		$obj = false;
		
		if (isset($_GET["folder"])) {
			$mailClass->getSortsByFolderId($_GET["folder"]);
				
			$mails = $mailClass->getMailsSort();
		} else if (isset($_GET["oid"])) {
			$obj = true;
			$mails = $mailClass->getObjMails($_GET["oid"]);
			$this->view->setMainContent("<p class='title'>Viewing of mail correspondence: " . $mailClass->getContact() . "</p>");
		} else {
			$mails = $mailClass->getMails();
		}
		
		$this->view->mail_index(array("mails" => $mails, "obj" => $obj));
		
		$this->view->showPage();
	}
}
?>