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
 * Controller\Mail\Boxes class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Boxes extends Mail {

	function index() {
		$this->view->setTitle("Настройки почты");
		
		$this->view->setLeftContent($this->view->render("left_mail", array("folders" => $this->folders, "enableCheck" => $this->enableCheck)));
		
		$mailClass = new Model\Mail();
		$mailClass->uid = $this->registry["ui"]["id"];
		
		if ( (isset($this->args[1])) and ($this->args[1] == "add") ) {
			if (isset($_POST["submit"])) {
				$flag = true;
		
				if ($_POST["email"] == "") {
					$flag = false;
				}
				if ($_POST["in_server"] == "") {
					$flag = false;
				}
				if ($_POST["in_login"] == "") {
					$flag = false;
				}
				if ($_POST["in_password"] == "") {
					$flag = false;
				}
				if ($_POST["in_protocol"] == "") {
					$flag = false;
				}
				if ($_POST["in_port"] == "") {
					$flag = false;
				}
				if ($_POST["in_ssl"] == "") {
					$flag = false;
				}
				if ($_POST["out_server"] == "") {
					$flag = false;
				}
				if ($_POST["out_auth"] == 0) {
					$_POST["out_login"] = "";
					$_POST["out_password"] = "";
				}
				if ($_POST["out_auth"] == 1) {
					$_POST["out_login"] = $_POST["in_login"];
					$_POST["out_password"] = $_POST["in_password"];
				}
				if ($_POST["out_auth"] == 2) {
					if ($_POST["out_login"] == "") {
						$flag = false;
					}
					if ($_POST["out_password"] == "") {
						$flag = false;
					}
				}
				if ($_POST["out_port"] == "") {
					$flag = false;
				}
				if ($_POST["out_ssl"] == "") {
					$flag = false;
				}
		
				if ($flag) {
					$bid = $mailClass->addMailbox($_POST);
					$mailClass->addSignature($bid, $_POST["textfield"]);
						
					$this->view->refresh(array("timer" => "1", "url" => "mail/boxes/"));
				} else {
					$this->view->profile_addmailbox(array("err" => true, "post" => $_POST));
				}
			} else {
				$post["clear"] = true;
				$this->view->profile_addmailbox(array("err" => false, "post" => $post));
			}
		} elseif (isset($_GET["email"])) {
			if (isset($_POST["submit"])) {
				$flag = true;
		
				if ($_POST["email"] == "") {
					$flag = false;
				}
				if ($_POST["in_server"] == "") {
					$flag = false;
				}
				if ($_POST["in_login"] == "") {
					$flag = false;
				}
				if ($_POST["in_password"] == "") {
					$flag = false;
				}
				if ($_POST["in_protocol"] == "") {
					$flag = false;
				}
				if ($_POST["in_port"] == "") {
					$flag = false;
				}
				if ($_POST["in_ssl"] == "") {
					$flag = false;
				}
				if ($_POST["out_server"] == "") {
					$flag = false;
				}
				if ($_POST["out_auth"] == 0) {
					$_POST["out_login"] = "";
					$_POST["out_password"] = "";
				}
				if ($_POST["out_auth"] == 1) {
					$_POST["out_login"] = $_POST["in_login"];
					$_POST["out_password"] = $_POST["in_password"];
				}
				if ($_POST["out_auth"] == 2) {
					if ($_POST["out_login"] == "") {
						$flag = false;
					}
					if ($_POST["out_password"] == "") {
						$flag = false;
					}
				}
				if ($_POST["out_port"] == "") {
					$flag = false;
				}
				if ($_POST["out_ssl"] == "") {
					$flag = false;
				}
		
				if ($flag) {
					$mailClass->editMailbox($_GET["email"], $_POST);
					$mailClass->editSignature($_GET["email"], $_POST["textfield"]);
						
					$this->view->refresh(array("timer" => "1", "url" => "mail/boxes/"));
				} else {
					$this->view->profile_editmailbox(array("err" => true, "post" => $_POST));
				}
			} else {
				$mailbox = $mailClass->getMailbox($_GET["email"]);
				$signature = $mailClass->getSignature($_GET["email"]);
				
				$this->view->profile_editmailbox(array("post" => $mailbox, "signature" => $signature));
			}
		} else {
			$mailboxes = $mailClass->getUserMailboxes();
				
			$this->view->profile_listmailboxes(array("mailboxes" => $mailboxes));
		}
		
		$this->view->showPage();
	}
}
?>