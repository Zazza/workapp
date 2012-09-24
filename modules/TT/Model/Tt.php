<?php
class Model_Tt extends Modules_Model {
	private $task;
	private $_route = false;
	
	private function getMD5($part) {
		if (substr($part, 0, 1) != "/") {
			$filename = mb_substr($part, 0, mb_strlen($part)-mb_strrpos($part, DIRECTORY_SEPARATOR));
		
			$sql = "SELECT `md5`
	        FROM fm_fs
	        WHERE `filename` = :filename AND pdirid = 1
	        LIMIT 1";
		
			$res = $this->registry['db']->prepare($sql);
			$param = array(":filename" => $filename);
			$res->execute($param);
			$row = $res->fetchAll(PDO::FETCH_ASSOC);
		} else {
			$filename = mb_substr($part, mb_strrpos($part, DIRECTORY_SEPARATOR) + 1, mb_strlen($part)-mb_strrpos($part, DIRECTORY_SEPARATOR));
			
			$path = mb_substr($part, 1, mb_strrpos($part, DIRECTORY_SEPARATOR)-1);
		
			if ($path == "0") {
				$sql = "SELECT `md5`
		        FROM fm_fs
		        WHERE `filename` = :filename AND pdirid = 0
		        LIMIT 1";
		
				$res = $this->registry['db']->prepare($sql);
				$param = array(":filename" => $filename);
				$res->execute($param);
				$row = $res->fetchAll(PDO::FETCH_ASSOC);
			} else {
				$sql = "SELECT f.md5
		        FROM fm_fs AS f
		        WHERE f.filename = :filename AND f.pdirid = :path
		        LIMIT 1";
		
				$res = $this->registry['db']->prepare($sql);
				$param = array(":filename" => $filename, ":path" => $path);
				$res->execute($param);
				$row = $res->fetchAll(PDO::FETCH_ASSOC);
			}
		}
		
		return $row[0]["md5"];
	}
	
	public function getGroups() {
		$sql = "SELECT id, `name`
        FROM group_tt
        ORDER BY `name`";

		$res = $this->registry['db']->prepare($sql);
		$param = array();
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		$num = count($data);
		$data[$num]["id"] = 0;
		$data[$num]["name"] = "Внутренняя";

		return $data;
	}

	public function getGroupName($gid) {
		$sql = "SELECT `name`
        FROM group_tt
        WHERE id = :gid
        LIMIT 1";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":gid" => $gid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		if (count($data) > 0) {
			return $data[0]["name"];
		} else {
			return "Внутренняя";
		}
	}

	public function addGroups($gname) {
		if ($gname == "") {
			return FALSE;
		}

		$sql = "SELECT id
        FROM group_tt
        WHERE `name` = :name
        LIMIT 1";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":name" => htmlspecialchars($gname));
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		$flag = FALSE;

		if (!isset($data[0]["id"])) {
			$flag = TRUE;
		}

		if ($flag) {
			$sql = "INSERT INTO group_tt (`name`) VALUES (:name)";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":name" => htmlspecialchars($gname));
			$res->execute($param);

			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function editGroupName($gid, $gname) {
		if ($gname == "") {
			return FALSE;
		}

		$sql = "SELECT id
        FROM group_tt
        WHERE `name` = :name
        LIMIT 1";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":name" => htmlspecialchars($gname));
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		$flag = FALSE;

		if (!isset($data[0]["id"])) {
			$flag = TRUE;
		} elseif ($gid == $data[0]["id"]) {
			$flag = TRUE;
		}

		if ($flag) {
			$sql = "UPDATE group_tt SET `name` = :gname WHERE id = :gid LIMIT 1";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":gid" => $gid, ":gname" => htmlspecialchars($gname));
			$res->execute($param);

			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function delGroup($gid) {
		$sql = "DELETE FROM group_tt WHERE id = :gid LIMIT 1";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":gid" => $gid);
		$res->execute($param);
	}

	public function getSortGroups() {
		$result = array();

		$object = new Model_Object();

		$sql = "SELECT g.id AS gid, g.name AS `gname`, t_o.oid, t.imp, td.type
        FROM troubles AS t
        LEFT JOIN troubles_responsible AS tr ON (tr.tid = t.id)
        LEFT JOIN troubles_deadline AS td ON (td.tid = t.id)
        LEFT JOIN troubles_objects AS t_o ON (t_o.tid = t.id)
        LEFT JOIN group_tt AS g ON (g.id = t.gid)
        WHERE ( ( (t.secure = 0) AND (tr.uid = :uid OR tr.all = 1 OR tr.gid = :gid) ) OR ( (t.secure = 1) AND (tr.uid = :uid OR tr.all = 1 OR tr.gid = :gid) ) )
            AND t.close = 0
            AND td.opening <= NOW()
		ORDER BY t.imp";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":uid" => $this->registry["ui"]["id"], ":gid" => $this->registry["ui"]["group"]);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		foreach($data as $part) {
			$result["gname"][$part["gid"]] = $part["gname"];
			$result["oid"][$part["oid"]] = $part["oid"];
			$result["imp"][$part["imp"]] = $part["imp"];
			$result["type"][$part["type"]] = $part["type"];
		}

		if (count($result) != 0) {
			$result["gname"] = array_unique($result["gname"]);
			$temp = array_unique($result["oid"]);
			foreach($temp AS $key=>$part) {
				$result["obj"][$key] = $object->getShortObject($part);
			}
			$result["imp"] = array_unique($result["imp"]);
			$result["type"] = array_unique($result["type"]);
		}

		return $result;
	}

	public function getSortGroupsMe() {
		$result = array();

		$object = new Model_Object();

		$sql = "SELECT g.id AS gid, g.name AS `gname`, t_o.oid, t.imp, td.type
        FROM troubles AS t
        LEFT JOIN troubles_responsible AS tr ON (tr.tid = t.id)
        LEFT JOIN troubles_deadline AS td ON (td.tid = t.id)
        LEFT JOIN troubles_objects AS t_o ON (t_o.tid = t.id)
        LEFT JOIN group_tt AS g ON (g.id = t.gid)
        WHERE ( ( (t.secure = 0) AND (t.who = :uid OR tr.uid = :uid OR tr.all = 1 OR tr.gid = :gid) ) OR ( (t.secure = 1) AND (t.who = :uid OR tr.uid = :uid OR tr.all = 1 OR tr.gid = :gid) ) )
            AND t.close = 0
            AND td.opening <= NOW()
		ORDER BY t.imp";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":uid" => $this->registry["ui"]["id"], ":gid" => $this->registry["ui"]["group"]);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		foreach($data as $part) {
			$result["gname"][$part["gid"]] = $part["gname"];
			$result["oid"][$part["oid"]] = $part["oid"];
			$result["imp"][$part["imp"]] = $part["imp"];
			$result["type"][$part["type"]] = $part["type"];
		}

		if (count($result) != 0) {
			$result["gname"] = array_unique($result["gname"]);
			$temp = array_unique($result["oid"]);
			foreach($temp AS $key=>$part) {
				$result["obj"][$key] = $object->getShortObject($part);
			}
			$result["imp"] = array_unique($result["imp"]);
			$result["type"] = array_unique($result["type"]);
		}

		return $result;
	}

	public function getTasks() {
		$data = array(); $result = array();

		$year = date("Y");
		$month = date("m");
		$day = date("d");

		$sortmytt = & $_SESSION["sortmytt"];

		if (isset($sortmytt["sort"])) {
			$sort = $sortmytt["sort"];
		}
		if (isset($sortmytt["id"])) {
			$id = $sortmytt["id"];
		};
		 
		if ($sort == "group") {
			if ($id == 'false') {
				$where = "";
				$sql_inc = "ORDER BY t.gid, t.imp DESC, t.id DESC";
			} else {
				$where = "AND t.gid = " . $id;
				$sql_inc = "ORDER BY t.gid, t.imp DESC, t.id DESC";
			}
		} elseif ($sort == "obj") {
			if ($id == 'false') {
				$where = "";
				$sql_inc = "ORDER BY t_o.oid DESC, t.imp DESC, t.id DESC";
			} else {
				$where = "AND t_o.oid = " . $id;
				$sql_inc = "ORDER BY t_o.oid DESC, t.imp DESC, t.id DESC";
			}
		} elseif ($sort == "imp") {
			if ($id == 'false') {
				$where = "";
				$sql_inc = "ORDER BY t.imp DESC, t.id DESC";
			} else {
				$where = "AND t.imp = " . $id;
				$sql_inc = "ORDER BY t.imp DESC, t.id DESC";
			}
		} elseif ($sort == "type") {
			if ($id == 'false') {
				$where = "";
				$sql_inc = "ORDER BY td.type DESC, t.imp DESC, t.id DESC";
			} else {
				$where = "AND td.type = " . $id;
				$sql_inc = "ORDER BY td.type DESC, t.imp DESC, t.id DESC";
			}
		} elseif ($sort == "date") {
			if ($id == 'false') {
				$where = "";
				$sql_inc = "ORDER BY t.id DESC";
			}
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT(t.id), td.type, td.deadline, td.iteration, td.timetype_iteration, td.opening, tc.cid
        FROM troubles AS t
        LEFT JOIN troubles_responsible AS tr ON (tr.tid = t.id)
        LEFT JOIN troubles_deadline AS td ON (td.tid = t.id)
        LEFT JOIN troubles_composite AS tc ON (tc.tid = t.id)
        LEFT JOIN troubles_objects AS t_o ON (t_o.tid = t.id)
        WHERE (tr.uid = :uid OR tr.all = 1 OR tr.gid = :gid)
            AND t.close = 0
            AND td.opening <= NOW()
            " . $where . "
        " . $sql_inc . "
        LIMIT " . $this->startRow .  ", " . $this->limit;

		$res = $this->registry['db']->prepare($sql);
		$param = array(":uid" => $this->registry["ui"]["id"], ":gid" => $this->registry["ui"]["group"]);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		$this->totalPage = $this->registry['db']->query("SELECT FOUND_ROWS()")->fetchColumn();

		//Если общее число статей больше показанного, вызовем пейджер
		if ($this->totalPage < $this->limit+1)  {
		} else {
			$this->Pager();
		}

		for($i=0; $i<count($data); $i++) {
			$start = strtotime($data[$i]["opening"]);
			if (($days = $data[$i]["deadline"] / 60 / 60 / 24) < 1) {
				$days = 1;
			}

			if ($data[$i]["type"] == "0" or $data[$i]["type"] == "1") {
				$curDay = date("d", mktime(0, 0, 0, date("m", $start), date("d", $start), date("Y", $start)));
				$curMonth = date("m", mktime(0, 0, 0, date("m", $start), date("d", $start), date("Y", $start)));
				$curYear = date("Y", mktime(0, 0, 0, date("m", $start), date("d", $start), date("Y", $start)));

				if ($curYear . $curMonth . $curDay <= $year . $month . $day) {
					$result[]["id"] = $data[$i]["id"];
				}
			} elseif ($data[$i]["type"] == "2") {
				$curYear = date("Y", $start);
				$curMonth = date("m", $start);
				
				$inc_day = 0;
				$inc_month = 0;
				if ($data[$i]["iteration"] != 0) {
					$inc = $data[$i]["iteration"];
				} else {
					$inc = $days;
				}
				$inc_type = $data[$i]["timetype_iteration"];

				while( ($curYear <= $year) ) {
					for($l=0; $l<$days; $l++) {
						$curDay = date("j", mktime(0, 0, 0, date("m", $start) + $inc_month, date("d", $start) + $l + $inc_day, date("Y", $start)));
						$curMonth = date("m", mktime(0, 0, 0, date("m", $start) + $inc_month, date("d", $start) + $l + $inc_day, date("Y", $start)));
						$curYear = date("Y", mktime(0, 0, 0, date("m", $start) + $inc_month, date("d", $start) + $l + $inc_day, date("Y", $start)));
							
						if ( ($curYear == $year) and ($curMonth == $month)  and ($curDay == $day) ) {
							$result[]["id"] = $data[$i]["id"];
						}
					}

					if ($inc_type == "day") {
						$inc_day = $inc_day + $inc;
					} elseif($inc_type == "month") {
						$inc_month = $inc_month + $inc;
					}
				}
			}
		}

		return $result;
	}

	public function getTasksDate($uid, $date) {
		$user = new Model_User();
		
		$data = array();
		$result = array();

		$caltype = & $_SESSION["cal"];
		if (!isset($caltype["type"])) {
			$caltype["type"] = 0;
		}

		if ($caltype["type"] == 1) {
			$sql_type = "WHERE t.who = " . $uid;
		} else {
			$sql_type = "WHERE tr.uid = " . $uid . " OR tr.all = 1 OR tr.gid = " . $user->getGidFromUid($uid);
		}

		$year = date("Y", strtotime($date));
		$month = date("m", strtotime($date));
		$day = date("d", strtotime($date));

		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT(t.id), t.close, td.type, td.deadline, td.iteration, td.timetype_iteration, td.opening, t.ending
        FROM troubles AS t 
        LEFT JOIN troubles_deadline AS td ON (td.tid = t.id)
        LEFT JOIN troubles_responsible AS tr ON (tr.tid = td.tid)
        LEFT JOIN troubles_composite AS tc ON (tc.tid = t.id)
        " . $sql_type . "
        ORDER BY td.opening DESC";

		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		for($i=0; $i<count($data); $i++) {

			$inc_day = 0;
			$inc_month = 0;
			$inc = $data[$i]["iteration"];
			$inc_type = $data[$i]["timetype_iteration"];
			$start = strtotime($data[$i]["opening"]);
			$end = strtotime($data[$i]["ending"]);
			if (($days = $data[$i]["deadline"] / 60 / 60 / 24) < 1) {
				$days = 1;
			}

			if ($data[$i]["close"] != "0") {
				$curDay = date("d", mktime(0, 0, 0, date("m", $end), date("d", $end), date("Y", $end)));
				$curMonth = date("m", mktime(0, 0, 0, date("m", $end), date("d", $end), date("Y", $end)));
				$curYear = date("Y", mktime(0, 0, 0, date("m", $end), date("d", $end), date("Y", $end)));

				if ( ($curYear == $year) and ($curMonth == $month)  and ($curDay == $day) ) {
					$result[]["id"] = $data[$i]["id"];
				}
			} elseif ($data[$i]["type"] == "0" or $data[$i]["type"] == "1") {
				$curDay = date("d", mktime(0, 0, 0, date("m", $start), date("d", $start), date("Y", $start)));
				$curMonth = date("m", mktime(0, 0, 0, date("m", $start), date("d", $start), date("Y", $start)));
				$curYear = date("Y", mktime(0, 0, 0, date("m", $start), date("d", $start), date("Y", $start)));

				if ($curYear . $curMonth . $curDay <= $year . $month . $day) {
					$result[]["id"] = $data[$i]["id"];
				}
			} elseif ($data[$i]["type"] == "2") {
				$curYear = date("Y", $start);
				$curMonth = date("m", $start);

				while( ($curYear <= $year) ) {
					for($l=0; $l<$days; $l++) {
						$curDay = date("d", mktime(0, 0, 0, date("m", $start) + $inc_month, date("d", $start) + $l+ $inc_day, date("Y", $start)));
						$curMonth = date("m", mktime(0, 0, 0, date("m", $start) + $inc_month, date("d", $start) + $l+ $inc_day, date("Y", $start)));
						$curYear = date("Y", mktime(0, 0, 0, date("m", $start) + $inc_month, date("d", $start) + $l+ $inc_day, date("Y", $start)));

						if ( ($curYear == $year) and ($curMonth == $month) and ($curDay == $day) ) {
							$result[]["id"] = $data[$i]["id"];
						}
					}

					if ($inc_type == "day") {
						$inc_day = $inc_day + $inc;
					} elseif($inc_type == "month") {
						$inc_month = $inc_month + $inc;
					}
				}
			}
		}

		//Если общее число статей больше показанного, вызовем пейджер
		$this->totalPage = count($result);
		if ($this->totalPage >= $this->limit+1)  {
			$this->Pager();
		}

		$limitResult = array();
		
		for($i = $this->startRow; $i < $this->startRow + $this->limit; $i++) {
			if (isset($result[$i])) {
				$limitResult[] = $result[$i];
			}
		}

		return $limitResult;
	}

	public function acceptReadTask($task) {
		$uid = $this->registry["ui"]["id"];
		$gid = $this->registry["ui"]["group"];

		$flag = false;

		foreach($task as $part) {
			if ($part["uid"] == $uid) {
				$flag = true;
			}
			if ($part["rgid"] == $gid) {
				$flag = true;
			}
			if ($part["all"]) {
				$flag = true;
			}
		}

		if ($task[0]["who"] == $uid) {
			$flag = true;
		}

		return $flag;
	}

	public function getTask($tid, $uid = null) {
		if ($uid == null) {
			$uid = $this->registry["ui"]["id"];
		}
		
		$data = array();

		$sql = "SELECT DISTINCT(t.id), t.route, t_o.oid, t.who, t.remote_id, t.mail_id, t.imp, t.secure, t.name AS `name`, t.text, t.opening AS start, t.ending, t.gid, t.close, g.name AS `group`, t.cuid AS cuid, tr.uid, tr.gid AS rgid, tr.all AS `all`, td.type, td.opening, td.deadline, td.iteration, td.timetype_iteration, ts.id AS spam, tc.cid, tch.tid AS `chid`
	        FROM troubles AS t
	        LEFT JOIN troubles_responsible AS tr ON (tr.tid = t.id)
	        LEFT JOIN troubles_responsible AS tr1 ON (tr1.tid = t.id)
	        LEFT JOIN troubles_objects AS t_o ON (t_o.tid = t.id)
	        LEFT JOIN troubles_deadline AS td ON (td.tid = t.id)
	        LEFT JOIN troubles_composite AS tc ON (tc.tid = t.id)
	        LEFT JOIN troubles_composite AS tch ON (tch.cid = t.id)
	        LEFT JOIN troubles_spam AS ts ON (ts.tid = t.id)
	        LEFT JOIN group_tt AS g ON (t.gid = g.id)
	        WHERE t.id = :tid AND ( (t.secure = 0) OR ( (t.secure = 1) AND (t.who = :uid OR tr1.uid = :uid OR tr1.all = 1 OR tr1.gid = :gid) ) )";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":tid" => $tid, ":uid" => $uid, ":gid" => $this->registry["ui"]["group"]);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		if (count($data) > 0) {
			$data[0]["startdate"] = date("Y-m-d", strtotime($data[0]["opening"]));
			$data[0]["starttime"] = date("H:i:s", strtotime($data[0]["opening"]));

			$data[0]["startF"] = $this->editDate($data[0]["start"]);
			$data[0]["openingF"] = $this->editDate($data[0]["opening"]);
			$data[0]["endingF"] = $this->editDate($data[0]["ending"]);

			$d = strtotime($data[0]["opening"]);
			$deadline = $data[0]["deadline"];
			$expire = date("YmdHis", mktime(date("H", $d), date("i", $d), date("s", $d) + $deadline, date("m", $d), date("d", $d), date("Y", $d)));
			if ($data[0]["close"] != 0) {
				$end = date("YmdHis", strtotime($data[0]["ending"]));
			} else {
				$end = date("YmdHis");
			}

			if (($data[0]["deadline"] / 60 /60 / 24) >= 1) {
				$data[0]["deadline"] = ($data[0]["deadline"] / 60 /60 / 24);
				$data[0]["deadline_date"] = "дней";
				if ($expire < $end) {
					$data[0]["expire"] = TRUE;
				} else { $data[0]["expire"] = FALSE;
				}
			} else {
				if (($data[0]["deadline"] / 60 /60 ) >= 1) {
					$data[0]["deadline"] = ($data[0]["deadline"] / 60 /60);
					$data[0]["deadline_date"] = "часов";
					if ($expire < $end) {
						$data[0]["expire"] = TRUE;
					} else { $data[0]["expire"] = FALSE;
					}
				} elseif (($data[0]["deadline"] / 60 ) >= 1) {
					$data[0]["deadline"] = ($data[0]["deadline"] / 60 );
					$data[0]["deadline_date"] = "минут";
					if ($expire < $end) {
						$data[0]["expire"] = TRUE;
					} else { $data[0]["expire"] = FALSE;
					}
				} else {
					$data[0]["deadline"] = "";
					$data[0]["deadline_date"] = "0";
				}
			}

			if ($data[0]["mail_id"] != 0) {
				$mailClass = new Model_Mail();
				$data[0]["text"] = $mailClass->getMailFromId($data[0]["mail_id"]);
			}

			if ($data[0]["group"] == "") {
				$data[0]["group"] = "Внутренняя";
			}

			if ($data[0]["mail_id"] != 0) {
				$contacts = array();
					
				$sql = "SELECT mc.oid
					FROM mail_contacts AS mc
					LEFT JOIN `mail` AS m ON (m.email = mc.email)
					WHERE m.id = :mail_id LIMIT 1";
					
				$res = $this->registry['db']->prepare($sql);
				$param = array(":mail_id" => $data[0]["mail_id"]);
				$res->execute($param);
				$contacts = $res->fetchAll(PDO::FETCH_ASSOC);
					
				if (count($contacts) > 0) {
					$data[0]["oid"] = $contacts[0]["oid"];
				}
			}
			// Названия вложенных задач
			if ($data[0]["chid"] != null) {
				foreach($data as $key=>$val) {
					$sql = "SELECT name
				        FROM troubles
				        WHERE id = :id
						LIMIT 1";
				
					$res = $this->registry['db']->prepare($sql);
					$param = array(":id" => $val["chid"]);
					$res->execute($param);
					$chname = $res->fetchAll(PDO::FETCH_ASSOC);
						
					$data[$key]["chname"] = $chname[0]["name"];
				}
			}
			
			if ($data[0]["remote_id"] != 0) {
				$sql = "SELECT ma.filename
			        FROM mail_attach AS ma
			        WHERE ma.tid = :id";

				$res = $this->registry['db']->prepare($sql);
				$param = array(":id" => $tid);
				$res->execute($param);
				$attaches = $res->fetchAll(PDO::FETCH_ASSOC);
					
				$data[0]["attach"] = $attaches;
			} else {
				$sql = "SELECT fs.pdirid, fs.filename
			        FROM troubles_attach AS ta
			        LEFT JOIN fm_fs AS fs ON (fs.md5 = ta.md5)
			        WHERE ta.tid = :id";

				$res = $this->registry['db']->prepare($sql);
				$param = array(":id" => $tid);
				$res->execute($param);
				$attaches = $res->fetchAll(PDO::FETCH_ASSOC);
					
				$data[0]["attach"] = $attaches;
			}
		}
			


		$flag = FALSE;

		if (isset($data[0]["secure"])) {
			if ($data[0]["secure"] == "1") {
				if ($data[0]["who"] == $uid) {
					$flag = TRUE;
				} else {
					foreach($data as $part) {
						if ($part["uid"] == $uid) {
							$flag = TRUE;
						}
						if ($part["rgid"] == $this->registry["ui"]["group"]) {
							$flag = TRUE;
						}
						if ($part["all"]) {
							$flag = TRUE;
						}
					}
				}
			} else {
				$flag = TRUE;
			}
		} else {
			$flag = TRUE;
		}

		if ($flag) {
			return $data;
		} else {
			return FALSE;
		}
	}
	
	private function validateIterDate($deadline, $itertime, $iter_period) {
		if ($iter_period == "min") {
			$result = $itertime * 60;
		} elseif ($iter_period == "hour") {
			$result = $itertime * 60 * 60;
		} elseif ($iter_period == "day") {
			$result = $itertime * 60 * 60 * 24;
		} elseif ($iter_period == "month") {
			$result = $itertime * 60 * 60 * 24 * 30;
		}

		if ($deadline > $result) {
			return false;
		} else {
			return true;
		}
	}
	
	public function setRoute() {
		$this->_route = true;
	}

	public function addTask($oid, $post, $mid = false) {
		if ( (isset($post["delegate"])) and ($post["delegate"] != null) ) {
			$this->uid = $post["delegate"];
		} else if ( !isset($this->uid) ) {
			$this->uid = $this->registry["ui"]["id"];
		}

		if ($post["task"] != '') {
			
			if (isset($post["secure"])) {
				$secure = $post["secure"];
			} else {
				$secure = 0;
			}

			if ($this->_route) {
				$sql = "INSERT INTO troubles (route, remote_id, who, imp, secure, name, text, gid) VALUES (:route, :remote_id, :who, :imp, :secure, :name, :text, :gid)";

				$res = $this->registry['db']->prepare($sql);
				$param = array(":route" => 1, ":remote_id" => '0', ":who" => $this->uid, ":imp" => $post["imp"], ":secure" => $secure, "name" => $post["taskname"], ":text" => $post["task"], ":gid" => $post["ttgid"]);
				$res->execute($param);
			} else if ($mid) {
				$sql = "INSERT INTO troubles (remote_id, who, imp, secure, mail_id, gid) VALUES (:remote_id, :who, :imp, :secure, :mail_id, :gid)";

				$res = $this->registry['db']->prepare($sql);
				$param = array(":remote_id" => '0', ":who" => $this->uid, ":imp" => $post["imp"], ":secure" => $secure, ":mail_id" => $mid, ":gid" => $post["ttgid"]);
				$res->execute($param);
			} else {
				$sql = "INSERT INTO troubles (remote_id, who, imp, secure, name, text, gid) VALUES (:remote_id, :who, :imp, :secure, :name, :text, :gid)";

				$res = $this->registry['db']->prepare($sql);
				$param = array(":remote_id" => '0', ":who" => $this->uid, ":imp" => $post["imp"], ":secure" => $secure, "name" => $post["taskname"], ":text" => $post["task"], ":gid" => $post["ttgid"]);
				$res->execute($param);
			}

			$tid = $this->registry['db']->lastInsertId();
			
			$sql = "INSERT INTO troubles_objects (tid, oid) VALUES (:tid, :oid)";
			
			$res = $this->registry['db']->prepare($sql);
			$param = array(":oid" => $oid, ":tid" => $tid);
			$res->execute($param);
			
			if (isset($post["sub"])) {
				$sql = "INSERT INTO troubles_composite (cid, tid) VALUES (:cid, :tid)";
				
				$res = $this->registry['db']->prepare($sql);
				$param = array(":cid" => $post["sub"], ":tid" => $tid);
				$res->execute($param);
			}

			// ответственные
			if (!isset($post["ruser"])) {
				$post["ruser"] = array();
			}
			if (!isset($post["gruser"])) {
				$post["gruser"] = array();
			}
			if (!isset($post["rall"])) {
				$post["rall"] = array();
			}

			foreach($post["ruser"] as $part) {
				$sql = "INSERT INTO troubles_responsible (tid, uid) VALUES (:tid, :uid)";

				$res = $this->registry['db']->prepare($sql);
				$param = array(":tid" => $tid, ":uid" => $part);
				@$res->execute($param);
			}

			foreach($post["gruser"] as $part) {
				$sql = "INSERT INTO troubles_responsible (tid, gid) VALUES (:tid, :gid)";

				$res = $this->registry['db']->prepare($sql);
				$param = array(":tid" => $tid, ":gid" => $part);
				@$res->execute($param);
			}

			if ($post["rall"] == "1") {
				$sql = "INSERT INTO troubles_responsible (tid, `all`) VALUES (:tid, 1)";

				$res = $this->registry['db']->prepare($sql);
				$param = array(":tid" => $tid);
				@$res->execute($param);
			}
			// END ответственные
				
			// email move task
			$helpers = new Helpers_Helpers();
			$users = $this->registry["user"]->getUniqUsers($post);
			foreach($users as $part) {
				$user = $this->registry["user"]->getUserInfo($part);
				if ($user["email_for_task"]) {
					if ($mid) {
						$mailClass = new Model_Mail();
						$post["task"] = $mailClass->getMailText($mid);

						$mail = $mailClass->getMailFromId($mid);
						foreach($mail[0]["attach"] as $part) {
							$post["attaches"][] = $this->registry["rootPublic"] . "system/settings/../../" . $part["filename"];
						}

						$post["mail"] = true;
						$post["mail_id"] = $mid;
					} else {
						$post["mail"] = false;
					}
						
					$subject["method"] = "addtask";
					$subject["name"] = "OTMS";
					$subject["tid"] = $tid;
						
					$post["uemail"] = $this->registry["ui"]["email"];
					$post["uname"] = $this->registry["ui"]["name"];
					$post["usoname"] = $this->registry["ui"]["soname"];
					$post["ugname"] = $this->registry["ui"]["gname"];
					$post["uavatar"] = base64_encode(file_get_contents($this->registry["ui"]["avatarpath"]));

					$helpers->sendTask($user["email"], $subject, $post);
				}
			}
			// END email move task

			if ($post["type"] == "0") {
				
				if ($post["startdate_global"] == "") {
					$post["startdate_global"] = date("Y-m-d");
				}
				if ($post["starttime_global"] == "") {
					$post["starttime_global"] = date("H:i:s");
				}

				$starttime = $post["startdate_global"] . " " . $post["starttime_global"];

				$lifetime = 0;
				$post["itertime"] = "";

			} elseif ($post["type"] == "1") {
				$post["itertime"] = "";
				
				if ($post["startdate_noiter"] == "") {
					$post["startdate_noiter"] = date("Y-m-d");
				}
				if ($post["starttime_noiter"] == "") {
					$post["starttime_noiter"] = date("H:i:s");
				}

				$starttime = $post["startdate_noiter"] . " " . $post["starttime_noiter"];

				if ($post["timetype_noiter"] == "min") {

					$lifetime = $post["lifetime_noiter"] * 60;

				} elseif ($post["timetype_noiter"] == "hour") {

					$lifetime = $post["lifetime_noiter"] * 60 * 60;

				} elseif ($post["timetype_noiter"] == "day") {

					$lifetime = $post["lifetime_noiter"] * 24 * 60 * 60;

				} else {

					$lifetime = 0;

				}
			} elseif ($post["type"] == "2") {
				
				if ($post["startdate_iter"] == "") {
					$post["startdate_iter"] = date("Y-m-d");
				}
				if ($post["starttime_iter"] == "") {
					$post["starttime_iter"] = date("H:i:s");
				}

				$starttime = $post["startdate_iter"] . " " . $post["starttime_iter"];

				if ($post["timetype_iter"] == "day") {

					$lifetime = $post["lifetime_iter"] * 24 * 60 * 60;

				} else {

					$lifetime = 0;

				}
				
				if (!$this->validateIterDate($lifetime, $post["itertime"], $post["timetype_itertime"])) {
					$lifetime = 24 * 60 * 60;
				}
			}

			$sql = "INSERT INTO troubles_deadline (tid, type, opening, deadline, iteration, timetype_iteration) VALUES (:tid, :type, :opening, :deadline, :iteration, :timetype_iteration)";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":tid" => $tid, ":type" => $post["type"], ":opening" => $starttime, ":deadline" => $lifetime, ":iteration" => $post["itertime"], ":timetype_iteration" => $post["timetype_itertime"]);
			$res->execute($param);

			$string = "Новая задача <a href='" . $this->registry["uri"] . "tt/show/" . $tid . "/'>" . $tid . "</a>";
			if (!is_numeric($mid)) {
				$tinfo["Текст"] = $post["task"];
			} else {
				$tinfo["Текст"] = '<iframe class="mailtext" src="' . $this->registry["siteName"] . $this->registry["uri"] . 'mail/load/?mid=' . $mid . '&part=1" frameborder="0" width="100%" height="90%"></iframe>';
			}

			$this->registry["logs"]->uid = $this->uid;
			$this->registry["logs"]->set("task", $string, $tid, $tinfo);

			if (!$mid) {
				if (isset($post["attaches"])) {
					foreach($post["attaches"] as $part) {
						$md5 = $this->getMD5($part);

						$sql = "INSERT INTO troubles_attach (`tid`, `md5`) VALUES (:tid, :md5)";
							
						$res = $this->registry['db']->prepare($sql);
						$param = array(":tid" => $tid, ":md5" => $md5);
						$res->execute($param);
					}
				}
			}
				
			return $tid;
		}
	}

	public function editTask($post, $task) {
		$tid = $post["tid"];
		
		$this->task = $this->getTask($tid);

		if ($post["task"] != '') {
			
			// Делегирование задачи
			if ( (isset($post["delegate"])) and ($post["delegate"] != null) ) {
				
				$sql = "UPDATE troubles SET who = :uid WHERE id = :tid LIMIT 1";
				
				$res = $this->registry['db']->prepare($sql);
				$param = array(":tid" => $tid, ":uid" => $post["delegate"]);
				$res->execute($param);
			}
			// END Делегирование задачи
			
			$secure = $post["secure"];

			$sql = "UPDATE troubles SET imp = :imp, secure = :secure, name = :name, text = :text, edittime = NOW(), gid = :gid WHERE id = :tid LIMIT 1";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":tid" => $tid, ":imp" => $post["imp"], ":secure" => $secure, "name" => $post["taskname"], ":text" => $post["task"], ":gid" => $post["ttgid"]);
			$res->execute($param);

			$sql = "DELETE FROM troubles_responsible WHERE tid = :tid";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":tid" => $tid);
			$res->execute($param);

			// ответственные
			if (!isset($post["ruser"])) {
				$post["ruser"] = array();
			}
			if (!isset($post["gruser"])) {
				$post["gruser"] = array();
			}
			if (!isset($post["rall"])) {
				$post["rall"] = array();
			}

			foreach($post["ruser"] as $part) {
				$sql = "INSERT INTO troubles_responsible (tid, uid) VALUES (:tid, :uid)";

				$res = $this->registry['db']->prepare($sql);
				$param = array(":tid" => $tid, ":uid" => $part);
				@$res->execute($param);
			}

			foreach($post["gruser"] as $part) {
				$sql = "INSERT INTO troubles_responsible (tid, gid) VALUES (:tid, :gid)";

				$res = $this->registry['db']->prepare($sql);
				$param = array(":tid" => $tid, ":gid" => $part);
				@$res->execute($param);
			}

			if ($post["rall"] == "1") {
				$sql = "INSERT INTO troubles_responsible (tid, `all`) VALUES (:tid, 1)";

				$res = $this->registry['db']->prepare($sql);
				$param = array(":tid" => $tid);
				@$res->execute($param);
			}
			// END ответственные
				
				
			// email move task
			$helpers = new Helpers_Helpers();
			$users = $this->registry["user"]->getUniqUsers($post);
			foreach($users as $part) {
				$user = $this->registry["user"]->getUserInfo($part);
				if ($user["email_for_task"]) {
					if ($task[0]["mail_id"] != 0) {
						$mailClass = new Model_Mail();
						$post["task"] = $mailClass->getMailText($task[0]["mail_id"]);

						$mail = $mailClass->getMailFromId($task[0]["mail_id"]);
						foreach($mail[0]["attach"] as $part) {
							$post["attaches"][] = $this->registry["rootPublic"] . "system/settings/../../" . $part["filename"];
						}

						$post["mail"] = true;
						$post["mail_id"] = $task[0]["mail_id"];
					} else {
						$post["mail"] = false;
					}
						
					$subject["method"] = "edittask";
					$subject["name"] = "OTMS";
					$subject["tid"] = $post["tid"];

					$post["uemail"] = $this->registry["ui"]["email"];
					$post["uname"] = $this->registry["ui"]["name"];
					$post["usoname"] = $this->registry["ui"]["soname"];
					$post["ugname"] = $this->registry["ui"]["gname"];
					$post["uavatar"] = base64_encode(file_get_contents($this->registry["ui"]["avatarpath"]));
						
					$helpers->sendTask($user["email"], $subject, $post);
				}
			}
			// END email move task
				

			if ($post["type"] == "0") {
				
				if ($post["startdate_global"] == "") {
					$post["startdate_global"] = date("Y-m-d");
				}
				if ($post["starttime_global"] == "") {
					$post["starttime_global"] = date("H:i:s");
				}

				$starttime = $post["startdate_global"] . " " . $post["starttime_global"];
				$lifetime = 0;
				$post["itertime"] = "";

			} elseif ($post["type"] == "1") {
				$post["itertime"] = "";
				
				if ($post["startdate_noiter"] == "") {
					$post["startdate_noiter"] = date("Y-m-d");
				}
				if ($post["starttime_noiter"] == "") {
					$post["starttime_noiter"] = date("H:i:s");
				}

				$starttime = $post["startdate_noiter"] . " " . $post["starttime_noiter"];

				if ($post["timetype_noiter"] == "min") {

					$lifetime = $post["lifetime_noiter"] * 60;

				} elseif ($post["timetype_noiter"] == "hour") {

					$lifetime = $post["lifetime_noiter"] * 60 * 60;

				} elseif ($post["timetype_noiter"] == "day") {

					$lifetime = $post["lifetime_noiter"] * 24 * 60 * 60;

				} else {

					$lifetime = 0;

				}
			} elseif ($post["type"] == "2") {
				
				if ($post["startdate_iter"] == "") {
					$post["startdate_iter"] = date("Y-m-d");
				}
				if ($post["starttime_iter"] == "") {
					$post["starttime_iter"] = date("H:i:s");
				}

				$starttime = $post["startdate_iter"] . " " . $post["starttime_iter"];

				if ($post["timetype_iter"] == "day") {

					$lifetime = $post["lifetime_iter"] * 24 * 60 * 60;

				} else {

					$lifetime = 0;

				}
				
				if (!$this->validateIterDate($lifetime, $post["itertime"], $post["timetype_itertime"])) {
					$lifetime = 24 * 60 * 60;
				}
			}

			$sql = "UPDATE troubles_deadline SET type = :type, opening = :opening, deadline = :deadline, iteration = :iteration, timetype_iteration = :timetype_iteration WHERE tid = :tid";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":tid" => $tid, ":type" => $post["type"], ":opening" => $starttime, ":deadline" => $lifetime, ":iteration" => $post["itertime"], ":timetype_iteration" => $post["timetype_itertime"]);
			$res->execute($param);

			if ($task[0]["mail_id"] == 0) {
				if (isset($post["attaches"])) {
					$sql = "DELETE FROM troubles_attach WHERE tid = :tid";
						
					$res = $this->registry['db']->prepare($sql);
					$param = array(":tid" => $tid);
					$res->execute($param);
						
					foreach($post["attaches"] as $part) {
						$md5 = $this->getMD5($part);

						$sql = "INSERT INTO troubles_attach (`tid`, `md5`) VALUES (:tid, :md5)";
							
						$res = $this->registry['db']->prepare($sql);
						$param = array(":tid" => $tid, ":md5" => $md5);
						@$res->execute($param);
					}
				}
			}
			
			$this->diffTask($post, $tid);
				
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public function diffTask($post, $tid) {
		$user = new Model_User();
		
		$tinfo = array();
		
		$oldTask = $this->task;
		$newTask = $this->getTask($tid);
		
		$old_all = array(); $old_uid = array(); $old_gid = array();
		for($i=0; $i<count($oldTask); $i++) {
			$old_all[] = $oldTask[$i]["all"];
			$old_uid[] = $oldTask[$i]["uid"];
			$old_gid[] = $oldTask[$i]["rgid"];
		}
		
		$new_all = array(); $new_uid = array(); $new_gid = array();
		for($j=0; $j<count($newTask); $j++) {
			$new_all[] = $newTask[$j]["all"];
			$new_uid[] = $newTask[$j]["uid"];
			$new_gid[] = $newTask[$j]["rgid"];
		}
		
		$del_all = array_diff($old_all, $new_all);
		foreach($del_all as $part) {
			if ($part != 0) {
				$tinfo["Ответственные все"] = "Удалены";
			}
		}
		
		$add_all = array_diff($new_all, $old_all);
		foreach($add_all as $part) {
			if ($part != 0) {
				$tinfo["Ответственные все"] = "Добавлены";
			}
		}

		$i = 0; $del_uid = array_diff($old_uid, $new_uid);
		foreach($del_uid as $part) {
			if ($part != 0) {
				$i++;
				$user = $user->getUserInfo($part);
				$tinfo["Ответственные пользователь удален [" . $i . "]"] = "<b>" . $user["name"] . " " . $user["soname"] . "</b>";
			}
		}
		
		$i = 0; $add_uid = array_diff($new_uid, $old_uid);
		foreach($add_uid as $part) {
			if ($part != 0) {
				$i++;
				$user = $user->getUserInfo($part);
				$tinfo["Ответственные пользователь добавлен [" . $i . "]"] = "<b>" . $user["name"] . " " . $user["soname"] . "</b>";
			}
		}
		
		$i = 0; $del_gid = array_diff($old_gid, $new_gid);
		foreach($del_gid as $part) {
			if ($part != 0) {
				$i++;
				$group = $user->getGroupName($part);
				$tinfo["Ответственные группа удалена [" . $i . "]"] = "<b>" . $group . "</b>";
			}
		}
		
		$i = 0; $add_gid = array_diff($new_gid, $old_gid);
		foreach($add_gid as $part) {
			if ($part != 0) {
				$i++;
				$group = $user->getGroupName($part);
				$tinfo["Ответственные группа добавлена [" . $i . "]"] = "<b>" . $group . "</b>";
			}
		}
		
		if ($oldTask[0]["text"] != $newTask[0]["text"]) {
			$tinfo["Текст"] = $newTask[0]["text"];
		}
		if ($oldTask[0]["imp"] != $newTask[0]["imp"]) {
			$tinfo["Важность"] = $oldTask[0]["imp"] . " -> " . $newTask[0]["imp"];
		}
		if ($oldTask[0]["group"] != $newTask[0]["group"]) {
			$tinfo["Группа задачи"] = $oldTask[0]["group"] . " -> " . $newTask[0]["group"];
		}
		if ($oldTask[0]["who"] != $newTask[0]["who"]) {
			$old_author = $user->getUserInfo($oldTask[0]["who"]);
			$new_author = $user->getUserInfo($newTask[0]["who"]);
			$tinfo["Делегирование"] = $old_author["name"] . " " . $old_author["soname"] . " -> " . $new_author["name"] . " " . $new_author["soname"];
		}
		if ($oldTask[0]["type"] != $newTask[0]["type"]) {
			if ($oldTask[0]["type"] == "0") {
				$old_tasktype = "глобальная";
			} else if ($oldTask[0]["type"] == "1") {
				$old_tasktype = "ограниченная по времени";
			} else if ($oldTask[0]["type"] == "2") {
				$old_tasktype = "повторяющаяся";
			}
			
			if ($newTask[0]["type"] == "0") {
				$new_tasktype = "глобальная";
			} else if ($newTask[0]["type"] == "1") {
				$new_tasktype = "ограниченная по времени";
			} else if ($newTask[0]["type"] == "2") {
				$new_tasktype = "повторяющаяся";
			}
			
			$tinfo["Тип задачи"] = $old_tasktype . " -> " . $new_tasktype;
		}
		if ($oldTask[0]["openingF"] != $newTask[0]["openingF"]) {
			$tinfo["Дата начала"] = $oldTask[0]["openingF"] . " -> " . $newTask[0]["openingF"];
		}
		if ($oldTask[0]["endingF"] != $newTask[0]["endingF"]) {
			$tinfo["Дата завершения"] = $oldTask[0]["endingF"] . " -> " . $newTask[0]["endingF"];
		}

		if (count($tinfo) == 0) {
			return false;
		}

		$string = "Правка задачи <a href='" . $this->registry["uri"] . "tt/show/" . $tid . "/'>" . $tid . "</a>";
		$this->registry["logs"]->uid = $this->registry["ui"]["id"];
		$this->registry["logs"]->set("task", $string, $tid, $tinfo);

		return true;
	}

	public function getNumComments($tid) {
		$sql = "SELECT COUNT(id) AS count FROM troubles_discussion WHERE tid = :tid";
			
		$res = $this->registry['db']->prepare($sql);
		$res->execute(array(":tid" => $tid));
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		return $data[0]["count"];
	}

	public function getComments($tid) {
		$data = array();

		$sql = "SELECT td.id, td.uid, td.object, td.mail_id, td.sendmail, td.text, cs.id AS status_id, cs.status, td.timestamp AS `timestamp`, td.remote AS `remote`
        FROM troubles_discussion AS td
        LEFT JOIN comments_status AS cs ON (cs.id = td.status)
        WHERE td.tid = :tid
        ORDER BY td.id";
			
		$res = $this->registry['db']->prepare($sql);
		$res->execute(array(":tid" => $tid));
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		for($i=0; $i<count($data); $i++) {
			$data[$i]["date"] = $data[$i]["timestamp"];
			$data[$i]["timestamp"] = date("YmdHis", strtotime($data[$i]["timestamp"]));
			$data[$i]["fdate"] = $this->editDate($data[$i]["timestamp"]);
				
			if ($data[$i]["remote"] == "1") {
				$sql = "SELECT ma.filename
	        	FROM mail_attach AS ma
	        	WHERE ma.tdid = :tdid";
			} else {
				$sql = "SELECT fs.filename
	        	FROM troubles_discussion_attach AS tda
	       		LEFT JOIN fm_fs AS fs ON (fs.md5 = tda.md5)
	        	WHERE tda.tdid = :tdid";
			}
				
			$res = $this->registry['db']->prepare($sql);
			$res->execute(array(":tdid" => $data[$i]["id"]));
			$attaches = $res->fetchAll(PDO::FETCH_ASSOC);
				
			$data[$i]["attaches"] = $attaches;
				
			if ($data[$i]["remote"]) {
				$data[$i]["ui"] = $this->registry["tt_user"]->getRemoteUserInfo($data[$i]["uid"]);
			} else {
				$data[$i]["ui"] = $this->registry["user"]->getUserInfo($data[$i]["uid"]);
			}
				
			if ($data[$i]["mail_id"] != 0) {
				$mailClass = new Model_Mail();
				$data[$i]["text"] = $mailClass->getMailFromId($data[$i]["mail_id"]);
			}
		}

		return $data;
	}

	public function getOidTasks($oid, $limit = false) {
		$data = array();

		if (!$limit) {
			$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT(t.id)
	        FROM troubles AS t
	        LEFT JOIN troubles_responsible AS tr ON (tr.tid = t.id)
	        LEFT JOIN troubles_composite AS tc ON (tc.tid = t.id)
	        LEFT JOIN troubles_objects AS t_o ON (t_o.tid = t.id)
	        WHERE ( (t.secure = 0) OR ( (t.secure = 1) AND (t.who = :uid OR tr.uid = :uid OR tr.all = 1 OR tr.gid = :gid) ) )
	            AND t_o.oid = :oid
	        ORDER BY t.id DESC
	        LIMIT " . $this->startRow .  ", " . $this->limit;
		} else {
			$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT(t.id)
	        FROM troubles AS t
	        LEFT JOIN troubles_responsible AS tr ON (tr.tid = t.id)
	        LEFT JOIN troubles_composite AS tc ON (tc.tid = t.id)
	        LEFT JOIN troubles_objects AS t_o ON (t_o.tid = t.id)
	        WHERE ( (t.secure = 0) OR ( (t.secure = 1) AND (t.who = :uid OR tr.uid = :uid OR tr.all = 1 OR tr.gid = :gid) ) )
	            AND t_o.oid = :oid
	        ORDER BY t.id DESC";
		}

		$res = $this->registry['db']->prepare($sql);
		$param = array(":uid" => $this->registry["ui"]["id"], ":gid" => $this->registry["ui"]["group"], "oid" => $oid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		$this->totalPage = $this->registry['db']->query("SELECT FOUND_ROWS()")->fetchColumn();

		//Если общее число статей больше показанного, вызовем пейджер
		if ($this->totalPage < $this->limit+1)  {
		} else {
			$this->Pager();
		}

		return $data;
	}

	public function getMeTasks() {
		$sortmytt = & $_SESSION["sortmytt"];

		$sort = $sortmytt["sort"];
		$id = $sortmytt["id"];
		 
		if ($sort == "group") {
			if ($id == 'false') {
				$where = "";
				$sql_inc = "ORDER BY t.gid, t.imp DESC, t.id DESC";
			} else {
				$where = "AND t.gid = " . $id;
				$sql_inc = "ORDER BY t.gid, t.imp DESC, t.id DESC";
			}
		} elseif ($sort == "obj") {
			if ($id == 'false') {
				$where = "";
				$sql_inc = "ORDER BY t_o.oid DESC, t.imp DESC, t.id DESC";
			} else {
				$where = "AND t_o.oid = " . $id;
				$sql_inc = "ORDER BY t_o.oid DESC, t.imp DESC, t.id DESC";
			}
		} elseif ($sort == "imp") {
			if ($id == 'false') {
				$where = "";
				$sql_inc = "ORDER BY t.imp DESC, t.id DESC";
			} else {
				$where = "AND t.imp = " . $id;
				$sql_inc = "ORDER BY t.imp DESC, t.id DESC";
			}
		} elseif ($sort == "type") {
			if ($id == 'false') {
				$where = "";
				$sql_inc = "ORDER BY td.type DESC, t.imp DESC, t.id DESC";
			} else {
				$where = "AND td.type = " . $id;
				$sql_inc = "ORDER BY td.type DESC, t.imp DESC, t.id DESC";
			}
		} elseif ($sort == "date") {
			if ($id == 'false') {
				$where = "";
				$sql_inc = "ORDER BY t.id DESC";
			}
		}

		$data = array();

		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT(t.id)
        FROM troubles AS t
        LEFT JOIN troubles_responsible AS tr ON (tr.tid = t.id)
        LEFT JOIN troubles_deadline AS td ON (td.tid = t.id)
        LEFT JOIN troubles_composite AS tc ON (tc.tid = t.id)
        LEFT JOIN troubles_objects AS t_o ON (t_o.tid = t.id)
        WHERE t.who = :uid
            AND t.close = 0
            " . $where . "
        " . $sql_inc . "
        LIMIT " . $this->startRow .  ", " . $this->limit;

		$res = $this->registry['db']->prepare($sql);
		$param = array(":uid" => $this->registry["ui"]["id"]);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		$this->totalPage = $this->registry['db']->query("SELECT FOUND_ROWS()")->fetchColumn();

		//Если общее число статей больше показанного, вызовем пейджер
		if ($this->totalPage < $this->limit+1)  {
		} else {
			$this->Pager();
		}

		return $data;
	}

	public function getNumStatTasks() {
		$caltype = & $_SESSION["cal"];
		if (!isset($caltype["type"])) {
			$caltype["type"] = 0;
		}

		if ($caltype["type"] == 1) {
			$sql_type = "WHERE t.who = " . $this->registry["ui"]["id"];
		} else {
			$sql_type = "WHERE (tr.uid = " . $this->registry["ui"]["id"] . " OR tr.all = 1 OR tr.gid = " . $this->registry["ui"]["group"] . ")";
		}

		$sql = "SELECT COUNT(DISTINCT t.id) AS count
        FROM troubles AS t
        LEFT JOIN troubles_responsible AS tr ON (tr.tid = t.id)
        " . $sql_type . "
            AND t.close = 0";
			
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		return $data[0]["count"];
	}

	public function getNumMeTasks() {
		$sql = "SELECT COUNT(DISTINCT id) AS count FROM troubles WHERE who = :uid AND close = 0";
			
		$res = $this->registry['db']->prepare($sql);
		$res->execute(array(":uid" => $this->registry["ui"]["id"]));
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		return $data[0]["count"];
	}

	public function getNumTasks() {
		$user = new Model_User();

		$data = array(); $result = array();

		$year = date("Y");
		$month = date("m");
		$day = date("d");

		$sql = "SELECT DISTINCT(t.id), td.type, td.deadline, td.iteration, td.timetype_iteration, td.opening
        FROM troubles AS t
        LEFT JOIN troubles_responsible AS tr ON (tr.tid = t.id)
        LEFT JOIN troubles_deadline AS td ON (td.tid = t.id)
        WHERE ( ( (t.secure = 0) AND (tr.uid = :uid OR tr.all = 1 OR tr.gid = :gid) ) OR ( (t.secure = 1) AND (tr.uid = :uid OR tr.all = 1 OR tr.gid = :gid) ) )
            AND t.close = 0
            AND td.opening <= NOW()
        ORDER BY t.id DESC";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":uid" => $this->registry["ui"]["id"], ":gid" => $this->registry["ui"]["gid"]);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		for($i=0; $i<count($data); $i++) {
			$start = strtotime($data[$i]["opening"]);
			if (($days = $data[$i]["deadline"] / 60 / 60 / 24) < 1) {
				$days = 1;
			}

			if ($data[$i]["type"] == "0" or $data[$i]["type"] == "1") {
				$curDay = date("d", mktime(0, 0, 0, date("m", $start), date("d", $start), date("Y", $start)));
				$curMonth = date("m", mktime(0, 0, 0, date("m", $start), date("d", $start), date("Y", $start)));
				$curYear = date("Y", mktime(0, 0, 0, date("m", $start), date("d", $start), date("Y", $start)));

				if ($curYear . $curMonth . $curDay <= $year . $month . $day) {
					$result[]["id"] = $data[$i]["id"];
				}
			} elseif ($data[$i]["type"] == "2") {
				$inc_day = 0;
				$inc_month = 0;
				if ($data[$i]["iteration"] != 0) {
					$inc = $data[$i]["iteration"];
				} else {
					$inc = $days;
				}
				$inc_type = $data[$i]["timetype_iteration"];
				
				$curYear = date("Y", $start);
				$curMonth = date("m", $start);

				while( ($curYear <= $year) ) {
					for($l=0; $l<$days; $l++) {
						$curDay = date("j", mktime(0, 0, 0, date("m", $start) + $inc_month, date("d", $start) + $l + $inc_day, date("Y", $start)));
						$curMonth = date("m", mktime(0, 0, 0, date("m", $start) + $inc_month, date("d", $start) + $l + $inc_day, date("Y", $start)));
						$curYear = date("Y", mktime(0, 0, 0, date("m", $start) + $inc_month, date("d", $start) + $l + $inc_day, date("Y", $start)));
						 
						if ( ($curYear == $year) and ($curMonth == $month)  and ($curDay == $day) ) {
							$result[]["id"] = $data[$i]["id"];
						}
					}

					if ($inc_type == "day") {
						$inc_day = $inc_day + $inc;
					} elseif($inc_type == "month") {
						$inc_month = $inc_month + $inc;
					}
				}
			}
		}

		return count($result);
	}

	public function getIterTasks() {
		$data = array();

		$caltype = & $_SESSION["cal"];
		if (!isset($caltype["type"])) {
			$caltype["type"] = 0;
		}

		if ($caltype["type"] == 1) {
			$sql_type = "WHERE t.who = " . $this->registry["ui"]["id"];
		} else {
			$sql_type = "WHERE (tr.uid = " . $this->registry["ui"]["id"] . " OR tr.all = 1 OR tr.gid = " . $this->registry["ui"]["group"] . ")";
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT(t.id)
        FROM troubles AS t
        LEFT JOIN troubles_responsible AS tr ON (tr.tid = t.id)
        LEFT JOIN troubles_deadline AS td ON (td.tid = t.id)
        LEFT JOIN troubles_composite AS tc ON (tc.tid = t.id)
        " . $sql_type . "
            AND td.type = 2
            AND t.close = 0
        ORDER BY t.id DESC
        LIMIT " . $this->startRow .  ", " . $this->limit;

		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		$this->totalPage = $this->registry['db']->query("SELECT FOUND_ROWS()")->fetchColumn();

		//Если общее число статей больше показанного, вызовем пейджер
		if ($this->totalPage < $this->limit+1)  {
		} else {
			$this->Pager();
		}

		return $data;
	}

	public function getNumIterTasks() {
		$caltype = & $_SESSION["cal"];
		if (!isset($caltype["type"])) {
			$caltype["type"] = 0;
		}

		if ($caltype["type"] == 1) {
			$sql_type = "WHERE t.who = " . $this->registry["ui"]["id"];
		} else {
			$sql_type = "WHERE (tr.uid = " . $this->registry["ui"]["id"] . " OR tr.all = 1 OR tr.gid = " . $this->registry["ui"]["group"] . ")";
		}

		$sql = "SELECT COUNT(DISTINCT t.id) AS count
        FROM troubles AS t
        LEFT JOIN troubles_deadline AS td ON (td.tid = t.id)
        LEFT JOIN troubles_responsible AS tr ON (tr.tid = t.id)
        " . $sql_type . "
            AND t.close = 0
            AND td.type = 2";	
			
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		return $data[0]["count"];
	}

	public function getTimeTasks() {
		$data = array();

		$caltype = & $_SESSION["cal"];
		if (!isset($caltype["type"])) {
			$caltype["type"] = 0;
		}

		if ($caltype["type"] == 1) {
			$sql_type = "WHERE t.who = " . $this->registry["ui"]["id"];
		} else {
			$sql_type = "WHERE (tr.uid = " . $this->registry["ui"]["id"] . " OR tr.all = 1 OR tr.gid = " . $this->registry["ui"]["group"] . ")";
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT(t.id)
        FROM troubles AS t
        LEFT JOIN troubles_responsible AS tr ON (tr.tid = t.id)
        LEFT JOIN troubles_deadline AS td ON (td.tid = t.id)
        LEFT JOIN troubles_composite AS tc ON (tc.tid = t.id)
        " . $sql_type . "
            AND td.type = 1
            AND t.close = 0
        ORDER BY t.id DESC
        LIMIT " . $this->startRow .  ", " . $this->limit;

		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		$this->totalPage = $this->registry['db']->query("SELECT FOUND_ROWS()")->fetchColumn();

		//Если общее число статей больше показанного, вызовем пейджер
		if ($this->totalPage < $this->limit+1)  {
		} else {
			$this->Pager();
		}

		return $data;
	}

	public function getNumTimeTasks() {
		$caltype = & $_SESSION["cal"];
		if (!isset($caltype["type"])) {
			$caltype["type"] = 0;
		}

		if ($caltype["type"] == 1) {
			$sql_type = "WHERE t.who = " . $this->registry["ui"]["id"];
		} else {
			$sql_type = "WHERE (tr.uid = " . $this->registry["ui"]["id"] . " OR tr.all = 1 OR tr.gid = " . $this->registry["ui"]["group"] . ")";
		}

		$sql = "SELECT COUNT(DISTINCT t.id) AS count
        FROM troubles AS t
        LEFT JOIN troubles_deadline AS td ON (td.tid = t.id)
        LEFT JOIN troubles_responsible AS tr ON (tr.tid = t.id)
        " . $sql_type . "
            AND t.close = 0
            AND td.type = 1";
			
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		return $data[0]["count"];
	}

	public function getNoiterTasks() {
		$data = array();

		$caltype = & $_SESSION["cal"];
		if (!isset($caltype["type"])) {
			$caltype["type"] = 0;
		}

		if ($caltype["type"] == 1) {
			$sql_type = "WHERE t.who = " . $this->registry["ui"]["id"];
		} else {
			$sql_type = "WHERE (tr.uid = " . $this->registry["ui"]["id"] . " OR tr.all = 1 OR tr.gid = " . $this->registry["ui"]["group"] . ")";
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT(t.id)
        FROM troubles AS t
        LEFT JOIN troubles_responsible AS tr ON (tr.tid = t.id)
        LEFT JOIN troubles_deadline AS td ON (td.tid = t.id)
        LEFT JOIN troubles_composite AS tc ON (tc.tid = t.id)
        " . $sql_type . "
            AND td.type = 0
            AND t.close = 0
        ORDER BY t.id DESC
        LIMIT " . $this->startRow .  ", " . $this->limit;

		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		$this->totalPage = $this->registry['db']->query("SELECT FOUND_ROWS()")->fetchColumn();

		//Если общее число статей больше показанного, вызовем пейджер
		if ($this->totalPage < $this->limit+1)  {
		} else {
			$this->Pager();
		}

		return $data;
	}

	public function getMonthTasks($year, $month, $uid = false) {
		$data = array(); $result = array();
		$user = new Model_User();
		
		if (!$uid) {
			$uid = $this->registry["ui"]["id"];
			$gid = $user->getGidFromUid($uid);
		} else {
			$gid = $this->registry["ui"]["group"];
		}

		$caltype = & $_SESSION["cal"];
		if (!isset($caltype["type"])) {
			$caltype["type"] = 0;
		}

		if ($caltype["type"] == 1) {
			$sql_type = "WHERE t.who = " . $uid;
		} else {
			$sql_type = "WHERE tr.uid = " . $uid . " OR tr.all = 1 OR tr.gid = " . $gid;
		}

		for($i=0; $i<=31; $i++) {
			$result[$i]["close"]["num"] = 0;
			$result[$i]["time"]["num"] = 0;
			$result[$i]["iter"]["num"] = 0;
			$result[$i]["noiter"]["num"] = 0;
		}

		$sql = "SELECT DISTINCT(t.id), t.close, td.type, td.deadline, td.iteration, td.timetype_iteration, td.opening, t.ending
        FROM troubles AS t 
        LEFT JOIN troubles_deadline AS td ON (td.tid = t.id)
        LEFT JOIN troubles_responsible AS tr ON (tr.tid = td.tid)
        " . $sql_type . "
        ORDER BY td.opening";

		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		for($i=0; $i<count($data); $i++) {

			$start = strtotime($data[$i]["opening"]);
			$end = strtotime($data[$i]["ending"]);
			if (($days = $data[$i]["deadline"] / 60 / 60 / 24) < 1) {
				$days = 1;
			}

			if ($data[$i]["close"] == 1) {
				$curDay = date("j", mktime(0, 0, 0, date("m", $end), date("d", $end), date("Y", $end)));
				$curMonth = date("m", mktime(0, 0, 0, date("m", $end), date("d", $end), date("Y", $end)));
				$curYear = date("Y", mktime(0, 0, 0, date("m", $end), date("d", $end), date("Y", $end)));
			
				if ( ($curYear == $year) and ($curMonth == $month) ) {
					$result[$curDay]["close"]["num"]++;
				}
			} elseif ($data[$i]["type"] == "0") {
				$cur_month_num_days = date("t", mktime(0, 0, 0, $month, "1", $year));
				
				$date1 = round(date("U", mktime(0, 0, 0, date("m", $start), date("d", $start), date("Y", $start))) / 60 / 60 / 24);
				$date2_start = round(date("U", mktime(0, 0, 0, $month, "1", $year)) / 60 / 60 / 24);
				$date2_end = round(date("U", mktime(0, 0, 0, $month, $cur_month_num_days, $year)) / 60 / 60 / 24);
				
				if ($date2_end > $date1) {
					if ($date2_start < $date1) {
						$start_date = 0;
					} else {
						$start_date = $date2_end - $date1 - $cur_month_num_days;
					}
					$end = $start_date + $cur_month_num_days;
				} else {
					$start_date = 0;
					$end = 0;
				}
				
				for($l=$start_date; $l<=$end; $l++) {
					$curDay = date("j", mktime(0, 0, 0, date("m", $start), date("d", $start) + $l, date("Y", $start)));
					$curMonth = date("m", mktime(0, 0, 0, date("m", $start), date("d", $start) + $l, date("Y", $start)));
					$curYear = date("Y", mktime(0, 0, 0, date("m", $start), date("d", $start) + $l, date("Y", $start)));
					if ( ($curYear == $year) and ($curMonth == $month) ) {
						$result[$curDay]["noiter"]["num"]++;
					}
				}
			} elseif ($data[$i]["type"] == "1") {
				for($l=0; $l<$days; $l++) {
					$curDay = date("j", mktime(0, 0, 0, date("m", $start), date("d", $start) + $l, date("Y", $start)));
					$curMonth = date("m", mktime(0, 0, 0, date("m", $start), date("d", $start) + $l, date("Y", $start)));
					$curYear = date("Y", mktime(0, 0, 0, date("m", $start), date("d", $start) + $l, date("Y", $start)));
					if ( ($curYear == $year) and ($curMonth == $month) ) {
						$result[$curDay]["time"]["num"]++;
					}
				}
			} elseif ($data[$i]["type"] == "2") {
				$curYear = date("Y", $start);
				$curMonth = date("m", $start);
				$inc_day = 0;
				$inc_month = 0;
				if ($data[$i]["iteration"] != '0') {
					$inc = $data[$i]["iteration"];
				} else {
					$inc = $days;
				}
				$inc_type = $data[$i]["timetype_iteration"];

				while( ($curYear <= $year) ) {
					for($l=0; $l<$days; $l++) {
						$curDay = date("j", mktime(0, 0, 0, date("m", $start) + $inc_month, date("d", $start) + $l + $inc_day, date("Y", $start)));
						$curMonth = date("m", mktime(0, 0, 0, date("m", $start) + $inc_month, date("d", $start) + $l + $inc_day, date("Y", $start)));
						$curYear = date("Y", mktime(0, 0, 0, date("m", $start) + $inc_month, date("d", $start) + $l + $inc_day, date("Y", $start)));

						if ( ($curYear == $year) and ($curMonth == $month) ) {
							$result[$curDay]["iter"]["num"]++;
						}
					}

					if ($inc_type == "day") {
						$inc_day = $inc_day + $inc;
					} elseif($inc_type == "month") {
						$inc_month = $inc_month + $inc;
					}
				}
			}
		}

		return $result;
	}

	public function issetTaskFromMail($from) {
		$sql = "SELECT t.id
		FROM troubles AS t
		LEFT JOIN `mail` AS m ON (m.id = t.mail_id)
		WHERE m.email = :from AND t.close = 0
		LIMIT 1";

		$res = $this->registry['db']->prepare($sql);
		$res->execute(array(":from" => $from));
		$data1 = $res->fetchAll(PDO::FETCH_ASSOC);

		$sql = "SELECT t.id
		FROM mail_contacts AS mc
		LEFT JOIN troubles_objects AS t_o ON (t_o.oid = mc.oid)
		LEFT JOIN troubles AS t ON (t_o.tid = t.id)		
		WHERE mc.email = :from AND t.close = 0
		LIMIT 1";

		$res = $this->registry['db']->prepare($sql);
		$res->execute(array(":from" => $from));
		$data2 = $res->fetchAll(PDO::FETCH_ASSOC);

		$data = array_merge_recursive($data1, $data2);

		if ( (isset($data[0]["id"])) and ($data[0]["id"] > 0) ) {
			return $data[0]["id"];
		}
	}

	public function issetTaskFromMid($mid) {
		$sql = "SELECT t.id
		FROM troubles AS t
		LEFT JOIN `mail` AS m ON (m.id = t.mail_id)
		WHERE m.id = :mid AND t.close = 0
		LIMIT 1";

		$res = $this->registry['db']->prepare($sql);
		$res->execute(array(":mid" => $mid));
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		if ( (isset($data[0]["id"])) and ($data[0]["id"] > 0) ) {
			return $data[0]["id"];
		}
	}
	
	public function addCommentFromUid($tid, $uid, $text) {
		$task = $this->getTask($tid);
		
		if (!$task[0]["close"]) {
			$sql = "INSERT INTO troubles_discussion (tid, uid, text, object, status) VALUES (:tid, :uid, :text, :object, :status)";
			
			$res = $this->registry['db']->prepare($sql);
			$res->execute(array(":tid" => $tid, ":uid" => $uid, ":text" => $text, ":object" => "1", ":status" => ""));
			
			$string = "Новый комментарий к задаче <a href='" . $this->registry["uri"] . "tt/show/" . $tid . "/'>" . $tid . "</a>";
			
			$tinfo["Текст"] = $text;
				
			$this->registry["logs"]->uid = $uid;
			$this->registry["logs"]->set("com", $string, $tid, $tinfo);
		}
	}

	public function addComment($tid, $text, $status, $attaches = false, $mid = false) {
		$helpers = new Helpers_Helpers();

		if (is_numeric($mid)) {
			$sql = "INSERT INTO troubles_discussion (tid, uid, mail_id, text, status) VALUES (:tid, :uid, :mail_id, :text, :status)";

			$res = $this->registry['db']->prepare($sql);
			$res->execute(array(":tid" => $tid, ":uid" => $this->uid, ":mail_id" => $mid, ":text" => "", ":status" => $status));
		} else {
			$sql = "INSERT INTO troubles_discussion (tid, uid, text, status) VALUES (:tid, :uid, :text, :status)";

			$res = $this->registry['db']->prepare($sql);
			$res->execute(array(":tid" => $tid, ":uid" => $this->uid, ":text" => $text, ":status" => $status));
		}

		$tdid = $this->registry['db']->lastInsertId();

		if (isset($attaches["attaches"])) {
			foreach($attaches["attaches"] as $part) {
				$md5 = $this->getMD5($part);

				$sql = "INSERT INTO troubles_discussion_attach (`tdid`, `md5`) VALUES (:tdid, :md5)";

				$res = $this->registry['db']->prepare($sql);
				$param = array(":tdid" => $tdid, ":md5" => $md5);
				$res->execute($param);
			}
		}

		// email move task
		$post = $this->getTask($tid);

		if ($post[0]["remote_id"] != 0) {
			$subject = array(); $body = array();
				
			$user = $this->registry["tt_user"]->getRemoteUserInfo($post[0]["who"]);
				
			$subject["method"] = "comment";
			$subject["name"] = "OTMS";
			$subject["tid"] = $post[0]["remote_id"];
			$subject["rc"] = true;

			$body["uemail"] = $this->registry["ui"]["email"];
			$body["uname"] = $this->registry["ui"]["name"];
			$body["usoname"] = $this->registry["ui"]["soname"];
			$body["ugname"] = $this->registry["ui"]["gname"];
			$body["uavatar"] = base64_encode(file_get_contents($this->registry["ui"]["avatarpath"]));
			$body["attaches"] = $attaches["attaches"];
				
			$body["text"] = $text;
			$body["status"] = $status;
				
			$helpers->sendTask($user["email"], $subject, $body);
		} else {
			$data["rall"] = 0;
			$data["gruser"] = array();
			$data["ruser"] = array();
				
			foreach($post as $part) {
				if ($part["all"] == 1) {
					$data["rall"] = 1;
				}

				if ($part["rgid"] != 0) {
					$data["gruser"][] = $part["rgid"];
				}
				if ($part["uid"] != 0) {
					$data["ruser"][] = $part["uid"];
				}
			}
				
			$users = $this->registry["user"]->getUniqUsers($data);
			foreach($users as $part) {
				$subject = array(); $body = array();
				$user = $this->registry["user"]->getUserInfo($part);
				if ($user["email_for_task"]) {
					if (is_numeric($mid)) {
						$mailClass = new Model_Mail();
						$text = $mailClass->getMailText($mid);

						$mail = $mailClass->getMailFromId($mid);
						foreach($mail[0]["attach"] as $part) {
							$attaches["attaches"][] = $this->registry["rootPublic"] . "system/settings/../../" . $part["filename"];
						}

						$body["mail"] = true;
						$body["mail_id"] = $mid;
					} else {
						$body["mail"] = false;
					}
						
					$subject["method"] = "comment";
					$subject["name"] = "OTMS";
					$subject["tid"] = $tid;
					$subject["rc"] = false;

					$body["uemail"] = $this->registry["ui"]["email"];
					$body["uname"] = $this->registry["ui"]["name"];
					$body["usoname"] = $this->registry["ui"]["soname"];
					$body["ugname"] = $this->registry["ui"]["gname"];
					$body["uavatar"] = base64_encode(file_get_contents($this->registry["ui"]["avatarpath"]));
					$body["attaches"] = $attaches["attaches"];
						
					$body["text"] = $text;
					$body["status"] = $status;

					$helpers->sendTask($user["email"], $subject, $body);
				}
			}
		}
		// END email move task
			
		$string = "Новый комментарий к задаче <a href='" . $this->registry["uri"] . "tt/show/" . $tid . "/'>" . $tid . "</a>";

		if ($status != 0) {
			$status_text = $this->getCommentStatusText($status);
			$tinfo["Статус"] = "<span style='padding: 2px 4px' class='info'>" . $status_text . "</span>";
		}
		if ($mid) {
			$tinfo["Текст"] = '<iframe class="mailtext" src="' . $this->registry["siteName"] . $this->registry["uri"] . 'mail/load/?mid=' . $mid . '&part=1" frameborder="0" width="100%" height="90%"></iframe>';
		} else {
			$tinfo["Текст"] = $text;
		}
		 
		$this->registry["logs"]->uid = $this->uid;
		$this->registry["logs"]->set("com", $string, $tid, $tinfo);
	}

	public function closeTask($tid, $uid = null) {
		if ($uid == null) {
			$uid = $this->registry["ui"]["id"];
		}
		
		$sql = "SELECT close FROM troubles WHERE id = :tid LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$res->execute(array(":tid" => $tid));
		$close = $res->fetchAll(PDO::FETCH_ASSOC);
		
		if ($close[0]["close"] == 0) {
			$sql = "UPDATE troubles SET ending = NOW(), close = 1, cuid = :cuid WHERE id = :tid LIMIT 1";
	
			$res = $this->registry['db']->prepare($sql);
			$res->execute(array(":tid" => $tid, ":cuid" => $uid));
	
			// email move task
			$post = $this->getTask($tid);
	
			$data["rall"] = 0;
			$data["gruser"] = array();
			$data["ruser"] = array();
	
			foreach($post as $part) {
				if ($part["all"] == 1) {
					$data["rall"] = 1;
				}
	
				if ($part["rgid"] != 0) {
					$data["gruser"][] = $part["rgid"];
				}
				if ($part["uid"] != 0) {
					$data["ruser"][] = $part["uid"];
				}
			}
	
			$helpers = new Helpers_Helpers();
			$users = $this->registry["user"]->getUniqUsers($data);
			foreach($users as $part) {
				$user = $this->registry["user"]->getUserInfo($part);
				if ($user["email_for_task"]) {
	
					$subject["method"] = "closetask";
					$subject["name"] = "OTMS";
					$subject["tid"] = $tid;
					$subject["from"] = $this->registry["ui"]["email"];
	
					$helpers->sendTask($user["email"], $subject);
				}
			}
			// END email move task
	
			$task = $this->getTask($tid);
			$tinfo = array();
			$string = "Завершена задача <a href='" . $this->registry["uri"] . "tt/show/" . $tid . "/'>" . $tid . "</a>";

			$this->registry["logs"]->uid = $uid;
			$this->registry["logs"]->set("task", $string, $tid, $tinfo);
		}
	}

	public function spamUsers($theme, $tid) {
		$user = new Model_User();
		
		$helpers = new Helpers_Helpers();

		$mailClass = new Model_Mail();

		$data1 = array(); $data = array(); $i = 0; $flag = TRUE;

		$sql = "SELECT tr.uid AS `uid`, users.email, users.notify, tr.gid AS `gid`, tr.all AS `all`
        FROM troubles_responsible AS tr
        LEFT JOIN users ON (users.id = tr.uid)
        WHERE tr.tid = :tid";

		$res = $this->registry['db']->prepare($sql);
		$param = array("tid" => $tid);
		$res->execute($param);
		$resp = $res->fetchAll(PDO::FETCH_ASSOC);

		if (count($resp) > 0) {
			foreach($resp as $part) {
				if ($part["all"]) {
					$flag = FALSE;

					$rusers = array();

					$allusers = $user->getUsersList();

					foreach($allusers AS $uid) {
						$data1[$i]["uid"] = $uid["id"];
						$data1[$i]["email"] = $uid["email"];
						$data1[$i]["notify"] = $uid["notify"];

						$i++;
					}
				}

				if (($part["gid"] != 0) and ($flag)) {
					$gusers = $user->getUserInfoFromGroup($part["gid"]);

					foreach($gusers AS $uid) {
						$data1[$i]["uid"] = $uid["uid"];
						$data1[$i]["email"] = $uid["email"];
						$data1[$i]["notify"] = $uid["notify"];

						$i++;
					}
				}

				if (($part["uid"] != 0) and ($flag)) {
					$data1[$i]["uid"] = $part["uid"];
					$data1[$i]["email"] = $part["email"];
					$data1[$i]["notify"] = $part["notify"];

					$i++;
				}
			}
		}

		$sql = "SELECT DISTINCT(ts.uid) AS uid, users.email, ts.id AS `spam`
        FROM troubles_spam AS ts
        LEFT JOIN users ON (users.id = ts.uid)
        WHERE ts.tid = :tid
        ORDER BY ts.uid DESC";

		$res = $this->registry['db']->prepare($sql);
		$param = array("tid" => $tid);
		$res->execute($param);
		$data2 = $res->fetchAll(PDO::FETCH_ASSOC);

		$data = array_merge($data1, $data2);
		$i = 0; $users = array();
		foreach($data as $part) {
			$flag = true;
			for($k=0; $k<count($users); $k++) {
				if ($users[$k]["uid"] == $part["uid"]) {
					$flag = false;
				}
			}

			if ($flag) {
				if ( ( (isset($part["notify"])) and ($part["notify"]) ) or (isset($part["spam"])) ) {
					$users[$i]["uid"] = $part["uid"];
					$users[$i]["email"] = $part["email"];
				}

				$i++;
			}
		}

		$task = $this->getTask($tid);
		if ($task[0]["mail_id"] != 0) {
			$task[0]["text"] = $mailClass->getMailText($task[0]["mail_id"]);
		}

		$comments = $this->getComments($tid);

		foreach($users as $part) {
			$helpers->sendMail($part["email"], $theme, $task, $comments);
		}
	}

	public function addDraft($oid, $post) {
		$secure = $post["secure"];

		$sql = "INSERT INTO draft (who, imp, secure, name, text, gid) VALUES (:who, :imp, :secure, :name, :text, :gid)";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":who" => $this->registry["ui"]["id"], ":imp" => $post["imp"], ":secure" => $secure, "name" => $post["taskname"], ":text" => $post["task"], ":gid" => $post["ttgid"]);
		$res->execute($param);

		$tid = $this->registry['db']->lastInsertId();
		
		$sql = "INSERT INTO draft_objects (tid, oid) VALUES (:tid, :oid)";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":oid" => $oid, ":tid" => $tid);
		$res->execute($param);
		
		if (isset($post["sub"])) {
			$sql = "INSERT INTO draft_composite (cid, tid) VALUES (:cid, :tid)";
		
			$res = $this->registry['db']->prepare($sql);
			$param = array(":cid" => $post["sub"], ":tid" => $tid);
			$res->execute($param);
		}

		// ответственные
		if (!isset($post["ruser"])) {
			$post["ruser"] = array();
		}
		if (!isset($post["gruser"])) {
			$post["gruser"] = array();
		}
		if (!isset($post["rall"])) {
			$post["rall"] = array();
		}

		foreach($post["ruser"] as $part) {
			$sql = "INSERT INTO draft_responsible (tid, uid) VALUES (:tid, :uid)";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":tid" => $tid, ":uid" => $part);
			@$res->execute($param);
		}

		foreach($post["gruser"] as $part) {
			$sql = "INSERT INTO draft_responsible (tid, gid) VALUES (:tid, :gid)";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":tid" => $tid, ":gid" => $part);
			@$res->execute($param);
		}

		if ($post["rall"] == "1") {
			$sql = "INSERT INTO draft_responsible (tid, `all`) VALUES (:tid, 1)";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":tid" => $tid);
			@$res->execute($param);
		}
		// END ответственные


		if ($post["type"] == "0") {

			$starttime = $post["startdate_global"] . " " . $post["starttime_global"];
			$lifetime = 0;
			$post["itertime"] = "";

		} elseif ($post["type"] == "1") {
			$post["itertime"] = "";

			$starttime = $post["startdate_noiter"] . " " . $post["starttime_noiter"];

			if ($post["timetype_noiter"] == "min") {

				$lifetime = $post["lifetime_noiter"] * 60;

			} elseif ($post["timetype_noiter"] == "hour") {

				$lifetime = $post["lifetime_noiter"] * 60 * 60;

			} elseif ($post["timetype_noiter"] == "day") {

				$lifetime = $post["lifetime_noiter"] * 24 * 60 * 60;

			} else {

				$lifetime = 0;

			}
		} elseif ($post["type"] == "2") {

			$starttime = $post["startdate_iter"] . " " . $post["starttime_iter"];

			if ($post["timetype_iter"] == "day") {

				$lifetime = $post["lifetime_iter"] * 24 * 60 * 60;

			} else {

				$lifetime = 0;

			}
			
			if (!$this->validateIterDate($lifetime, $post["itertime"], $post["timetype_itertime"])) {
					$lifetime = 24 * 60 * 60;
			}
		}

		$sql = "INSERT INTO draft_deadline (tid, type, opening, deadline, iteration, timetype_iteration) VALUES (:tid, :type, :opening, :deadline, :iteration, :timetype_iteration)";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":tid" => $tid, ":type" => $post["type"], ":opening" => $starttime, ":deadline" => $lifetime, ":iteration" => $post["itertime"], ":timetype_iteration" => $post["timetype_itertime"]);
		$res->execute($param);

		if (isset($post["attaches"])) {
			foreach($post["attaches"] as $part) {

				$md5 = $this->getMD5($part);

				$sql = "INSERT INTO draft_attach (`tid`, `md5`) VALUES (:tid, :md5)";
					
				$res = $this->registry['db']->prepare($sql);
				$param = array(":tid" => $tid, ":md5" => $md5);
				$res->execute($param);
			}
		}

		return $tid;
	}

	public function getDrafts() {
		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT(id)
        FROM draft
        WHERE who = :uid
        ORDER BY id DESC
        LIMIT " . $this->startRow .  ", " . $this->limit;

		$res = $this->registry['db']->prepare($sql);
		$param = array(":uid" => $this->registry["ui"]["id"]);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		$this->totalPage = $this->registry['db']->query("SELECT FOUND_ROWS()")->fetchColumn();

		//Если общее число статей больше показанного, вызовем пейджер
		if ($this->totalPage < $this->limit+1)  {
		} else {
			$this->Pager();
		}

		return $data;
	}

	public function getDraft($tid) {

		$sql = "SELECT t.id, t_o.oid, t.who, t.imp, t.secure, t.name, t.text, t.opening AS start, t.gid, g.name AS `group`, tr.uid, tr.gid AS rgid, tr.all AS `all`, td.type, td.opening, td.deadline, td.iteration, td.timetype_iteration, tc.cid
        FROM draft AS t
        LEFT JOIN draft_responsible AS tr ON (tr.tid = t.id)
        LEFT JOIN draft_deadline AS td ON (td.tid = t.id)
        LEFT JOIN draft_objects AS t_o ON (t_o.tid = t.id)
        LEFT JOIN group_tt AS g ON (t.gid = g.id)
        LEFT JOIN draft_composite AS tc ON (tc.tid = t.id)
        WHERE t.id = :tid AND t.who = :uid";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":tid" => $tid, ":uid" => $this->registry["ui"]["id"]);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		if (count($data) > 0) {
			$data[0]["startdate"] = date("Y-m-d", strtotime($data[0]["opening"]));
			$data[0]["starttime"] = date("H:i:s", strtotime($data[0]["opening"]));

			$data[0]["startF"] = $this->editDate($data[0]["start"]);
			$data[0]["openingF"] = $this->editDate($data[0]["opening"]);

			$d = strtotime($data[0]["opening"]);
			$deadline = $data[0]["deadline"];
			$expire = date("YmdHis", mktime(date("H", $d), date("i", $d), date("s", $d) + $deadline, date("m", $d), date("d", $d), date("Y", $d)));
			$end = date("YmdHis");

			if (($data[0]["deadline"] / 60 /60 / 24) >= 1) {
				$data[0]["deadline"] = ($data[0]["deadline"] / 60 /60 / 24);
				$data[0]["deadline_date"] = "дней";
				if ($expire < $end) {
					$data[0]["expire"] = TRUE;
				} else { $data[0]["expire"] = FALSE;
				}
			} else {
				if (($data[0]["deadline"] / 60 /60 ) >= 1) {
					$data[0]["deadline"] = ($data[0]["deadline"] / 60 /60);
					$data[0]["deadline_date"] = "часов";
					if ($expire < $end) {
						$data[0]["expire"] = TRUE;
					} else { $data[0]["expire"] = FALSE;
					}
				} elseif (($data[0]["deadline"] / 60 ) >= 1) {
					$data[0]["deadline"] = ($data[0]["deadline"] / 60 );
					$data[0]["deadline_date"] = "минут";
					if ($expire < $end) {
						$data[0]["expire"] = TRUE;
					} else { $data[0]["expire"] = FALSE;
					}
				} else {
					$data[0]["deadline"] = "";
					$data[0]["deadline_date"] = "0";
				}
			}

			if ($data[0]["group"] == "") {
				$data[0]["group"] = "Внутренняя";
			}
				
			$sql = "SELECT fs.filename
	        FROM draft_attach AS da
	        LEFT JOIN fm_fs AS fs ON (fs.md5 = da.md5)
	        WHERE da.tid = :id";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":id" => $tid);
			$res->execute($param);
			$attaches = $res->fetchAll(PDO::FETCH_ASSOC);
				
			$data[0]["attach"] = $attaches;
		} else {
			$data = false;
		}

		return $data;
	}

	public function editDraft($post) {
		$tid = $post["tid"];

		$secure = $post["secure"];

		$sql = "UPDATE draft SET imp = :imp, secure = :secure, name = :name, text = :text, gid = :gid WHERE id = :tid LIMIT 1";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":tid" => $post["tid"], ":imp" => $post["imp"], ":secure" => $secure, ":name" => $post["taskname"], ":text" => $post["task"], ":gid" => $post["ttgid"]);
		$res->execute($param);

		$sql = "DELETE FROM draft_responsible WHERE tid = :tid";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":tid" => $tid);
		$res->execute($param);

		// ответственные
		if (!isset($post["ruser"])) {
			$post["ruser"] = array();
		}
		if (!isset($post["gruser"])) {
			$post["gruser"] = array();
		}
		if (!isset($post["rall"])) {
			$post["rall"] = array();
		}

		foreach($post["ruser"] as $part) {
			$sql = "INSERT INTO draft_responsible (tid, uid) VALUES (:tid, :uid)";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":tid" => $tid, ":uid" => $part);
			@$res->execute($param);
		}

		foreach($post["gruser"] as $part) {
			$sql = "INSERT INTO draft_responsible (tid, gid) VALUES (:tid, :gid)";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":tid" => $tid, ":gid" => $part);
			@$res->execute($param);
		}

		if ($post["rall"] == "1") {
			$sql = "INSERT INTO draft_responsible (tid, `all`) VALUES (:tid, 1)";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":tid" => $tid);
			@$res->execute($param);
		}
		// END ответственные

		if ($post["type"] == "0") {

			$starttime = $post["startdate_global"] . " " . $post["starttime_global"];
			$lifetime = 0;
			$post["itertime"] = "";

		} elseif ($post["type"] == "1") {
			$post["itertime"] = "";

			$starttime = $post["startdate_noiter"] . " " . $post["starttime_noiter"];

			if ($post["timetype_noiter"] == "min") {

				$lifetime = $post["lifetime_noiter"] * 60;

			} elseif ($post["timetype_noiter"] == "hour") {

				$lifetime = $post["lifetime_noiter"] * 60 * 60;

			} elseif ($post["timetype_noiter"] == "day") {

				$lifetime = $post["lifetime_noiter"] * 24 * 60 * 60;

			} else {

				$lifetime = 0;

			}
		} elseif ($post["type"] == "2") {

			$starttime = $post["startdate_iter"] . " " . $post["starttime_iter"];

			if ($post["timetype_iter"] == "day") {

				$lifetime = $post["lifetime_iter"] * 24 * 60 * 60;

			} else {

				$lifetime = 0;

			}
			
			if (!$this->validateIterDate($lifetime, $post["itertime"], $post["timetype_itertime"])) {
					$lifetime = 24 * 60 * 60;
			}
		}

		$sql = "UPDATE draft_deadline SET type = :type, opening = :opening, deadline = :deadline, iteration = :iteration, timetype_iteration = :timetype_iteration WHERE tid = :tid";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":tid" => $tid, ":type" => $post["type"], ":opening" => $starttime, ":deadline" => $lifetime, ":iteration" => $post["itertime"], ":timetype_iteration" => $post["timetype_itertime"]);
		$res->execute($param);

		if (isset($post["attaches"])) {
			$sql = "DELETE FROM draft_attach WHERE tid = :tid";
				
			$res = $this->registry['db']->prepare($sql);
			$param = array(":tid" => $tid);
			$res->execute($param);
				
			foreach($post["attaches"] as $part) {
				$md5 = $this->getMD5($part);

				$sql = "INSERT INTO draft_attach (`tid`, `md5`) VALUES (:tid, :md5)";
				
				$res = $this->registry['db']->prepare($sql);
				$param = array(":tid" => $tid, ":md5" => $md5);
				$res->execute($param);
			}
		}

		return TRUE;
	}

	public function delDraft($did) {
		$sql = "DELETE FROM draft WHERE who = :uid AND id = :did LIMIT 1";

		$res = $this->registry['db']->prepare($sql);
		$res->execute(array(":did" => $did, ":uid" => $this->registry["ui"]["id"]));

		$sql = "DELETE FROM draft_deadline WHERE tid = :did LIMIT 1";

		$res = $this->registry['db']->prepare($sql);
		$res->execute(array(":did" => $did));

		$sql = "DELETE FROM draft_responsible WHERE tid = :did LIMIT 1";

		$res = $this->registry['db']->prepare($sql);
		$res->execute(array(":did" => $did));
	}

	public function getCommentsStatus() {
		$sql = "SELECT id, status FROM comments_status ORDER BY id";

		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		return $data;
	}

	public function addCommentSendmail($cid) {
		$sql = "UPDATE troubles_discussion SET `sendmail` = '1' WHERE id = :id LIMIT 1";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $cid);
		$res->execute($param);
	}

	public function getComment($id) {
		$sql = "SELECT td.text AS `text`, fm.filename AS `filename`
		FROM troubles_discussion AS td
		LEFT JOIN troubles_discussion_attach AS tda ON (tda.tdid = td.id)
		LEFT JOIN fm_fs AS fm ON (fm.md5 = tda.md5)
		WHERE td.id = :id";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		return $data;
	}

	public function getCommentText($id) {
		$sql = "SELECT `text` FROM troubles_discussion WHERE id = :id LIMIT 1";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		return $data[0]["text"];
	}

	public function getCommentStatusText($id) {
		$sql = "SELECT status FROM comments_status WHERE id = :id LIMIT 1";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		return $data[0]["status"];
	}
	
	function getDraftFile($did, $filename) {
		$sql = "SELECT da.md5 AS `md5`
		        FROM draft_attach AS da
		        LEFT JOIN fm_fs AS fs ON (fs.md5 = da.md5)
		        WHERE da.tid = :did AND fs.filename = :filename
		        LIMIT 1";
		 
		$res = $this->registry['db']->prepare($sql);
		$param = array(":did" => $did, ":filename" => $filename);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($row) > 0) {
			return $row[0]["md5"];
		} else {
			return FALSE;
		}
	}

	function getFile($tid, $filename) {
		$sql = "SELECT ta.md5 AS `md5`
        FROM troubles_attach AS ta
        LEFT JOIN fm_fs AS fs ON (fs.md5 = ta.md5)
        WHERE ta.tid = :tid AND fs.filename = :filename
        LIMIT 1";
	  
		$res = $this->registry['db']->prepare($sql);
		$param = array(":tid" => $tid, ":filename" => $filename);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);

		if (count($row) > 0) {
			return $row[0]["md5"];
		} else {
			return FALSE;
		}
	}

	function getCommentFile($tdid, $filename) {
		$sql = "SELECT tda.md5 AS `md5`
        FROM troubles_discussion_attach AS tda
        LEFT JOIN fm_fs AS fs ON (fs.md5 = tda.md5)
        WHERE tda.tdid = :tdid AND fs.filename = :filename
        LIMIT 1";
	  
		$res = $this->registry['db']->prepare($sql);
		$param = array(":tdid" => $tdid, ":filename" => $filename);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);

		if (count($row) > 0) {
			return $row[0]["md5"];
		} else {
			return FALSE;
		}
	}

	function getMailFile($tid, $filename) {
		$sql = "SELECT ma.md5 AS `md5`
        FROM mail_attach AS ma
        WHERE ma.tid = :tid AND ma.filename = :filename
        LIMIT 1";
	  
		$res = $this->registry['db']->prepare($sql);
		$param = array(":tid" => $tid, ":filename" => $filename);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);

		if (count($row) > 0) {
			return $row[0]["md5"];
		} else {
			return FALSE;
		}
	}

	function getCommentMailFile($tdid, $filename) {
		$sql = "SELECT ma.md5 AS `md5`
        FROM mail_attach AS ma
        WHERE ma.tdid = :tdid AND ma.filename = :filename
        LIMIT 1";
	  
		$res = $this->registry['db']->prepare($sql);
		$param = array(":tdid" => $tdid, ":filename" => $filename);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);

		if (count($row) > 0) {
			return $row[0]["md5"];
		} else {
			return FALSE;
		}
	}

	function addTaskView($tid, $uid = null) {
		if ($uid == null) {
			$uid = $this->registry["ui"]["id"];
		}
		
		$sql = "SELECT COUNT(id) AS count, `timestamp`
        FROM troubles_view
        WHERE uid = :uid AND tid = :tid
        LIMIT 1";
	  
		$res = $this->registry['db']->prepare($sql);
		$param = array(":uid" => $uid, ":tid" => $tid);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);

		if ($row[0]["count"] == 1) {
			$sql = "UPDATE troubles_view SET timestamp = NOW() WHERE uid = :uid AND tid = :tid LIMIT 1";

			$res = $this->registry['db']->prepare($sql);
			$res->execute(array(":uid" => $uid, ":tid" => $tid));
		} else {
			$sql = "INSERT INTO troubles_view (uid, tid) VALUES (:uid, :tid)";

			$res = $this->registry['db']->prepare($sql);
			$res->execute(array(":uid" => $uid, ":tid" => $tid));
		}

		return $row[0]["timestamp"];
	}

	function getNewCommentsFromTid($tid, $uid = null) {
		if ($uid == null) {
			$uid = $this->registry["ui"]["id"];
		}
		
		$sql = "SELECT COUNT(id) AS count
        FROM troubles_view
        WHERE uid = :uid AND tid = :tid
        LIMIT 1";
	  
		$res = $this->registry['db']->prepare($sql);
		$param = array(":uid" => $uid, ":tid" => $tid);
		$res->execute($param);
		$count = $res->fetchAll(PDO::FETCH_ASSOC);

		if ($count[0]["count"] == 1) {
			$sql = "SELECT COUNT(com.id) AS count
			FROM troubles_discussion AS com
	        LEFT JOIN troubles_view AS tv ON (tv.tid = com.tid)
	        WHERE tv.uid = :uid AND com.tid = :tid AND com.timestamp >= tv.timestamp";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":uid" => $uid, ":tid" => $tid);
			$res->execute($param);
			$row = $res->fetchAll(PDO::FETCH_ASSOC);
		} else {
			$sql = "SELECT COUNT(com.id) AS count
			FROM troubles_discussion AS com
	        WHERE com.tid = :tid";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":tid" => $tid);
			$res->execute($param);
			$row = $res->fetchAll(PDO::FETCH_ASSOC);
		}

		return $row[0]["count"];
	}

	public function getDraftNumTasks($uid) {
		$sql = "SELECT count(id) AS count
        FROM draft
        WHERE who = :uid";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":uid" => $uid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		return $data[0]["count"];
	}
}
?>