<?php

/**
 * This file is part of the Workapp project.
 *
 * Mail Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Mail\Controller\Mail;

use Otms\Modules\Mail\Controller\Mail;
use Otms\Modules\Mail\Model;

/**
 * Controller\Mail\Send class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Send extends Mail {

	function index() {
		$mailClass = new Model\Mail();
		
		$this->view->setTitle("Sent mails");
		
		$this->view->setLeftContent($this->view->render("left_mail", array("folders" => $this->folders, "enableCheck" => $this->enableCheck)));
		
		if (isset($_GET["oid"])) {
			$obj = true;
			$mails = $mailClass->getObjOutMails($_GET["oid"]);
			$this->view->setMainContent("<p class='title'>Viewing of sent mails: " . $mailClass->getContact() . "</p>");
		} else {
			$mails = $mailClass->getOutMails();
		}

		$this->view->mail_indexout(array("mails" => $mails));
		
		$this->view->showPage();
	}
}
?>