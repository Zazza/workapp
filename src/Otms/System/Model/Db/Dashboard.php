<?php

/**
 * This file is part of the Workapp project.
*
* (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
*
*/

namespace Otms\System\Model\Db;

use Engine\Model;
use PDO;

/**
 * Db\Dashboard Model class
 *
 * Обеспечивает хранение и получение даных dashboard - информация о событиях в системе
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */

class Dashboard extends Model {
	/**
	 * Получить события
	 * 
	 * @param string $date - SQL сормированная строка Model\getSessionDate
	 * @param string $where - SQL сормированная строка Model\getSessionWhere()
	 * @return array
	 */
	public function getEvents($date, $where) {
		$sql = "SELECT logs.id, logs.type, logs.event, logs.oid, logs.uid, logs.timestamp, lo.key, lo.val, u.name, u.soname
			FROM logs
			LEFT JOIN logs_object AS lo ON (lo.log_oid = logs.id)
			LEFT JOIN users AS u ON (u.id = logs.uid)
			WHERE " . $date . " " . $where . "
			GROUP BY logs.id
			ORDER BY logs.timestamp DESC, lo.id
			LIMIT 20";
		
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $data;
	}
	
	/**
	 * Получить событие от нужного ID
	*
	* @param int $id - ID лога
	* @param string $where - SQL сормированная строка Model\getSessionWhere()
	* @return array
	*/
	public function getEventsId($id, $where) {
		$sql = "SELECT logs.id, logs.type, logs.event, logs.oid, logs.uid, logs.timestamp, lo.key, lo.val, u.name, u.soname
		FROM logs
		LEFT JOIN logs_object AS lo ON (lo.log_oid = logs.id)
		LEFT JOIN users AS u ON (u.id = logs.uid)
		WHERE " . $id . " " . $where . "
		GROUP BY logs.id
		ORDER BY logs.timestamp DESC, lo.id
		LIMIT 20";
	
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
	
		return $data;
	}
	
	/**
	 * Получить события для выпадающего dashboard на панели вверху страницы
	*
	* @param string $date - SQL сормированная строка Model\getSessionDate
	* @param string $where - SQL сормированная строка Model\getSessionWhere()
	* @return array
	*/
	public function getDashEvents($date, $where) {
		$sql = "SELECT logs.id, logs.type, logs.event, logs.oid, logs.uid, logs.timestamp, lo.key, lo.val, u.name, u.soname
			FROM logs_closed AS lc
			LEFT JOIN logs ON (logs.id = lc.eid)
			LEFT JOIN logs_object AS lo ON (lo.log_oid = lc.eid)
			LEFT JOIN users AS u ON (u.id = lc.uid)
			WHERE " . $date . " " . $where . "
			GROUP BY lc.eid
			ORDER BY logs.timestamp DESC, lo.id";
		
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		return $data;
	}
	
	/**
	 * Получить НОВЫЕ события для выпадающего dashboard на панели вверху страницы
	*
	* @param int $lid - ID последнего события
	* @param string $where - SQL сормированная строка Model\getSessionWhere()
	* @return array
	*/
	public function getNewEvents($lid, $where) {
		$sql = "SELECT logs.id, logs.type, logs.event, logs.oid, logs.uid, logs.timestamp, lo.key, lo.val, u.name, u.soname
				FROM logs_closed AS lc
				LEFT JOIN logs ON (logs.id = lc.eid)
				LEFT JOIN logs_object AS lo ON (lo.log_oid = lc.eid)
				LEFT JOIN users AS u ON (u.id = lc.uid)
				WHERE logs.id > :lid " . $where . "
				GROUP BY lc.eid
				ORDER BY logs.timestamp DESC";

		$res = $this->registry['db']->prepare($sql);
		$params = array(":lid" => $lid);
		$res->execute($params);
		$data = $res->fetchAll(PDO::FETCH_ASSOC); 
	
		return $data;
	}
	
	/**
	 * Пометить событие какпрочитанное
	 * 
	 * @param int $eid - ID события 
	 */
	public function closeEvent($eid) {
		$sql = "DELETE FROM logs_closed WHERE eid = :eid AND uid = :uid";
		
		$res = $this->registry['db']->prepare($sql);
		$params = array(":eid" => $eid, ":uid" => $this->registry["ui"]["id"]);
		$res->execute($params);
	}
}