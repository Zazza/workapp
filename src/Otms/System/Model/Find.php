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
 * Find Model class
 *
 * Поиск задач, объектов и информации
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */

class Find extends Model {
	/**
	 * Количество найденной информации
	 * 
	 * @param array $find - поисковый запрос 
	 * @return array:
	 *    $rows["obj"] - объекты
	 *    $rows["tasks"] - задачи
	 *    $rows["advs"] - информация
	 */
    public function getNumFinds($find) {
        $rows = array();
        
		foreach ($find as $part) {
            $part = str_replace("*", "", $part);
    		$str = "+" . $part . "*";
    		$finds[] = $str;
		}
        
		$finds = implode(" ", $finds);

		$sql = "SELECT DISTINCT o.id, MATCH (ov.val) AGAINST (:find IN BOOLEAN MODE) AS relev
				FROM objects AS o
                LEFT JOIN objects_vals AS ov ON (ov.oid = o.id)
                LEFT JOIN templates AS t ON (t.id = o.template)
                WHERE t.id IS NOT NULL
				HAVING relev > 0";

		$res = $this->registry['db']->prepare($sql);
        $param = array(":find" => $finds);
		$res->execute($param);
		$row1 = $res->fetchAll(PDO::FETCH_ASSOC); 
        
        $rows["obj"] = count($row1);
        
		$sql = "SELECT DISTINCT t.id, MATCH (t.text) AGAINST (:find IN BOOLEAN MODE) AS relev, t_o.oid
				FROM troubles AS t
                LEFT JOIN troubles_responsible AS tr ON (tr.tid = t.id)
                LEFT JOIN troubles_objects AS t_o ON (t_o.tid = t.id)
                WHERE ((t.secure = 0) OR ((t.secure = 1) AND (t.who = :uid OR tr.uid = :uid)))
				HAVING relev > 0";

		$res = $this->registry['db']->prepare($sql);
        $param = array(":find" => $finds, ":uid" => $this->registry["ui"]["id"]);
		$res->execute($param);
		$row2 = $res->fetchAll(PDO::FETCH_ASSOC);
        
        $rows["tasks"] = count($row2);
        
		$sql = "SELECT DISTINCT o.id, MATCH (oa.val) AGAINST (:find IN BOOLEAN MODE) AS relev
				FROM objects AS o
                LEFT JOIN objects_advanced AS oa ON (oa.oid = o.id)
                LEFT JOIN templates AS t ON (t.id = o.template)
                WHERE t.id IS NOT NULL
				HAVING relev > 0";

		$res = $this->registry['db']->prepare($sql);
        $param = array(":find" => $finds);
		$res->execute($param);
		$row3 = $res->fetchAll(PDO::FETCH_ASSOC);
        
        $rows["advs"] = count($row3);

		return $rows;
    }
    
    /**
     * Возвращает найденные объекты и их релевантность
     * 
     * @param array $find - поисковый запрос 
     * @return array
     */
    public function findObjects($find) {
        $rows = array(); $finds = array();
		foreach ($find as $part) {
            $part = str_replace("*", "", $part);
    		$str = "+" . $part . "*";
    		$finds[] = $str;
		}

		if (count($finds) > 0) {
            $finds = implode(" ", $finds);
        
    		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT o.id AS id, MATCH (ov.val) AGAINST (:find IN BOOLEAN MODE) AS relev
    				FROM objects AS o
                    LEFT JOIN objects_vals AS ov ON (ov.oid = o.id)
                    LEFT JOIN templates AS t ON (t.id = o.template)
                    WHERE t.id IS NOT NULL
    				HAVING relev > 0
    				ORDER BY relev DESC, o.id DESC
    				LIMIT " . $this->startRow .  ", " . $this->limit;
    
    		$res = $this->registry['db']->prepare($sql);
    		$param = array(":find" => $finds);
    		$res->execute($param);
    		$rows = $res->fetchAll(PDO::FETCH_ASSOC);
            
            $this->totalPage = $this->registry['db']->query("SELECT FOUND_ROWS()")->fetchColumn();
            
    		if ($this->totalPage < $this->limit+1)  {
    		} else {
    			$this->Pager();
    		}
        }

		return $rows;
    }
    
    /**
     * Возвращает найденные задачи и их релевантность
    *
    * @param array $find - поисковый запрос
    * @return array
    */
    public function findTroubles($find) {
        $rows = array(); $finds = array();
		foreach ($find as $part) {
            $part = str_replace("*", "", $part);
    		$str = "+" . $part . "*";
    		$finds[] = $str;
		}
		
		if (count($finds) > 0) {
            $finds = implode(" ", $finds);
        
			$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT t.id AS id, MATCH (t.text) AGAINST (:find IN BOOLEAN MODE) AS relev, t_o.oid
					FROM troubles AS t
	                LEFT JOIN troubles_responsible AS tr ON (tr.tid = t.id)
	                LEFT JOIN troubles_objects AS t_o ON (t_o.tid = t.id)
	                WHERE ((t.secure = 0) OR ((t.secure = 1) AND (t.who = :uid OR tr.uid = :uid)))
					HAVING relev > 0
					ORDER BY relev DESC, t.id DESC
					LIMIT " . $this->startRow .  ", " . $this->limit;
	
			$res = $this->registry['db']->prepare($sql);
			$param = array(":find" => $finds, ":uid" => $this->registry["ui"]["id"]);
			$res->execute($param);
			$rows = $res->fetchAll(PDO::FETCH_ASSOC);
	        
	        $this->totalPage = $this->registry['db']->query("SELECT FOUND_ROWS()")->fetchColumn();
	        
			if ($this->totalPage < $this->limit+1)  {
			} else {
				$this->Pager();
			}
		}

		return $rows;
    }
    
    /**
     * Возвращает найденную информацию и их релевантность
    *
    * @param array $find - поисковый запрос
    * @return array
    */
    public function findAdvs($find) {
        $rows = array(); $finds = array();
		foreach ($find as $part) {
            $part = str_replace("*", "", $part);
    		$str = "+" . $part . "*";
    		$finds[] = $str;
		}
		
		if (count($finds) > 0) {
            $finds = implode(" ", $finds);
        
			$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT o.id AS id, MATCH (oa.val) AGAINST (:find IN BOOLEAN MODE) AS relev
					FROM objects AS o
	                LEFT JOIN objects_advanced AS oa ON (oa.oid = o.id)
	                LEFT JOIN templates AS t ON (t.id = o.template)
	                WHERE t.id IS NOT NULL
					HAVING relev > 0
					ORDER BY relev DESC, o.id DESC
					LIMIT " . $this->startRow .  ", " . $this->limit;
	
			$res = $this->registry['db']->prepare($sql);
			$param = array(":find" => $finds);
			$res->execute($param);
			$rows = $res->fetchAll(PDO::FETCH_ASSOC);
	        
	        $this->totalPage = $this->registry['db']->query("SELECT FOUND_ROWS()")->fetchColumn();
	        
			if ($this->totalPage < $this->limit+1)  {
			} else {
				$this->Pager();
			}
		}

		return $rows;
    }
}