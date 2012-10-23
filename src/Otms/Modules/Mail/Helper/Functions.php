<?php

/**
 * This file is part of the Workapp project.
 *
 * Mail Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Mail\Helper;

use Engine\Modules;

/**
 * Mail Helper class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Functions extends Modules\Functions {
	/**
	 * Получить количество непрочитанных писем
	 * 
	 * @return int
	 */
	function unreadMails() {
		$mailClass = new Model_Mail();
		
		$unreadMails = $mailClass->getNumUnreadMails();
		return $unreadMails;
	}
	
	/**
	 * Возвращает текст письма
	 * 
	 * @param array
	 *    int $params[0] - ID письма
	 * @return string
	 */
	function getMailText($params) {
		$mailClass = new Model_Mail();
		
		return $mailClass->getMailText($params[0]);
	}
	
	/**
	 * Получить MD5 по реальному имени файла (файл - в файловом менеджере)
	 * 
	 * @param array
	 *    $params[0] - имя файла
	 * @return string
	 * @return boolean FALSE
	 */
	function getFileMD5($params) {
		$mailClass = new Model_Mail();
		
		return $mailClass->getFileMD5($params[0]);
	}
	
	/**
	 * Получить MD5 по реальному имени файла (файл - attach к письму)
	 * 
	 * @param array
	 *    int $params[0] - имя файла
	 * @return string
	 * @return boolean FALSE
	 */
	function getAttachFileMD5($params) {
		$mailClass = new Model_Mail();
	
		return $mailClass->getAttachFileMD5($params[0]);
	}
}
?>