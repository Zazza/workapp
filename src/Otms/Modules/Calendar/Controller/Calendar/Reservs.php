<?php

/**
 * This file is part of the Workapp project.
 *
 * Calendar Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Calendar\Controller\Calendar;

use Otms\Modules\Calendar\Controller\Calendar;
use Otms\Modules\Objects\Model\Object;
use Otms\Modules\Calendar\Model\Reserv;

/**
 * Controller\Calendar\Reservs
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */
class Reservs extends Calendar {

	function index() {
		$this->view->setTitle("Просмотр броней");
		
		$reserv = new Reserv();
		$object = new Object();
		
		$reserv->setUid($this->registry["ui"]["id"]);
		$list = $reserv->getList();

		$i = 0; $res_list = array();
		foreach($list as $part) {
			$i++;
			$res_list[$i] = $part;
			$res_list[$i]["object"] = $object->getShortObject($part["oid"]);
			$res_list[$i]["fstart"] = date("H:i, d F Y", strtotime($part["start"]));
			if ($part["end"] == "0000-00-00 00:00:00") {
				$res_list[$i]["fend"] = "-:- -- -- --";
			} else {
				$res_list[$i]["fend"] = date("H:i, d F Y", strtotime($part["end"]));
			}
		}
		
		$this->view->reservs(array("list" => $res_list));
		$this->view->showPage();
	}
}
?>
