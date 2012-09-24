<?php
class Controller_Ajax_Mail extends Modules_Ajax {

	function getMail($params) {
		$mid = $params["mid"];

		$mailClass = new Model_Mail();

		$mail = $mailClass->getMailFromId($mid);
		
		if (is_numeric($this->registry["tt"]->issetTaskFromMid($mid))) {
			$isset = true;
		} else {
			$isset = false;
		}

		$row["data"] = $this->view->render("mail_mail", array("mail" => $mail, "isset" => $isset));
		$row["new"] = $mailClass->newMail;

		echo json_encode($row);
	}

	function getMailOut($params) {
		$mid = $params["mid"];

		$mailClass = new Model_Mail();

		$mail = $mailClass->getMailOutFromId($mid);

		echo $this->view->render("mail_mailout", array("mail" => $mail));
	}

	function delMail($params) {
		$mid = $params["mid"];

		$mailClass = new Model_Mail();

		$mail = $mailClass->delMail($mid);
	}
	
	function delMails($params) {
		$json = array();
	
		$json = json_decode($params["json"], true);
	
		$mailClass = new Model_Mail($this->registry);
	
		foreach($json as $part) {
			$mail = $mailClass->delMail($part);
		}
	}

	function delMailOut($params) {
		$mid = $params["mid"];

		$mailClass = new Model_Mail();

		$mail = $mailClass->delMailOut($mid);
	}
	
	function delMailsOut($params) {
		$json = array();
	
		$json = json_decode($params["json"], true);
	
		$mailClass = new Model_Mail($this->registry);
	
		foreach($json as $part) {
			$mail = $mailClass->delMailOut($part);
		}
	}

	function getMailboxes() {
		$mailClass = new Model_Mail();
			
		$mailboxes = $mailClass->getUserInMailboxes($this->registry["ui"]["id"]);
		foreach($mailboxes as $mailbox) {
			$data[] = $mailbox["email"];
		}
		echo json_encode($data);
	}

	function checkMboxes($params) {
		$mbox = $params["mbox"];
		
		$mailClass = new Model_Mail();
		
		$mailClass->uid = $this->registry["ui"]["id"];

		if (!$mailClass->checkMail($mbox)) {
			echo "false";
		} else {
			echo "true";
		}
	}

	function delMailbox($params) {
		$mailbox = $params["email"];

		$mailClass = new Model_Mail();

		$mailClass->delMailbox($mailbox);
	}

	function delSort($params) {
		$sid = $params["sid"];

		$mailClass = new Model_Mail();

		$mailClass->delSort($sid);
	}

	function delMailDir($params) {
		$fid = $params["fid"];

		$mailClass = new Model_Mail();

		$mailClass->delMailDir($fid);
	}

	function setDefault($params) {
		$mailbox = $params["email"];

		$mailClass = new Model_Mail();

		$mailClass->setDefault($mailbox);
	}
	
	function setRead($params) {
		$fid = $params["fid"];
		
		$mailClass = new Model_Mail();

		$mailClass->setRead($fid);
	}
	
	function clearFolder($params) {
		$fid = $params["fid"];
		
		$mailClass = new Model_Mail();

		$mailClass->clearFolder($fid);
	}
	
	function addTaskFromMail($params) {
		$mid = $params["mid"];
		$startdate["startdate_global"] = date("Y-m-d"); $startdate["starttime_global"] = date("H:i:s");
		$post = json_decode('{"ttgid":"0","imp":"3","type":"0","lifetime_noiter":"","timetype_noiter":"day","itertime":"","timetype_itertime":"day","lifetime_iter":"","timetype_iter":"0"}', true);
		$post += $startdate;
		$post["task"] = "1";
		
		$tid = $this->registry["tt"]->addTask(0, $post, $mid);
		
		echo $tid;
	}
	
	function addContact($params) {
		$email = $params["email"];
		
		$contact = & $_SESSION["contact"];
		$contact["email"] = $email;
	}
	
	function sendMailComment($params) {
		$email = $params["email"];
		$cid = $params["cid"];
		
		$_POST["to"] = $email;
		$_POST["subject"] = "Комментарий";
		$_POST["textfield"] = $this->registry["tt"]->getCommentText($cid);
		$carr = $this->registry["tt"]->getComment($cid);
		foreach($carr as $part) {
			$_POST["attaches"][] = $this->registry["rootPublic"] . "system/settings/../../" . $part["filename"];
		}
		
		$mailClass = new Model_Mail();
		$helpers = new Helpers_Helpers();
		
		$mailboxes = $mailClass->getUserOutMailboxes();
		for($i=0; $i<count($mailboxes); $i++) {
			if ($mailboxes[$i]["default"]) { $def = $i; };
		}
		$smtp = $mailClass->getOutMailbox($mailboxes[$def]["id"]);
		
		$fromName = $this->registry["ui"]["name"] . " " . $this->registry["ui"]["soname"];

		if (!$err = $helpers->phpmailer($_POST, $smtp, $fromName)) {
			$mailClass->saveOutMail($_POST, $smtp);
			$this->registry["tt"]->addCommentSendmail($cid);
		}
	}
	
	function writeMail($params) {
		$json = json_decode($params["json"]);
		
		$object = new Model_Object();
		
		$data = array();
		foreach($json as $key=>$val) {
			$email = null;
			
			$oid = mb_substr($key, 4, mb_strlen($key)-5);
			$email = $object->getEmailFromOid($oid);
			
			if ($email != null) {
				$data[] = $email;
			}
		}

		$mail = & $_SESSION["mail"];
		$mail["json"] = json_encode($data);
	}
	
	function getSign($params) {
		$bid = $params["bid"];
	
		$mailClass = new Model_Mail();
	
		$email = $mailClass->getEmailFromId($bid);
		$signature = $mailClass->getSignature($email);
	
		echo $signature;
	}
}
?>