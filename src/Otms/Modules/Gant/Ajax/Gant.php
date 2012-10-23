<?php

/**
 * This file is part of the Workapp project.
 *
 * Gant Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Gant\Ajax;

use Engine\Modules\Ajax;

/**
 * Ajax\Gant class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Gant extends Ajax {
	/**
	 * "Перемотать" даты на календаре влево
	 */
	public function calLeft() {
		$sgant = & $_SESSION["gant"];
		$sgant["date"]++;
	}
	
	/**
	 * "Перемотать" даты на календаре вправо
	 */
	public function calRight() {
		$sgant = & $_SESSION["gant"];
		if ($sgant["date"] > 1) {
			$sgant["date"]--;
		}
	}
	
	/**
	 * "Сбросить" календарь
	 */
	public function calReset() {
		$sgant = & $_SESSION["gant"];
		$sgant["date"] = 1;
	}
	
	/**
	 * Выбрать сколько дней показывать на календаре
	 * 
	 * @param array
	 *    $params["limit"] = 10, 30
	 */
	public function calLimit($params) {
		$limit = $params["limit"];
		
		$sgant = & $_SESSION["gant"];
		$sgant["limit"] = $limit;
	}
}
?>