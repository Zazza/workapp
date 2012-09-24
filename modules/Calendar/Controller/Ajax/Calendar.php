<?php
class Controller_Ajax_Calendar extends Modules_Ajax {
	private $_reserv;
	
	public function setCalTask($params) {
		$caltask = $params["caltask"];
			
		$cal = & $_SESSION["cal"];
			
		$cal["type"] = $caltask;
	}
	
	public function setCalDate($params) {
		$month = $params["month"];
		$year = $params["year"];
			
		$cal = & $_SESSION["cal"];
			
		$cal["month"] = $month;
		$cal["year"] = $year;
	}
	
	public function getReservsShow($params) {
		$date = $params["date"];
		
		$reserv = new Model_Reserv();
		$mobject = new Model_Object();
		
		if (isset($params["uid"])) {
			$uid = $params["uid"];
				
			$reserv->setUid($uid);
		} else {
			$uid = $this->registry["ui"]["id"];
		
			$reserv->setUid($this->registry["ui"]["id"]);
		}
		
		$data = $reserv->getDateReservs($date);

		foreach($data as $part) {
			$object = "";
			$dobject = $mobject->getShortObject($part["oid"]);
			foreach($dobject as $val) {
				if ($val["main"]) {
					$object = $object . " " . $val["val"];
				}
			}
			
			echo "<div style='margin-bottom: 10px; font-size: 10px;'><span style='font-weight: bold;'>" . $object . "</span><br />" . $part["startreserv"] . "&nbsp;-&nbsp;" . $part["endreserv"] . "</div>";
		}
	}
	
	public function getMonthOid($year, $month) {
		$data1 = $this->_reserv->getMonthReservs($year, $month);
		
		foreach($data1 as $key=>$value) {
			$data[$key]["reserv"] = $value;
		}
		
		foreach($data as $key=>$value) {
			$date = $year . $month . $key;
			$fulldate = $key . "." . $month . "." . $year;
		
			if ($value["reserv"]["num"] > 0) {
				$reserv = $this->view->render("cal_reserv", array("date" => $date, "fulldate" => $fulldate, "num" => $value["reserv"]["num"]));
			} else {
				$reserv = '';
			}
		
			$row[$key] = $reserv;
		}
		
		return $row;
	}
	
	public function getMonthAll($year, $month, $uid) {
		$data1 = $this->_reserv->getMonthReservs($year, $month);
		$data2 = $this->registry["tt"]->getMonthTasks($year, $month, $uid);
		
		foreach($data1 as $key=>$value) {
			$data[$key] = $data2[$key];
			$data[$key]["reserv"] = $value;
		}
		
		foreach($data as $key=>$value) {
			$date = date("Ymd", mktime(0, 0, 0, $month, $key, $year));
			$fulldate = date("d.m.Y", mktime(0, 0, 0, $month, $key, $year));
				
			if ($value["close"]["num"] > 0) {
				$close = $this->view->render("cal_close", array("num" => $value["close"]["num"]));
			} else {
				$close = '';
			}
			if ($value["time"]["num"] > 0) {
				$time = $this->view->render("cal_time", array("num" => $value["time"]["num"]));
			} else {
				$time = '';
			}
			if ($value["iter"]["num"] > 0) {
				$iter = $this->view->render("cal_iter", array("num" => $value["iter"]["num"]));
			} else {
				$iter = '';
			}
			if ($value["noiter"]["num"] > 0) {
				$noiter = $this->view->render("cal_noiter", array("num" => $value["noiter"]["num"]));
			} else {
				$noiter = '';
			}
			if ($value["reserv"]["num"] > 0) {
				$reserv = $this->view->render("cal_reserv", array("num" => $value["reserv"]["num"]));
			} else {
				$reserv = '';
			}
		
			$row[$key] = $this->view->render("cal_block", array("date" => $date, "fulldate" => $fulldate, "close" => $close, "iter" => $iter, "time" => $time, "noiter" => $noiter, "reserv" => $reserv));
		}
		
		return $row;
	}
	
	public function getMonth($params) {
		$month = $params["month"];
		$year = $params["year"];
		
		$this->_reserv = new Model_Reserv();

		if (isset($params["oid"])) {
			$oid = $params["oid"];
			$this->_reserv->setOid($oid);

			$row = $this->getMonthOid($year, $month);
		} else {
			if (isset($params["uid"])) {
				$uid = $params["uid"];
			
				$this->_reserv->setUid($uid);
			} else {
				$uid = $this->registry["ui"]["id"];
				
				$this->_reserv->setUid($this->registry["ui"]["id"]);
			}
			
			$row = $this->getMonthAll($year, $month, $uid);
		}

		$row["first"] = date("N", mktime(0, 0, 0, $month, 1, $year));
		$row["num"] = date("t", mktime(0, 0, 0, $month, 1, $year));
	
		echo json_encode($row);
	}
	
	public function getDateReservs($params) {
		$reserv = new Model_Reserv();
		$obj = new Model_Object();
		$user = new Model_User();
	
		$date = $params["date"];
	
		if (isset($params["oid"])) {
			$oid = $params["oid"];
				
			$reserv->setOid($oid);
		}
		if (isset($params["uid"])) {
			$uid = htmlspecialchars($params["uid"]);
				
			$reserv->setUid($uid);
		} else {
			$reserv->setUid($this->registry["ui"]["id"]);
		}
	
		$data = $reserv->getDateReservs($date);
	
		$res = "";
	
		if (count($data) > 0) {
			$j = 0;
			foreach($data as $part) {
				$res .= "<table height='50px' style='margin: 5px 2px; border: 0;'><tr style='border: 0'>";
				for($i=1; $i<=24; $i++) {
					if ( ($part["start"] < $i) and ($part["end"] >= $i) ) {
						$res .= "<td class='restd' data-id='" . $part["id"] . "' id='restd_" . $i . "' style='border: 0; width: 45px; border: 1px solid #6696AF; background-color: #DDEEFF;'>&nbsp;</td>";
					} else {
						$res .= "<td style='background-color: transparent; border: 0; width: 46px;'>&nbsp;</td>";
					}
				}
				$res .= "</tr></table>";
	
				$params["id"] = $part["id"];
				$res .= $this->getDateReservText($params);
	
				echo $res;
	
				$res = "";
			}
		}
	}
	
	public function getDateReservText($params) {
		$id = $params["id"];
	
		$obj = new Model_Object();
		$user = new Model_User();
		$reserv = new Model_Reserv();

		$data = $reserv->getReservFromID($id);
	
		if (count($data) > 0) {
			$data["obj"] = $obj->getShortObject($data[0]["oid"]);
			$data["user"] = $user->getUserInfo($data[0]["uid"]);
			$data["start"] = date("H:i d F Y", strtotime($data[0]["start"]));
			$data["end"] = date("H:i d F Y", strtotime($data[0]["end"]));
		}
	
		return $this->view->render("ralert", array("data" => $data));
	}
	
	public function showDateReservText($params) {
		$id = $params["id"];
		$wid = $params["wid"];
	
		$obj = new Model_Object();
		$user = new Model_User();
		$reserv = new Model_Reserv();

		$data = $reserv->getReservFromID($id);
	
		if (count($data) > 0) {
			$data["obj"] = $obj->getShortObject($data[0]["oid"]);
			$data["user"] = $user->getUserInfo($data[0]["uid"]);
			$data["start"] = date("H:i d F Y", strtotime($data[0]["start"]));
			$data["end"] = date("H:i d F Y", strtotime($data[0]["end"]));
		}
	
		echo $this->view->render("rwin", array("data" => $data, "wid" => $wid));
	}
	
	public function addReservs($params) {
		$oid = $params["oid"];
		$reservstart = $params["reservstart"];
		$reservstarttime = $params["reservstarttime"];
		$reservend = $params["reservend"];
		$reservendtime = $params["reservendtime"];
		$repeat = $params["repeat"];
		$repeat_num = $params["repeat_num"];
		$repeat_period = $params["repeat_period"];
		$repeat_cont_num = $params["repeat_cont_num"];
		$repeat_cont_period = $params["repeat_cont_period"];
		 
		$reserv = new Model_Reserv();
		 
		if (strtotime($reservstart . " " . $reservstarttime) > strtotime($reservend . " " . $reservendtime)) {
			echo "date";
		} else if ($oid == 0) {
			echo "object";
		} else {
			$reserv->setOid($oid);
			$reserv->setUid($this->registry["ui"]["id"]);
	
			if ($repeat) {
				 
				$diff = floor((strtotime($reservend . " " . $reservendtime . ":00")-strtotime($reservstart . " " . $reservstarttime . ":00"))/3600);
				 
				if ($repeat_cont_period == "m") {
					$next = date("Y-m-d", mktime(0,0,0,date("m") + $repeat_cont_num, date("d"), date("Y")));
					$cont = floor((strtotime($next)-strtotime(date("Y-m-d")))/86400);
				} else if ($repeat_cont_period == "y") {
					$next = date("Y-m-d", mktime(0,0,0,date("m"), date("d"), date("Y") + $repeat_cont_num));
					$cont = floor((strtotime($next)-strtotime(date("Y-m-d")))/86400);
				} else {
					$next = date("Y-m-d", mktime(0,0,0,date("m"), date("d") + $repeat_cont_num, date("Y")));
					$cont = floor((strtotime($next)-strtotime(date("Y-m-d")))/86400);
				}
				 
				$num = 0;
	
				$prev = date("Y-m-d", strtotime($reservstart)) . " " . $reservstarttime . ":00";
				$first = $prev;
				$end = date("Y-m-d", strtotime($reservend)) . " " . $reservendtime . ":00";
	
				while($num < $cont) {
					if (!$reserv->addEndReserv($prev, $end)) {
						echo "collision";
					};
	
					if ($repeat_period == "m") {
						$start = date("Y-m-d H:i:s", mktime(date("H", strtotime($prev)), date("i", strtotime($prev)), date("s", strtotime($prev)), date("m", strtotime($prev)) + $repeat_num, date("d", strtotime($prev)), date("Y", strtotime($prev))));
						$end = date("Y-m-d H:i:s", mktime(date("H", strtotime($prev)) + $diff, date("i", strtotime($prev)), date("s", strtotime($prev)), date("m", strtotime($prev)) + $repeat_num, date("d", strtotime($prev)), date("Y", strtotime($prev))));
					} else if ($repeat_period == "y") {
						$start = date("Y-m-d H:i:s", mktime(date("H", strtotime($prev)), date("i", strtotime($prev)), date("s", strtotime($prev)), date("m", strtotime($prev)), date("d", strtotime($prev)), date("Y", strtotime($prev)) + $repeat_num));
						$end = date("Y-m-d H:i:s", mktime(date("H", strtotime($prev)) + $diff, date("i", strtotime($prev)), date("s", strtotime($prev)), date("m", strtotime($prev)), date("d", strtotime($prev)), date("Y", strtotime($prev)) + $repeat_num));
					} else {
						$start = date("Y-m-d H:i:s", mktime(date("H", strtotime($prev)), date("i", strtotime($prev)), date("s", strtotime($prev)), date("m", strtotime($prev)), date("d", strtotime($prev)) + $repeat_num, date("Y", strtotime($prev))));
						$end = date("Y-m-d H:i:s", mktime(date("H", strtotime($prev)) + $diff, date("i", strtotime($prev)), date("s", strtotime($prev)), date("m", strtotime($prev)), date("d", strtotime($prev)) + $repeat_num, date("Y", strtotime($prev))));
					}
	
					$num = floor((strtotime($start)-strtotime($first))/86400);
	
					$prev = $start;
				}
			} else {
				$start = date("Y-m-d", strtotime($reservstart)) . " " . $reservstarttime . ":00";
				$end = date("Y-m-d", strtotime($reservend)) . " " . $reservendtime . ":00";
				if (!$reserv->addEndReserv($start, $end)) {
					echo "collision";
				};
			}
		}
	}
	
	public function editReservs($params) {
		$id = $params["id"];
		$oid = $params["oid"];
		$reservstart = $params["reservstart"];
		$reservstarttime = $params["reservstarttime"];
		$reservend = $params["reservend"];
		$reservendtime = $params["reservendtime"];
		$repeat = $params["repeat"];
		$repeat_num = $params["repeat_num"];
		$repeat_period = $params["repeat_period"];
		$repeat_cont_num = $params["repeat_cont_num"];
		$repeat_cont_period = $params["repeat_cont_period"];
			
		$reserv = new Model_Reserv();
	
		if (isset($oid)) {
			$reserv->setOid($oid);
		}
		$reserv->setUid($this->registry["ui"]["id"]);
	
		if ($repeat) {
			$diff = floor((strtotime($reservend . " " . $reservendtime . ":00")-strtotime($reservstart . " " . $reservstarttime . ":00"))/3600);
	
			if ($repeat_cont_period == "m") {
				$next = date("Y-m-d", mktime(0,0,0,date("m") + $repeat_cont_num, date("d"), date("Y")));
				$cont = floor((strtotime($next)-strtotime(date("Y-m-d")))/86400);
			} else if ($repeat_cont_period == "y") {
				$next = date("Y-m-d", mktime(0,0,0,date("m"), date("d"), date("Y") + $repeat_cont_num));
				$cont = floor((strtotime($next)-strtotime(date("Y-m-d")))/86400);
			} else {
				$next = date("Y-m-d", mktime(0,0,0,date("m"), date("d") + $repeat_cont_num, date("Y")));
				$cont = floor((strtotime($next)-strtotime(date("Y-m-d")))/86400);
			}
	
			$num = 0;
			$prev = date("Y-m-d", strtotime($reservstart)) . " " . $reservstarttime . ":00";
			$first = $prev;
			$end = date("Y-m-d", strtotime($reservend)) . " " . $reservendtime . ":00";
	
			while($num < $cont) {
				if (!$reserv->addEndReserv($prev, $end)) {
					echo "collision";
				};
	
				if ($repeat_period == "m") {
					$start = date("Y-m-d H:i:s", mktime(date("H", strtotime($prev)), date("i", strtotime($prev)), date("s", strtotime($prev)), date("m", strtotime($prev)) + $repeat_num, date("d", strtotime($prev)), date("Y", strtotime($prev))));
					$end = date("Y-m-d H:i:s", mktime(date("H", strtotime($prev)) + $diff, date("i", strtotime($prev)), date("s", strtotime($prev)), date("m", strtotime($prev)) + $repeat_num, date("d", strtotime($prev)), date("Y", strtotime($prev))));
				} else if ($repeat_period == "y") {
					$start = date("Y-m-d H:i:s", mktime(date("H", strtotime($prev)), date("i", strtotime($prev)), date("s", strtotime($prev)), date("m", strtotime($prev)), date("d", strtotime($prev)), date("Y", strtotime($prev)) + $repeat_num));
					$end = date("Y-m-d H:i:s", mktime(date("H", strtotime($prev)) + $diff, date("i", strtotime($prev)), date("s", strtotime($prev)), date("m", strtotime($prev)), date("d", strtotime($prev)), date("Y", strtotime($prev)) + $repeat_num));
				} else {
					$start = date("Y-m-d H:i:s", mktime(date("H", strtotime($prev)), date("i", strtotime($prev)), date("s", strtotime($prev)), date("m", strtotime($prev)), date("d", strtotime($prev)) + $repeat_num, date("Y", strtotime($prev))));
					$end = date("Y-m-d H:i:s", mktime(date("H", strtotime($prev)) + $diff, date("i", strtotime($prev)), date("s", strtotime($prev)), date("m", strtotime($prev)), date("d", strtotime($prev)) + $repeat_num, date("Y", strtotime($prev))));
				}
	
				$num = floor((strtotime($start)-strtotime($first))/86400);
	
				$prev = $start;
			}
		} else {
			$start = date("Y-m-d", strtotime($reservstart)) . " " . $reservstarttime . ":00";
			$end = date("Y-m-d", strtotime($reservend)) . " " . $reservendtime . ":00";
			if (!$reserv->updateEndReserv($id, $start, $end)) {
				echo "collision";
			};
		}
	}
	
	public function delReserv($params) {
		$id = $params["id"];
	
		$reserv = new Model_Reserv();
		$reserv->delReserv($id);
	}
	
	public function getReserv($params) {
		$id = $params["id"];
	
		$reserv = new Model_Reserv();
		$obj = new Model_Object();
	
		$data = $reserv->getReserv($id);
	
		$data["object"] = "<a style='cursor: pointer;' onclick='getInfo(" . $data["oid"] . ")'>";
		$temp = $obj->getShortObject($data["oid"]);
		foreach($temp as $part) {
			$data["object"] .= $part["val"] . " ";
		}
		$data["object"] .= "</a>";
	
		$data["reservstart"] = date("d.m.Y", strtotime($data["start"]));
		$data["reservstarttime"] = date("H:i", strtotime($data["start"]));
	
		if ($data["end"] != "0000-00-00 00:00:00") {
			$data["enddate"] = 1;
			$data["reservend"] = date("d.m.Y", strtotime($data["end"]));
			$data["reservendtime"] = date("H:i", strtotime($data["end"]));
		} else {
			$data["enddate"] = 0;
		}
	
		echo json_encode($data);
	}
	
	public function delSelected($params) {
		$json = json_decode($params["json"], true);
	
		$reserv = new Model_Reserv();
	
		foreach($json as $key=>$val) {
			$reserv->delReserv(substr($key, 3));
		}
	}
}
?>