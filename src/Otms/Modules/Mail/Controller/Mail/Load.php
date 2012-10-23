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
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller\Mail\Load class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Load extends Mail {

	function index() {
		$response = new Response();
		
		$mid = $_GET["mid"];
		$part = $_GET["part"]-1;
		
		$mail = array();
		$content = null;

		$mailClass = new Model\Mail();

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
				$response->setContent($this->view->render("mail_mailbody", array("content" => $content, "mid" => $mid, "part" => $part+1, "out" => $out)));
			}
			
			$response->send();
			
			exit();
		}
	}
}
?>