<?php
class Controller_Calendar_Reservs extends Controller_Calendar {

	function index() {
		$this->view->setTitle("Просмотр броней");
		
		$reserv = new Model_Reserv();
		$object = new Model_Object();
		
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
