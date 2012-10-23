<?php

/**
 * This file is part of the Workapp project.
 *
 * Object Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Objects\Model;

use Engine\Modules\Model;
use PDO;

/**
 * Model\Object class
 *
 * Класс-модель для работы с объектами
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Object extends Model {
	
	/**
	 * Добавить объект
	 * 
	 * @param array $post
	 * @param int $uid
	 *    if !$uid else $uid = $this->registry["ui"]["id"]
	 */
    public function addObject($post, $uid = 0) {
    	if ($uid == 0) {
    		$uid = $this->registry["ui"]["id"];
    	} else {
    		$this->registry["logs"]->uid = $uid;
    	}
    	
        $sql = "INSERT INTO objects (template, uid) VALUES (:tpl, :uid)";
        
        $res = $this->registry['db']->prepare($sql);
        $param = array(":tpl" => $post["tid"], ":uid" => $uid);
        $res->execute($param);
        
        $oid = $this->registry['db']->lastInsertId();
        $fid = $this->registry["module_filemanager"]->createdir("2", $oid);
        
        $sql = "UPDATE objects SET fid = :fid WHERE id = :id LIMIT 1";
        
        $res = $this->registry['db']->prepare($sql);
        $param = array(":id" => $oid, ":fid" => $fid);
        $res->execute($param);
		
        if ( (isset($post["email"])) and ($post["email"] != null) ) {
        	$contact = & $_SESSION["contact"];
        	unset($contact["email"]);
        	
	        $sql = "INSERT INTO mail_contacts (oid, email) VALUES (:oid, :email)";
	        
	        $res = $this->registry['db']->prepare($sql);
	        $param = array(":oid" => $oid, ":email" => $post["email"]);
	        $res->execute($param);
    	}
        
        foreach ($post as $key=>$val) {
            if (is_int($key)) {
                $sql = "INSERT INTO objects_vals (oid, fid, val) VALUES (:oid, :fid, :val)";
                
                $res = $this->registry['db']->prepare($sql);
                $param = array(":oid" => $oid, ":fid" => $key, ":val" => strip_tags($val, "<a>"));
                $res->execute($param);
                
                $obj[$key] = strip_tags($val, "<a>");
            	
                $field = $this->getObject($oid);
        		foreach($field as $part) {
        			if ($part["fid"] == $key) $logs_obj[$part["field"]] = strip_tags($val, "<a>");
        		}
            }
        }
            
    	$string = "Добавление объекта <a href='" . $this->registry["uri"] . "objects/show/" . $oid . "/'>" . $oid . "</a>";
    
    	$this->registry["logs"]->set("obj", $string, $oid, $logs_obj);
    }
    
    /**
     * Получить описание объекта
     * 
     * @param int $id
     * @return array
     * @return boolean FALSE
     */
    public function getShortObject($id) {
	        $sql = "SELECT o.id AS id, o.timestamp AS `timestamp`, o.template AS tid, o.fid AS fdirid, t.name AS tname, ov.fid AS fid, tf.field AS `field`, tf.main AS `main`, ov.val AS val
					FROM objects AS o
	                LEFT JOIN objects_vals AS ov ON (ov.oid = o.id)
	                LEFT JOIN templates_fields AS tf ON (tf.id = ov.fid)
	                LEFT JOIN templates AS t ON (t.id = tf.tid)
	                WHERE o.id = :id
	                    AND tf.main = 1
	                ORDER BY fid";
	                
			$res = $this->registry['db']->prepare($sql);
			$param = array(":id" => $id);
			$res->execute($param);
			$rows = $res->fetchAll(PDO::FETCH_ASSOC);
	
			if (count($rows) > 0) {
				$sql = "SELECT `email`
				FROM mail_contacts
				WHERE oid = :id
				LIMIT 1";
				
				$res = $this->registry['db']->prepare($sql);
				$param = array(":id" => $id);
				$res->execute($param);
				$email = $res->fetchAll(PDO::FETCH_ASSOC);
				
				if ( (count($email)) and ($email[0]["email"] != "") ) {
					$rows[0]["email"] = $email[0]["email"];
				}
				
	        	return $rows;
			} else {
				return false;
			}
    	
    	if (count($rows) > 0) {
    		return $rows;
    	} else {
    		return false;
    	}
    }
    
    /**
     * Получить количество задач (по типам) для объекта
     * 
     * @param int $oid
     * @return array
     */
    public function getNumTroubles($oid) {
        $data = FALSE;
        
        $sql = "SELECT COUNT(t.id) AS count
        FROM troubles AS t
        LEFT JOIN troubles_deadline AS td ON (td.tid = t.id)
        LEFT JOIN troubles_objects AS t_o ON (t_o.tid = t.id)
        WHERE t.secure = 0
        	AND t_o.oid = :oid
            AND t.close = 0
            AND td.type = 0";
        
        $res = $this->registry['db']->prepare($sql);
		$param = array(":oid" => $oid);
		$res->execute($param);
		$rows = $res->fetchAll(PDO::FETCH_ASSOC);
        $data["global"] = $rows[0]["count"];
        
        $sql = "SELECT COUNT(t.id) AS count
        FROM troubles AS t
        LEFT JOIN troubles_deadline AS td ON (td.tid = t.id)
        LEFT JOIN troubles_objects AS t_o ON (t_o.tid = t.id)
        WHERE t.secure = 0
        	AND t_o.oid = :oid
            AND t.close = 0
            AND td.type = 1";
        
        $res = $this->registry['db']->prepare($sql);
		$param = array(":oid" => $oid);
		$res->execute($param);
		$rows = $res->fetchAll(PDO::FETCH_ASSOC);
        $data["time"] = $rows[0]["count"];
        
        $sql = "SELECT COUNT(t.id) AS count
        FROM troubles AS t
        LEFT JOIN troubles_deadline AS td ON (td.tid = t.id)
        LEFT JOIN troubles_objects AS t_o ON (t_o.tid = t.id)
        WHERE t.secure = 0
        	AND t_o.oid = :oid
            AND t.close = 0
            AND td.type = 2";
        
        $res = $this->registry['db']->prepare($sql);
		$param = array(":oid" => $oid);
		$res->execute($param);
		$rows = $res->fetchAll(PDO::FETCH_ASSOC);
        $data["iter"] = $rows[0]["count"];
        
        $sql = "SELECT COUNT(id) AS count
        FROM troubles
        WHERE troubles.secure = 0
        	AND troubles.oid = :oid
            AND troubles.close = 1";
        
        $res = $this->registry['db']->prepare($sql);
		$param = array(":oid" => $oid);
		$res->execute($param);
		$rows = $res->fetchAll(PDO::FETCH_ASSOC);
        $data["close"] = $rows[0]["count"];
        
        return $data;
    }
    
    /**
     * Получить развёрнутое описание объекта (история, координаты и т.п.)
     * 
     * @param int $id
     * @return array
     * @return boolean FALSE
     */
    public function getObject($id) {
        $rows = FALSE;
        
        $sql = "SELECT o.id AS id, o.timestamp AS `timestamp`, o.template AS tid, o.fid AS fdirid, t.id AS tid, t.name AS tname, ov.fid AS fid, tf.field AS `field`, tf.main AS `main`, tf.type AS `type`, tf.datatype AS datatype, ov.val AS val, o.uid AS auid, author.name AS aname, author.soname AS asoname, o.timestamp AS adate, ov.uid AS euid, editor.name AS ename, editor.soname AS esoname, ov.timestamp AS edate
				FROM objects AS o
                LEFT JOIN objects_vals AS ov ON (ov.oid = o.id)
                LEFT JOIN templates_fields AS tf ON (tf.id = ov.fid)
                LEFT JOIN templates AS t ON (t.id = tf.tid)
                LEFT JOIN users AS author ON (author.id = o.uid)
                LEFT JOIN users AS editor ON (editor.id = ov.uid)
                WHERE o.id = :id
                ORDER BY fid";
                
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
		$rows = $res->fetchAll(PDO::FETCH_ASSOC);
		
		$template = new Template();

		$fields = $template->getTypeTemplate($rows[0]["tid"]);
		for($i=0; $i<count($fields); $i++) {
			$flag = false;

			for($j=0; $j<count($rows); $j++) {

				if (isset($rows[$j]["val"])) {
					$rows[$j]["formatval"] = nl2br($rows[$j]["val"]);
					$search=array("\n", "\r");
					$rows[$j]["formatval"]=str_replace($search,"", $rows[$j]["formatval"]);
				}

				if (isset($rows[$j]["field"])) {
					if ($fields[$i]["field"] == $rows[$j]["field"]) {
						$flag = true;
						$rows[$i]["sel"] = $template->getDataVals($rows[$i]["datatype"]);
					}
				}
			}
			
			if (!$flag) {
				$rows[] = $fields[$i];
			}
		}

		$sql = "SELECT `email`
		FROM mail_contacts
		WHERE oid = :id";
		
    	$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
		$email = $res->fetchAll(PDO::FETCH_ASSOC);
		
		if ( (count($email)) and ($email[0]["email"] != "") ) {
			$rows[0]["email"] = $email[0]["email"];
		}
        
        if ($rows) {
            $fields = $template->getTemplate($rows[0]["tid"]);
        } else {
            return FALSE;
        }
        
        foreach($fields as $part) {
            $flag = FALSE;
            foreach($rows as $row) {
                if ($part["fid"] == $row["fid"]) {
                    $flag = TRUE;
                }
            }
            
            if (!$flag) {
                $rows[] = $part;
            }
        }

        for($i=0; $i<count($rows); $i++) {
        	$sql = "SELECT fid, x, y, w, h
        		FROM templates_view
        		WHERE tid = :tid";
        		
        	$res = $this->registry['db']->prepare($sql);
        	$param = array(":tid" => $rows[0]["tid"]);
        	$res->execute($param);
        	$data = $res->fetchAll(PDO::FETCH_ASSOC);
        }
        
        // если поле есть в таблице  templates_view, присвоим массиву $fields значения
        for ($i=0; $i<count($rows); $i++) {
        	for ($j=0; $j<count($data); $j++) {
        		if ($rows[$i]["fid"] == $data[$j]["fid"]) {
        			$rows[$i]["view"] = $data[$j];
        		}
        	}
        }
        
        // sort array
        for ($i=0; $i<count($rows); $i++) {
        	for ($j=0; $j<count($rows); $j++) {
        		if ((isset($rows[$i]["view"]["y"])) and (isset($rows[$j]["view"]["y"]))) {
	        		if ($rows[$i]["view"]["y"] < $rows[$j]["view"]["y"]) {
	        			$min = $rows[$i];
	        			$rows[$i] = $rows[$j];
	        			$rows[$j] = $min;
        		}
        		}
        	}
        }

		return $rows;
    }
    
    /**
     * Получить структуру дерева объектов
     *  
     * @return array
     */
    public function getObjsTree() {
    	$data = array();
    	
    	$sql = "SELECT o.id AS id, t.name AS tname
    	                    FROM objects AS o
    	                    LEFT JOIN templates AS t ON (t.id = o.template)
    	                    ORDER BY o.id DESC";
    	
    	$res = $this->registry['db']->prepare($sql);
    	$res->execute();
    	$data = $res->fetchAll(PDO::FETCH_ASSOC);

    	return $data;
    }
    
    /**
     * Получить ID объектов по ID шаблона (например: все клиенты)
     * 
     * @param int $id
     * @return array
     */
    public function getObjects($id) {
        $data = array();
        
		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT(o.id) AS id
			FROM objects AS o
			WHERE o.template = :id AND o.trash = 0
			ORDER BY o.id DESC
			LIMIT " . $this->startRow .  ", " . $this->limit;
                    
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		$this->totalPage = $this->registry['db']->query("SELECT FOUND_ROWS()")->fetchColumn();
		
		if ($this->totalPage < $this->limit+1)  {
		} else {
			$this->Pager();
		}

        return $data;
    }
    
    /**
     * Получить ID объектов в "корзине" по ID шаблона (например: все удалённые клиенты)
     * 
     * @param int $id
     * @return array
     */
    public function getTrashObjects($id) {
    	$data = array();
    
    	$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT(o.id) AS id
    			FROM objects AS o
    			WHERE o.template = :id AND o.trash = 1
    			ORDER BY o.id DESC
    			LIMIT " . $this->startRow .  ", " . $this->limit;
    
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":id" => $id);
    	$res->execute($param);
    	$data = $res->fetchAll(PDO::FETCH_ASSOC);
    
    	$this->totalPage = $this->registry['db']->query("SELECT FOUND_ROWS()")->fetchColumn();
    
    	if ($this->totalPage < $this->limit+1)  {
    	} else {
    		$this->Pager();
    	}
    
    	return $data;
    }
    
    /**
     * Выборка объектов по заданному в поиске критерию(-ям)
     * 
     * @param int $id
     * @param array $search
     * @return array
     */
    public function getObjectsSearch($id, $search) {

    	$where_str = null; $where = array(); $join = array(); $join_str = null; $i = 0;
    	foreach($search["sort"] as $key=>$part) {
    		$join[] = "LEFT JOIN objects_vals AS ov_" . $i . " ON (ov_" . $i . ".oid = o.id)";
    		$where[] = "ov_" . $i . ".fid = " . $key;
    		if ($part == "%") {
    			$where[] = "ov_" . $i . ".val LIKE '%" . $search["name"][$key] . "%'";
    		} elseif ($part == ">") {
    			$where[] = "ov_" . $i . ".val  > '" . $search["name"][$key] . "'";
    		} elseif ($part == "<") {
    			$where[] = "ov_" . $i . ".val  < '" . $search["name"][$key] . "'";
    		} elseif ($part == "=") {
    			$where[] = "ov_" . $i . ".val  = '" . $search["name"][$key] . "'";
    		} elseif ($part == "!=") {
    			$where[] = "ov_" . $i . ".val != '" . $search["name"][$key] . "'";
    		}
    		$i++;
    	}
    	$join_str = implode(" ", $join);
    	
    	$where_str = implode(" AND ", $where);
    	if ($where_str != null) {
    		$where_str = " AND " . $where_str;
    	}
		
    	$data = array();

    	$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT(o.id) AS id
    				FROM objects AS o
    				" . $join_str . "
    				WHERE o.template = :id " . $where_str . "
    				ORDER BY o.id DESC
    				LIMIT " . $this->startRow .  ", " . $this->limit;
    	
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":id" => $id);
    	$res->execute($param);
    	$data = $res->fetchAll(PDO::FETCH_ASSOC);
    	
    	$this->totalPage = $this->registry['db']->query("SELECT FOUND_ROWS()")->fetchColumn();
    	
    	if ($this->totalPage < $this->limit+1)  {
    	} else {
    		$this->Pager();
    	}
    	
    	return $data;
    }
    
    /**
     * Правка объекта
     * 
     * @param array $post
     * @param int $uid
     *    if !$uid else $uid = $this->registry["ui"]["id"]
     */
    public function editObject($post, $uid = 0) {
    	if ($uid == 0) {
    		$uid = $this->registry["ui"]["id"];
    	} else {
    		$this->registry["logs"]->uid = $uid;
    	}
    	
    	$template = new Template();
    	
        foreach ($post as $key=>$val) {
            if (is_int($key)) {
                $sql = "REPLACE INTO objects_vals (val, oid, fid, uid) VALUES (:val, :oid, :fid, :uid)";
                
                $res = $this->registry['db']->prepare($sql);
                $param = array(":oid" => $post["tid"], ":fid" => $key, ":val" => strip_tags($val, "<a>"), ":uid" => $uid);
                $res->execute($param);
                
                $obj[$key] = strip_tags($val, "<a>");

				$field = $this->getObject($post["tid"]);
        		foreach($field as $part) {
        			if ($part["fid"] == $key) $logs_obj[$part["field"]] = strip_tags($val, "<a>");
        		}
            }
        }
        
        if (isset($post["email"])) {
        	$contact = & $_SESSION["contact"];
        	unset($contact["email"]);
        	
        	$data = array();
        	
        	$sql = "SELECT id FROM mail_contacts WHERE oid = :oid LIMIT 1";
	        
	        $res = $this->registry['db']->prepare($sql);
	        $param = array(":oid" => $post["tid"]);
	        $res->execute($param);
	        $data = $res->fetchAll(PDO::FETCH_ASSOC);
	        
	        if (count($data) == 0) {        	
	        	$sql = "INSERT INTO mail_contacts (oid, email) VALUES (:oid, :email)";
	        
	        	$res = $this->registry['db']->prepare($sql);
	        	$param = array(":oid" => $post["tid"], ":email" => $post["email"]);
	        	$res->execute($param);
	        } else {
	        	if ($post["email"] == "") {
	        		$sql = "DELETE FROM mail_contacts WHERE oid = :oid LIMIT 1";
	        
	        		$res = $this->registry['db']->prepare($sql);
	        		$param = array(":oid" => $post["tid"]);
	        		$res->execute($param);
	        	} else {
	        		$sql = "UPDATE mail_contacts SET email = :email WHERE oid = :oid LIMIT 1";
	        
	        		$res = $this->registry['db']->prepare($sql);
	        		$param = array(":oid" => $post["tid"], ":email" => $post["email"]);
	        		$res->execute($param);
	        	}
	        }
    	}
        
        $string = "Правка объекта <a href='" . $this->registry["uri"] . "objects/show/" . $post["tid"] . "/'>" . $post["tid"] . "</a>";

    	$this->registry["logs"]->set("obj", $string, $post["tid"], $logs_obj);
    }

    /**
     * Получить email по ID объекта
     * 
     * @param int $oid
     * @return string
     */
    public function getEmailFromOid($oid) {
    	$data = array();
    	
    	$sql = "SELECT `email` FROM mail_contacts WHERE oid = :oid LIMIT 1";
    	
    	$res = $this->registry['db']->prepare($sql);
        $param = array(":oid" => $oid);
        $res->execute($param);
        $data = $res->fetchAll(PDO::FETCH_ASSOC);
        
        if ( (count($data) == 1) and ($data[0]["email"] != null) ) {
        	return $data[0]["email"];
        }
    }
    
    /**
     * Deprecated
     * Получить ID объекта по значению одного из полей (должно быть уникальным)
     *  
     * @param int $tid
     * @param string $uniqId
     */
    public function getOidFromUniqId($tid, $uniqId) {
    	$sql = "SELECT ov.oid 
    	FROM objects_vals AS ov
    	LEFT JOIN objects AS o ON (o.id = ov.oid)
    	WHERE ov.val = :val AND o.template = :tid
    	LIMIT 1";
    	 
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":tid" => $tid, ":val" => $uniqId);
    	$res->execute($param);
    	$data = $res->fetchAll(PDO::FETCH_ASSOC);
    	
    	if (count($data) > 0) {
    		return $data[0]["oid"];
    	}
    }
    
    /**
     * Удалить объект
     * 
     * @param string $key (кодированная строка, получается, как key из нормализованного JSON массива)
     */
    public function removeObject($key) {
    	$id = mb_substr($key, mb_strpos($key, "[") + 1, mb_strpos($key, "]") - mb_strpos($key, "[") - 1);
    	
    	$sql = "UPDATE objects SET trash = 1 WHERE id = :id LIMIT 1";
    	
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":id" => $id);
    	$res->execute($param);
    }
    
    /**
     * Восстановить объект из "корзины"
     * 
     * @param string $key (кодированная строка, получается, как key из нормализованного JSON массива)
     */
    public function repairObject($key) {
    	$id = mb_substr($key, mb_strpos($key, "[") + 1, mb_strpos($key, "]") - mb_strpos($key, "[") - 1);
    	 
    	$sql = "UPDATE objects SET trash = 0 WHERE id = :id LIMIT 1";
    	 
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":id" => $id);
    	$res->execute($param);
    }
}
?>
