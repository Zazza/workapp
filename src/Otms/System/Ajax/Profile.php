<?php

/**
 * This file is part of the Workapp project.
*
* (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
*
*/

namespace Otms\System\Ajax;

use Engine\Ajax;
use Otms\System\Model\Ui;

/**
 * Profile Ajax class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */

class Profile extends Ajax {
	/**
	 * Удаляет картинку пользователя (аватарку)
	 */
	function delAva() {
		$ui = new Ui();
		$ui->delAva();
	}
}