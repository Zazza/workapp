<?php

/**
 * This file is part of the Workapp project.
 *
 * Calendar Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Calendar\Model;

use Engine\Modules\Model;
use PDO;

/**
 * Model\Reserv class
 * 
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Reserv extends Model {
	/**
	 * User ID
	 * 
	 * @var int
	 */
	private $_uid = 0;
	
	/**
	 * Object ID
	 * 
	 * @var int
	 */
	private $_oid = 0;
	
	/**
	 * Получить список всех броней за текущий месяц
	 * 
	 * @param string $year
	 * @param string $month
	 * @return array
	 */
	public function getMonthReservs($year, $month) {
		for($i=1; $i<=31; $i++) {
			$data[$i]["num"] = 0;
		}
		
		if ( ($this->_uid != 0) and ($this->_oid != 0) ) {
			$sql = "SELECT DATE_FORMAT(`start`, '%Y%m%d') AS start, DATE_FORMAT(`end`, '%Y%m%d') AS end FROM reserv WHERE oid = :oid AND uid = :uid";
		
			$res = $this->registry['db']->prepare($sql);
			$param = array(":oid" => $this->_oid, ":uid" => $this->_uid);
		} else if ( ($this->_uid == 0) and ($this->_oid == 0) ) {
			$sql = "SELECT DATE_FORMAT(`start`, '%Y%m%d') AS start, DATE_FORMAT(`end`, '%Y%m%d') AS end FROM reserv";
			
			$res = $this->registry['db']->prepare($sql);
			$param = array();
		} else if ( ($this->_uid != 0) and ($this->_oid == 0) ) {
			$sql = "SELECT DATE_FORMAT(`start`, '%Y%m%d') AS start, DATE_FORMAT(`end`, '%Y%m%d') AS end FROM reserv WHERE uid = :uid";
			
			$res = $this->registry['db']->prepare($sql);
			$param = array(":uid" => $this->_uid);
		} else if ( ($this->_uid == 0) and ($this->_oid != 0) ) {
			$sql = "SELECT DATE_FORMAT(`start`, '%Y%m%d') AS start, DATE_FORMAT(`end`, '%Y%m%d') AS end FROM reserv WHERE oid = :oid";
			
			$res = $this->registry['db']->prepare($sql);
			$param = array(":oid" => $this->_oid);
		}

		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);

		foreach($row AS $part) {
			for($i=1; $i<=31; $i++) {
				if ($part["end"] == "0") {
					if ($i < 10) {
						if ($part["start"] <= $year . $month . "0" . $i) {
							$data[$i]["num"]++;
						}
					} else {
						if ($part["start"] <= $year . $month . $i) {
							$data[$i]["num"]++;
						}
					}					
				} else {
					if ($i < 10) {
						if ( ($part["start"] <= $year . $month . "0" . $i) and ($part["end"] >= $year . $month . "0" . $i) ) {
							$data[$i]["num"]++;
						}
					} else {
						if ( ($part["start"] <= $year . $month . $i) and ($part["end"] >= $year . $month . $i) ) {
							$data[$i]["num"]++;
						}
					}
				}
			}
		}
		
		return $data;
	}
	
	/**
	 * Setter
	 * 
	 * @param int $uid
	 */
	public function setUid($uid) {
		$this->_uid = $uid;
	}
	
	/**
	 * Setter
	 * 
	 * @param int $oid
	 */
	public function setOid($oid) {
		$this->_oid = $oid;
	}
	
	/**
	 * Получить информацию о броне по её ID
	 * 
	 * @param int $id
	 * @return array
	 */
	public function getReservFromID($id) {
		$sql = "SELECT id, uid, oid, start, end FROM reserv WHERE id = :id";
			
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);		
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $row;
	}
	
	/**
	 * Получить информацию о всех бронях за нужный день
	 * 
	 * @param string(date) $date
	 * @return array
	 */
	public function getDateReservs($date) {
		$mktime = mktime(0, 0, 0, date("m", strtotime($date)), date("d", strtotime($date)), date("Y", strtotime($date)));
		$year = date("Y", $mktime);
		$month = date("m", $mktime);
		$day = date("d", $mktime);

		if ( ($this->_uid != 0) and ($this->_oid != 0) ) {
			$sql = "SELECT id, uid, oid, start, end FROM reserv WHERE DATE_FORMAT(`start`, '%Y%m%d') <= '" . $year . $month . $day . "' AND (DATE_FORMAT(`end`, '%Y%m%d') >= '" . $year . $month . $day . "' OR DATE_FORMAT(`end`, '%Y%m%d') <= '00000000') AND oid = :oid AND uid = :uid";
			
			$res = $this->registry['db']->prepare($sql);
			$param = array(":oid" => $this->_oid, ":uid" => $this->_uid);
		} else if ( ($this->_uid == 0) and ($this->_oid == 0) ) {
			$sql = "SELECT id, uid, oid, start, end FROM reserv WHERE DATE_FORMAT(`start`, '%Y%m%d') <= '" . $year . $month . $day . "' AND (DATE_FORMAT(`end`, '%Y%m%d') >= '" . $year . $month . $day . "' OR DATE_FORMAT(`end`, '%Y%m%d') <= '00000000')";
			
			$res = $this->registry['db']->prepare($sql);
			$param = array();
		} else if ( ($this->_uid != 0) and ($this->_oid == 0) ) {
			$sql = "SELECT id, uid, oid, start, end FROM reserv WHERE DATE_FORMAT(`start`, '%Y%m%d') <= '" . $year . $month . $day . "' AND (DATE_FORMAT(`end`, '%Y%m%d') >= '" . $year . $month . $day . "' OR DATE_FORMAT(`end`, '%Y%m%d') <= '00000000') AND uid = :uid";
			
			$res = $this->registry['db']->prepare($sql);
			$param = array(":uid" => $this->_uid);
		} else if ( ($this->_uid == 0) and ($this->_oid != 0) ) {
			$sql = "SELECT id, uid, oid, start, end FROM reserv WHERE DATE_FORMAT(`start`, '%Y%m%d') <= '" . $year . $month . $day . "' AND (DATE_FORMAT(`end`, '%Y%m%d') >= '" . $year . $month . $day . "' OR DATE_FORMAT(`end`, '%Y%m%d') <= '00000000') AND oid = :oid";
			
			$res = $this->registry['db']->prepare($sql);
			$param = array(":oid" => $this->_oid);
		}

		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
		
		$data = array();
		for($i=0; $i<count($row); $i++) {
			$mktime_start = mktime(date("H", strtotime($row[$i]["start"])), date("i", strtotime($row[$i]["start"])), date("s", strtotime($row[$i]["start"])), date("m", strtotime($row[$i]["start"])), date("d", strtotime($row[$i]["start"])), date("Y", strtotime($row[$i]["start"])));
			$start_day = date("Ymd", $mktime_start);
			$mktime_end = mktime(date("H", strtotime($row[$i]["end"])), date("i", strtotime($row[$i]["end"])), date("s", strtotime($row[$i]["end"])), date("m", strtotime($row[$i]["end"])), date("d", strtotime($row[$i]["end"])), date("Y", strtotime($row[$i]["end"])));
			$end_day = date("Ymd", $mktime_end);

			if ( ($start_day == $year . $month . $day) and ($end_day == $year . $month . $day) ) {
				$data[$i]["id"] = $row[$i]["id"];
				$data[$i]["oid"] = $row[$i]["oid"];
				$data[$i]["uid"] = $row[$i]["uid"];
				$data[$i]["startreserv"] = $row[$i]["start"];
				$data[$i]["endreserv"] = $row[$i]["end"];
				$data[$i]["start"] = date("H", $mktime_start);
				if (date("H:i", $mktime_end) == "23:59") {
					$temp_end = "24";
				} else {
					$temp_end = date("H:i", $mktime_end);
				}
				$data[$i]["end"] = $temp_end;
			} else if ($start_day == $year . $month . $day) {
				if (date("H", $mktime_start) == "00") {
					$data[$i]["id"] = $row[$i]["id"];
					$data[$i]["oid"] = $row[$i]["oid"];
					$data[$i]["uid"] = $row[$i]["uid"];
					$data[$i]["startreserv"] = $row[$i]["start"];
					$data[$i]["endreserv"] = $row[$i]["end"];
					$data[$i]["start"] = "0";
					$data[$i]["end"] = "24";
				} else {
					$data[$i]["id"] = $row[$i]["id"];
					$data[$i]["oid"] = $row[$i]["oid"];
					$data[$i]["uid"] = $row[$i]["uid"];
					$data[$i]["startreserv"] = $row[$i]["start"];
					$data[$i]["endreserv"] = $row[$i]["end"];
					$data[$i]["start"] = date("H", $mktime_start);
					$data[$i]["end"] = "24";
				}
			} else if ($end_day == $year . $month . $day) {
				if (date("H", $mktime_end) == "00") {
					$data[$i]["id"] = $row[$i]["id"];
					$data[$i]["oid"] = $row[$i]["oid"];
					$data[$i]["uid"] = $row[$i]["uid"];
					$data[$i]["startreserv"] = $row[$i]["start"];
					$data[$i]["endreserv"] = $row[$i]["end"];
					$data[$i]["start"] = "0";
					$data[$i]["end"] = "24";
				} else {
					$data[$i]["id"] = $row[$i]["id"];
					$data[$i]["oid"] = $row[$i]["oid"];
					$data[$i]["uid"] = $row[$i]["uid"];
					$data[$i]["startreserv"] = $row[$i]["start"];
					$data[$i]["endreserv"] = $row[$i]["end"];
					$data[$i]["start"] = "0";
					if (date("H:i", $mktime_end) == "23:59") {
						$temp_end = "24";
					} else {
						$temp_end = date("H:i", $mktime_end);
					}
					$data[$i]["end"] = $temp_end;
				}
			} else {
				$data[$i]["id"] = $row[$i]["id"];
				$data[$i]["oid"] = $row[$i]["oid"];
				$data[$i]["uid"] = $row[$i]["uid"];
				$data[$i]["startreserv"] = $row[$i]["start"];
				$data[$i]["endreserv"] = $row[$i]["end"];
				$data[$i]["start"] = "0";
				$data[$i]["end"] = "24";
			}
		}
		
		return $data;
	}
	
	/**
	 * Добавить дату брони со стартовой даты до конечной.
	 * 
	 * $this->_oid - ID объекта
	 * @param string(date) $start
	 * @param string(date) $end
	 * @return boolean
	 *    TRUE - успех
	 *    FALSE - есть пересечения
	 */
	public function addEndReserv($start, $end) {
		$sql = "SELECT COUNT(id) AS count FROM reserv WHERE (DATE(`start`) BETWEEN :start AND :end OR DATE(`end`) BETWEEN :start AND :end) AND oid = :oid";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":oid" => $this->_oid, ":start" => $start, ":end" => $end);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
		
		if ($row[0]["count"] == 0) {		
			$sql = "INSERT INTO reserv (`uid`, `oid`, `start`, `end`) VALUES (:uid, :oid, :start, :end)";
				
			$res = $this->registry['db']->prepare($sql);
			$param = array(":uid" => $this->_uid, ":oid" => $this->_oid, ":start" => $start, ":end" => $end);
			$res->execute($param);
			
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Обновить дату брони со стартовой даты до конечной.
	 *
	 * $this->_oid - ID объекта
	 * @param int $id - ID брони
	 * @param string(date) $start
	 * @param string(date) $end
	 * @return boolean
	 *    TRUE - успех
	 *    FALSE - есть пересечения
	 */
	public function updateEndReserv($id, $start, $end) {
		$sql = "SELECT COUNT(id) AS count FROM reserv WHERE (DATE(`start`) BETWEEN :start AND :end OR DATE(`end`) BETWEEN :start AND :end) AND oid = :oid AND uid != :uid";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":oid" => $this->_oid, ":start" => $start, ":end" => $end, ":uid" => $this->registry["ui"]["id"]);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);

		if ($row[0]["count"] == 0) {
			$sql = "UPDATE reserv SET `start` = :start,  `end` = :end WHERE id = :id LIMIT 1";
				
			$res = $this->registry['db']->prepare($sql);
			$param = array(":id" => $id, ":start" => $start, ":end" => $end);
			$res->execute($param);
			
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Получить полный список броней.
	 * 
	 *  Если задан $this->_uid - для ID пользователя
	 *     не задан - все 
	 *  @return array
	 */
	public function getList() {
		if ($this->_uid != 0) {
			$sql = "SELECT id, uid, oid, start, end FROM reserv WHERE uid = :uid";
		
			$res = $this->registry['db']->prepare($sql);
			$param = array(":uid" => $this->_uid);
		} else if ( ($this->_uid == 0) and ($this->_oid == 0) ) {
			$sql = "SELECT id, uid, oid, start, end FROM reserv";
			
			$res = $this->registry['db']->prepare($sql);
			$param = array();
		}
			
		$res = $this->registry['db']->prepare($sql);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $row;
	}
	
	/**
	 * Удалить бронь
	 * 
	 * @param int $id
	 */
	public function delReserv($id) {
		$sql = "DELETE FROM reserv WHERE id = :id LIMIT 1";
			
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
	}
	
	/**
	 * Получить информацию о броне
	 * 
	 * @param int $id
	 * @return array
	 */
	public function getReserv($id) {
		$sql = "SELECT id, uid, oid, start, end FROM reserv WHERE id = :id LIMIT 1";
			
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $row[0];
	}
	
	/**
	 * Получить клалендарь для Ганта
	 *    Количество дней задано в сессии $_SESSION["gant"]
	 * 
	 * @return array
	 */
	public function getCalendar() {
		$data = array(); $cal = array();
		
		$sql = "SELECT id, uid, oid, start, end
		FROM reserv
		ORDER BY id, start";
		
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
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
		
				$data[$j]["start"] = date("Y-m-d", strtotime($data[$j]["start"]));
		
				$days = 1;
		
				if ( (strtotime($data[$j]["start"]) <= strtotime($timestamp)) and ((strtotime($data[$j]["end"]) >= strtotime($timestamp))) ) {
					$cal["Бронь"][$data[$j]["id"]][$timestamp] = date("d.m", strtotime($timestamp));
				}
			}
		}

		return $cal;
	}
}
?>
