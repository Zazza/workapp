<?php

/**
 * This file is part of the Workapp project.
 *
 * Mail Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Mail\Ajax;

use Engine\Modules\Ajax;
use Otms\Modules\Mail\Model;
use Otms\System\Helper\Helpers;
use Otms\Modules\Objects\Model\Object;

/**
 * Ajax\Mail class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Mail extends Ajax {

	/**
	 * Вернуть письмо
	 * 
	 * @param array
	 *    int $params["mid"] - ID нужного письма
	 * @return string (json array)
	 *    string $row["data"] - .../mail/mail.tpl
	 *    boolean $row["new"] - флаг нового письма
	 */
	function getMail($params) {
		$mid = $params["mid"];

		$mailClass = new Model\Mail();

		$mail = $mailClass->getMailFromId($mid);
		
		if (is_numeric($this->registry["task"]->issetTaskFromMid($mid))) {
			$isset = true;
		} else {
			$isset = false;
		}

		$row["data"] = $this->view->render("mail_mail", array("mail" => $mail, "isset" => $isset));
		$row["new"] = $mailClass->newMail;

		return json_encode($row);
	}

	/**
	 * Вернуть письмо из папки "Отправленное"
	 *
	 * @param array
	 *    int $params["mid"] - ID нужного письма
	 * @return string (json array)
	 *    string $row["data"] - .../mail/mailout.tpl
	 */
	function getMailOut($params) {
		$mid = $params["mid"];

		$mailClass = new Model\Mail();

		$mail = $mailClass->getMailOutFromId($mid);

		return $this->view->render("mail_mailout", array("mail" => $mail));
	}

	/**
	 * Удалить письмо
	 * 
	 * @param array
	 *    int $params["mid"] - ID нужного письма
	 */
	function delMail($params) {
		$mid = $params["mid"];

		$mailClass = new Model\Mail();

		$mail = $mailClass->delMail($mid);
	}
	
	/**
	 * Удалить письма
	 *
	 * @param array
	 *    string $params["json"]
	 */
	function delMails($params) {
		$json = array();
	
		$json = json_decode($params["json"], true);
	
		$mailClass = new Model\Mail();
	
		foreach($json as $part) {
			$mail = $mailClass->delMail($part);
		}
	}

	/**
	 * Удалить письмо из папки "Отправленное"
	 *
	 * @param array
	 *    int $params["mid"] - ID нужного письма
	 */
	function delMailOut($params) {
		$mid = $params["mid"];

		$mailClass = new Model\Mail();

		$mail = $mailClass->delMailOut($mid);
	}
	
	/**
	 * Удалить письма из папки "Отправленное"
	 *
	 * @param array
	 *    string $params["json"]
	 */
	function delMailsOut($params) {
		$json = array();
	
		$json = json_decode($params["json"], true);
	
		$mailClass = new Model\Mail();
	
		foreach($json as $part) {
			$mail = $mailClass->delMailOut($part);
		}
	}

	/**
	 * Получить все mailboxes текущего пользователя
	 * 
	 * @return string (json array)
	 */
	function getMailboxes() {
		$mailClass = new Model\Mail();
			
		$mailboxes = $mailClass->getUserInMailboxes($this->registry["ui"]["id"]);
		foreach($mailboxes as $mailbox) {
			$data[] = $mailbox["email"];
		}
		return json_encode($data);
	}

	/**
	 * Проверка новой почты в mailboxe
	 * 
	 * @param array
	 *    string $params["mbox"]
	 * @return string "true"|"false"
	 */
	function checkMboxes($params) {
		$mbox = $params["mbox"];
		
		$mailClass = new Model\Mail();
		
		$mailClass->uid = $this->registry["ui"]["id"];

		if (!$mailClass->checkMail($mbox)) {
			return "false";
		} else {
			return "true";
		}
	}

	/**
	 * Удалить mailbox
	 * 
	 * @param array
	 *    $params["email"]
	 */
	function delMailbox($params) {
		$mailbox = $params["email"];

		$mailClass = new Model\Mail();

		$mailClass->delMailbox($mailbox);
	}

	/**
	 * Удалить сортировку
	 * 
	 * @param array
	 *    $params["sid"] - ID сортировки
	 */
	function delSort($params) {
		$sid = $params["sid"];

		$mailClass = new Model\Mail();

		$mailClass->delSort($sid);
	}

	/**
	 * Удалить папку для писем
	 * 
	 * @param array
	 *    $params["fid"]
	 */
	function delMailDir($params) {
		$fid = $params["fid"];

		$mailClass = new Model\Mail();

		$mailClass->delMailDir($fid);
	}

	/**
	 * Назначить mailbox по умолчанию для новых писем
	 * 
	 * @param array
	 *    $params["email"]
	 */
	function setDefault($params) {
		$mailbox = $params["email"];

		$mailClass = new Model\Mail();

		$mailClass->setDefault($mailbox);
	}
	
	/**
	 * Пометить все письма в папке, как прочитанные
	 * 
	 * @param array
	 *    $params["fid"] - ID папки
	 */
	function setRead($params) {
		$fid = $params["fid"];
		
		$mailClass = new Model\Mail();

		$mailClass->setRead($fid);
	}
	
	/**
	 * Удалить все письма в папке
	 * 
	 * @param array
	 *    $params["fid"] - ID папки
	 */
	function clearFolder($params) {
		$fid = $params["fid"];
		
		$mailClass = new Model\Mail();

		$mailClass->clearFolder($fid);
	}
	
	/**
	 * Создать задачу из письма
	 * 
	 * @param array
	 *    $params["mid"] - ID письма
	 * @return int $tid - ID новой задачи
	 */
	function addTaskFromMail($params) {
		$mid = $params["mid"];
		$startdate["startdate_global"] = date("Y-m-d"); $startdate["starttime_global"] = date("H:i:s");
		$post = json_decode('{"ttgid":"0","imp":"3","type":"0","lifetime_noiter":"","timetype_noiter":"day","itertime":"","timetype_itertime":"day","lifetime_iter":"","timetype_iter":"0"}', true);
		$post += $startdate;
		$post["task"] = "1";
		
		$tid = $this->registry["task"]->addTask(0, $post, $mid);
		
		return $tid;
	}
	
	/**
	 * Сохранить email в сессии для связи с объектом (контактом)
	 * 
	 * @param array
	 *    string $params["email"]
	 */
	function addContact($params) {
		$email = $params["email"];
		
		$contact = & $_SESSION["contact"];
		$contact["email"] = $email;
	}
	
	/**
	 * Отправить комментарий к задаче письмом
	 * 
	 * @param array
	 *    string $params["email"]
	 *    int $params["cid"] - ID комментария
	 */
	function sendMailComment($params) {
		$email = $params["email"];
		$cid = $params["cid"];
		
		$_POST["to"] = $email;
		$_POST["subject"] = "Комментарий";
		$_POST["textfield"] = $this->registry["task"]->getCommentText($cid);
		$carr = $this->registry["task"]->getComment($cid);
		foreach($carr as $part) {
			$_POST["attaches"][] = $this->registry["rootPublic"] . "system/settings/../../" . $part["filename"];
		}
		
		$mailClass = new Model\Mail();
		$helpers = new Helpers();
		
		$mailboxes = $mailClass->getUserOutMailboxes();
		for($i=0; $i<count($mailboxes); $i++) {
			if ($mailboxes[$i]["default"]) { $def = $i; };
		}
		$smtp = $mailClass->getOutMailbox($mailboxes[$def]["id"]);
		
		$fromName = $this->registry["ui"]["name"] . " " . $this->registry["ui"]["soname"];

		if (!$err = $helpers->phpmailer($_POST, $smtp, $fromName)) {
			$mailClass->saveOutMail($_POST, $smtp);
			$this->registry["task"]->addCommentSendmail($cid);
		}
	}
	
	/**
	 * Написать письмо выбранным объектам (контактам)
	 * 
	 * @param array
	 *    $params["json"]
	 */
	function writeMail($params) {
		$json = json_decode($params["json"]);
		
		$object = new Object();
		
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
	
	/**
	 * Получить подпись в mailbox для нового письма
	 * 
	 * @param array
	 *    $params["bid"]
	 * @return string
	 */
	function getSign($params) {
		$bid = $params["bid"];
	
		$mailClass = new Model\Mail();
	
		$email = $mailClass->getEmailFromId($bid);
		$signature = $mailClass->getSignature($email);
	
		return $signature;
	}
}
?>