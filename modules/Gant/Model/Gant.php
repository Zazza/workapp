<?php
class Model_Gant extends Modules_Model {
	private $_group = array();
	private $_task = array();
	
	private function _gantResult($opening, $ending, $cur, $closedate = 0) {
		$res = "";
		
		$opening = strtotime($opening);
		$ending = strtotime($ending);
		$cur = strtotime($cur);
		if ($closedate != 0) {
			$closedate = strtotime($closedate);
		}

		$flag = FALSE;
		
		if ($cur == $opening) {
			$res .= "start";
			$flag = TRUE;
		}
		
		if ($cur == $ending) {
			$res .= "end";
			$flag = TRUE;
		}
		
		if ( (!$flag) and ($closedate != 0) ) {
			if ( ($cur > $ending) and ($cur < $closedate) ) {
				$res = "over";
			} else if ( ($cur > $ending) and ($cur == $closedate) ) {
				$res = "overend";
			}
		}
		
		if ($res == "") { $res = date("d.m", $cur); }
		
		return $res;
	}
	
	public function getCalendar() {
		$data = array();
		
		$sql = "SELECT DISTINCT(t.id), t.name, t.gid, g.name AS gname, t.close, td.type, td.deadline, td.iteration, td.timetype_iteration, td.opening, t.ending
		FROM troubles AS t
		LEFT JOIN troubles_deadline AS td ON (td.tid = t.id)
		LEFT JOIN troubles_responsible AS tr ON (tr.tid = td.tid)
		LEFT JOIN troubles_responsible AS tr1 ON (tr1.tid = td.tid)
		LEFT JOIN group_tt AS g ON (g.id = t.gid)
		WHERE (t.secure = 0) OR ( (t.secure = 1) AND (t.who = :uid OR tr1.uid = :uid OR tr1.all = 1 OR tr1.gid = :gid) )
		ORDER BY t.gid, t.id, td.opening";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":uid" => $this->registry["ui"]["id"], ":gid" => $this->registry["ui"]["group"]);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		$sgant = & $_SESSION["gant"];
		if (!isset($sgant["date"])) {
			$sgant["date"] = 1;
		}
		if(!isset($sgant["limit"])) {
			$sgant["limit"] = 10;
		}
		$while = ($sgant["date"] * $sgant["limit"]) - $sgant["limit"];
		for($i=$sgant["limit"]*$sgant["date"]; $i>=$while; $i--) {
			$timestamp = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-$i, date("y")));
			for($j=0; $j<count($data); $j++) {
				
				if ($data[$j]["ending"] != "0000-00-00 00:00:00") {
					$data[$j]["ending"] = date("Y-m-d", strtotime($data[$j]["ending"]));
				} else {
					$data[$j]["ending"] =  date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));
				}
				
				$data[$j]["opening"] = date("Y-m-d", strtotime($data[$j]["opening"]));
				
				if (($days = $data[$j]["deadline"] / 60 / 60 / 24) < 1) {
					$days = 1;
				}
				
				if ($data[$j]["type"] == "1") {
					$start = strtotime($data[$j]["opening"]);
					$end = date("Y-m-d", mktime(0, 0, 0, date("m", $start), date("d", $start)+$days, date("Y", $start)));
					if ( ( $start <= strtotime($timestamp) ) and ( (strtotime($end) >= strtotime($timestamp)) or (strtotime($data[$j]["ending"]) >= strtotime($timestamp)) ) ) {
						$this->_group[$data[$j]["gid"]] = $data[$j]["gname"];
						$this->_task[$data[$j]["id"]] = $data[$j]["name"];
						
						$cal[$data[$j]["gid"]][$data[$j]["id"]][$timestamp] = $this->_gantResult($data[$j]["opening"], $end, $timestamp, $data[$j]["ending"]);
					}	
				} elseif ($data[$j]["type"] == "2") {
					$inc_type = $data[$j]["timetype_iteration"];
					
					if ($data[$j]["iteration"] != '0') {
						$inc = $data[$j]["iteration"];
					} else {
						$inc = $days;
					}
					
					$opening = strtotime($data[$j]["opening"]);

					$start = date("Y-m-d", mktime(0, 0, 0, date("m", $opening), date("d", $opening), date("Y", $opening)));
					$end =  date("Y-m-d", mktime(0, 0, 0, date("m", $opening), date("d", $opening)+$days, date("Y", $opening)));

					while(strtotime($start) <= strtotime($timestamp)) {
						if (($data[$j]["ending"] == "0000-00-00") or (strtotime($data[$j]["ending"]) >= strtotime($timestamp))) {
							if ( (strtotime($start) <= strtotime($timestamp)) and (strtotime($end) > strtotime($timestamp)) ) {
								$this->_group[$data[$j]["gid"]] = $data[$j]["gname"];
								$this->_task[$data[$j]["id"]] = $data[$j]["name"];
								
								$cal[$data[$j]["gid"]][$data[$j]["id"]][$timestamp] = $this->_gantResult($data[$j]["opening"], $data[$j]["ending"], $timestamp);
							}
						}
						
						if ($inc_type == "day") {
							$start = date("Y-m-d", mktime(0, 0, 0, date("m", $opening), date("d", $opening)+$inc, date("Y", $opening)));
							$end =  date("Y-m-d", mktime(0, 0, 0, date("m", $opening), date("d", $opening)+$inc+$days, date("Y", $opening)));
						} elseif($inc_type == "month") {
							$start = date("Y-m-d", mktime(0, 0, 0, date("m", $opening)+$inc, date("d", $opening), date("Y", $opening)));
							$end =  date("Y-m-d", mktime(0, 0, 0, date("m", $opening)+$inc, date("d", $opening)+$days, date("Y", $opening)));
						}
						
						$inc = $inc + $inc;
					}					
				} else {
					if ( (strtotime($data[$j]["opening"]) <= strtotime($timestamp)) and ((strtotime($data[$j]["ending"]) >= strtotime($timestamp))) ) {
						$this->_group[$data[$j]["gid"]] = $data[$j]["gname"];
						$this->_task[$data[$j]["id"]] = $data[$j]["name"];
						
						$cal[$data[$j]["gid"]][$data[$j]["id"]][$timestamp] = $this->_gantResult($data[$j]["opening"], $data[$j]["ending"], $timestamp);
					}
				}
			}
		}

		return $cal;
	}
	
	public function getTaskName() {
		return $this->_task;
	}
	
	public function getGroupName() {
		return $this->_group;
	}
}
?>
