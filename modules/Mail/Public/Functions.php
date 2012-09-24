<?php
class Mail_Public_Functions extends Modules_Functions {
	function unreadMails() {
		$mailClass = new Model_Mail();
		
		$unreadMails = $mailClass->getNumUnreadMails();
		return $unreadMails;
	}
	
	function getMailText($params) {
		$mailClass = new Model_Mail();
		
		return $mailClass->getMailText($params[0]);
	}
	
	function getFileMD5($params) {
		$mailClass = new Model_Mail();
		
		return $mailClass->getFileMD5($params[0]);
	}
	
	function getAttachFileMD5($params) {
		$mailClass = new Model_Mail();
	
		return $mailClass->getAttachFileMD5($params[0]);
	}
}
?>