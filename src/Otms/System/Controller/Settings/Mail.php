<?php

/**
 * This file is part of the Workapp project.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\System\Controller\Settings;

use Engine\Controller;
use Otms\System\Model\Settings;

class Mail extends Controller {
	public function index() {
		if ($this->registry["ui"]["admin"]) {
			$this->view->setTitle("Mail settings");
			
			$this->view->setLeftContent($this->view->render("left_settings", array()));
			
			$settings = new Settings();
			
			if (isset($_POST["submit"])) {
				$flag = true;
	
				if ($_POST["email"] == "") { $flag = false; }
				if ($_POST["server"] == "") { $flag = false; }
				if ($_POST["auth"] == "0") {
					$_POST["login"] = "";
					$_POST["password"] = "";
				} else {
					if ($_POST["login"] == "") { $flag = false; }
					if ($_POST["password"] == "") { $flag = false; }
				}
				if ($_POST["port"] == "") { $flag = false; }
				if ($_POST["ssl"] == "") { $flag = false; }
				
				if ($flag) {
					$settings->editMailbox($_POST);
					
					$this->view->refresh(array("timer" => "1", "url" => "settings/mail/"));
				} else {
					$this->view->settings_mail(array("err" => true, "post" => $_POST));
				}
			} else {
				$mailboxes = $settings->getMailbox();
			
				$this->view->settings_mail(array("post" => $mailboxes));
			}
		}
	}
}