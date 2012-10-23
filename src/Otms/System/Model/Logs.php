<?php

/**
 * This file is part of the Workapp project.
*
* (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
*
*/

namespace Otms\System\Model;

use Engine\Model;
use PDO;

/**
 * Logs Model class
 *
 * Обеспечивает функицонирование подсистемы логирования
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */

class Logs extends Model {
	/**
	 * ID пользователя
	 * @var int
	 */
	public $uid;

	/**
	 * Сохранить событие (лог)
	 * 
	 * @param string $type:
	 *    mail - письмо
	 *    com - комментарий к задаче
	 *    obj - объект
	 *    info - информация
	 *    service - сервисное сообщение
	 *    task - задача
	 * @param string $event - текст события
	 * @param int $oid - ID (объекта, задачи и т.д.)
	 * @param array $data - данные для хранений связанные с данным логом, нужны для работы "истории"
	 * Например: изменение в задаче - хранит предыдущее состояние задачи
	 */
	function set($type, $event, $oid, $data = array()) {
		if ($this->uid == null) {
			$this->uid = $this->registry["ui"]["id"];
		}
		
        $sql = "INSERT INTO logs (type, event, uid, oid) VALUES (:type, :event, :uid, :oid)";
    
        $res = $this->registry['db']->prepare($sql);
        $param = array(":type" => $type, ":event" => $event, ":uid" => $this->uid, ":oid" => $oid);
        $res->execute($param);
        
        $log_oid = $this->registry['db']->lastInsertId();

        $this->addUsersEvent($log_oid, $oid, $type, $this->uid);
        
		foreach($data as $key=>$val) {
	        $sql = "INSERT INTO logs_object (log_oid, `key`, `val`) VALUES (:oid, :key, :val)";
	    
	        $res = $this->registry['db']->prepare($sql);
	        $param = array(":oid" => $log_oid, ":key" => $key, ":val" => $val);
	        $res->execute($param);
		}
	}
	
	/**
	 * Создание события для dashboard (вверху страницы) нужному пользователю
	 * 
	 * @param int $eid - ID события
	 * @param int $oid - ID (объекта, задачи и т.д.)
	 * @param string $type:
	 *    mail - письмо
	 *    com - комментарий к задаче
	 *    obj - объект
	 *    info - информация
	 *    service - сервисное сообщение
	 *    task - задача
	 * @param int $uid - ID пользователя
	 */
	function addUsersEvent($eid, $oid, $type, $uid) {
		$dashEvents = $this->registry["ajax_notice_sets"];
		
		// mail only for one user!
		if ($type == "mail") {
			if (!$dashEvents["mail"]) {
				$this->setUserEvent($eid, $uid);
			}
		// service only for one user and required!
		} else if ($type == "service") {
			$this->setUserEvent($eid, $uid);
		// else notice
		} else {
			$users = $this->registry["user"]->getUsersList();
			
			for($i=0; $i<count($users); $i++) {
				if ( ($type == "task") or ($type == "com") ) {
					if ($task = $this->registry["task"]->getTask($oid, $users[$i]["id"])) {
						if ($this->registry["task"]->acceptReadTask($task)) {
							if (!$dashEvents[$type]) {
								$this->setUserEvent($eid, $users[$i]["id"]);
							}
						}
					}
				} elseif ($type == "obj") {
					if (!$dashEvents["obj"]) {
						$this->setUserEvent($eid, $users[$i]["id"]);
					}
				}
			}
		}
	}
	
	/**
	 * Добавляет событие в БД
	 * Нужно для addUsersEvent
	 * 
	 * @param int $eid - ID события
	 * @param int $uid - ID пользователя
	 */
	function setUserEvent($eid, $uid) {
		$sql = "INSERT INTO logs_closed (eid, uid) VALUES (:eid, :uid)";
			
		$res = $this->registry['db']->prepare($sql);
		$param = array(":eid" => $eid, ":uid" => $uid);
		$res->execute($param);
	}
	
	/**
	 * Проверка существования в существующих логах объекта (задачи и т.д.)
	 * 
	 * @param string $type:
	 *    mail - письмо
	 *    com - комментарий к задаче
	 *    obj - объект
	 *    info - информация
	 *    service - сервисное сообщение
	 *    task - задача
	 * @param int $oid - ID (объекта, задачи и т.д.)
	 * @return TRUE - существует
	 * @return FALSE - не существует
	 */
	function issetHistory($type, $oid) {
        $sql = "SELECT COUNT(id) AS count FROM logs WHERE type = :type AND oid = :oid";
    
        $res = $this->registry['db']->prepare($sql);
        $param = array(":type" => $type, ":oid" => $oid);
        $res->execute($param);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        
        if ($row[0]["count"] > 0) {
        	return true;
        } else {
        	return false;
        }
	}
	
	/**
	 * Получить лог по ID
	 * 
	 * @param int $id - ID события
	 * @return array
	 */
	function getHistoryFromId($id) {
        $sql = "SELECT lo.key, lo.val
        FROM logs
        LEFT JOIN logs_object AS lo ON (lo.log_oid = logs.id)
        WHERE logs.id = :id
        ORDER BY lo.id";
    
        $res = $this->registry['db']->prepare($sql);
        $param = array(":id" => $id);
        $res->execute($param);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        
        return $row;
	}
	
	/**
	 * Получить весь лог для OID
	*
	 * @param string $type:
	 *    mail - письмо
	 *    com - комментарий к задаче
	 *    obj - объект
	 *    info - информация
	 *    service - сервисное сообщение
	 *    task - задача
	 * @param int $oid - ID (объекта, задачи и т.д.)
	 * @return array
	*/
    function getHistory($type, $oid) {
        $sql = "SELECT logs.id, logs.uid, logs.timestamp AS `timestamp`
        FROM logs
        WHERE logs.type = :type AND logs.oid = :oid
        GROUP BY logs.id
        ORDER BY logs.id DESC";
    
        $res = $this->registry['db']->prepare($sql);
        $param = array(":type" => $type, ":oid" => $oid);
        $res->execute($param);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        
    	for($i=0; $i<count($row); $i++) {
			$row[$i]["param"] = $this->getHistoryFromId($row[$i]["id"]);
			$row[$i]["date"] = $row[$i]["timestamp"];
			$row[$i]["timestamp"] = date("YmdHis", strtotime($row[$i]["timestamp"]));
			$row[$i]["fdate"] = $this->editDate($row[$i]["timestamp"]);
		}
        
        return $row;
    }
    
    /**
     * Обновить ID последнего события
     * 
     * @param int $id - ID события
     */
    
    function addLastDashId($id) {
    	$sql = "REPLACE INTO logs_dashajax SET lid = :lid, uid = :uid";
    	
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":uid" => $this->registry["ui"]["id"], ":lid" => $id);
    	$res->execute($param);
    }
    
    /**
     * Получить ID последнего события
     * 
     * @return int
     */
    
    function getLastDashId() {
    	$sql = "SELECT lid FROM logs_dashajax WHERE uid = :uid LIMIT 1";
    	 
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":uid" => $this->registry["ui"]["id"]);
    	$res->execute($param);
    	$row = $res->fetchAll(PDO::FETCH_ASSOC);
    	
    	return $row[0]["lid"];
    }
}