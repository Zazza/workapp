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
 * Model\Ai class
 *
 * Класс-модель для работы базой знаний и формами для информации в базе знаний
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Ai extends Model {
	/**
	 * @var array
	 */
	private $_aiinfo;

	/**
	 * Получить информацию по ID объекта
	 * 
	 * @param int $id
	 * @return array $data
	 */
    public function getAdvancedInfo($id) {
        $rows = FALSE; $data = array();
        
        $sql = "SELECT oa.oid AS id, oa.id AS oaid, oa.title AS `title`, oa.val AS val, oa.timestamp AS `timestamp`, u.id AS uid, u.name AS uname, u.soname AS usoname
                FROM objects_advanced AS oa
                LEFT JOIN users AS u ON (u.id = oa.who)
                WHERE oa.oid = :id
                ORDER BY oa.id DESC";
                
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
		$rows = $res->fetchAll(PDO::FETCH_ASSOC);

        for($i=0; $i<count($rows); $i++) {
            $sql = "SELECT ot.tag
                    FROM objects_tags AS ot
                    WHERE ot.oaid = :oaid
                    ORDER BY ot.id";
                    
    		$res = $this->registry['db']->prepare($sql);
    		$param = array(":oaid" => $rows[$i]["oaid"]);
    		$res->execute($param);
            $rtags = $res->fetchAll(PDO::FETCH_ASSOC);
            
            $tags = array();
            foreach($rtags as $tag) {
                $tags["tags"][] = $tag["tag"];
            }
            
    		$data[$i] = array_merge($rows[$i], $tags);
        }

        return $data;
    }
    
    /**
     * Получить информацию по ID
     * 
     * @param int $oaid
     * @return array - поля, координаты
     *    $this->_aiinfo - информация
     */
    public function getAdvanced($oaid) {
        $row = FALSE;
        
        $sql = "SELECT oa.id, oa.oid, oa.fid AS tid, oa.title, oa.val, oa.who AS auid, author.name AS aname, author.soname AS asoname, oa.timestamp AS adate, oa.euid AS euid, editor.name AS ename, editor.soname AS esoname, oa.edittime AS edate
        FROM objects_advanced AS oa
        LEFT JOIN users AS author ON (author.id = oa.who)
        LEFT JOIN users AS editor ON (editor.id = oa.euid)
        WHERE oa.id = :oaid";
        
		$res = $this->registry['db']->prepare($sql);
		$param = array(":oaid" => $oaid);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
		
		$this->_aiinfo = $row[0];
		
		if ($row[0]["tid"] != 0) {
			$result["form"] = true;
			
			$i = 0;
			$json = json_decode($row[0]["val"], true);
			
			foreach($json as $key=>$val) {
			
				$sql = "SELECT id
						FROM objects_forms_fields
						WHERE field = :field
						LIMIT 1";
				
				$res = $this->registry['db']->prepare($sql);
				$param = array(":field" => $key);
				$res->execute($param);
				$data_fields = $res->fetchAll(PDO::FETCH_ASSOC);

				if (isset($data_fields[0]["id"])) {
					$sql = "SELECT fid, x, y, w, h
				        	FROM objects_forms_view
				        	WHERE fid = :fid
							LIMIT 1";
				
					$res = $this->registry['db']->prepare($sql);
					$param = array(":fid" => $data_fields[0]["id"]);
					$res->execute($param);
					$data = $res->fetchAll(PDO::FETCH_ASSOC);
					
					$result[$i]["key"] = $key;
					$result[$i]["val"] = $val;
					$result[$i]["view"] = $data[0];
					
					$i++;
				}
			}
	    } else {
	    	$result = $row[0];
	    }

        return $result;
    }

    /**
     * Getter $this->_aiinfo
     */
    public function getaiinfo() {
    	return $this->_aiinfo;
    }
    
    /**
     * Правка информации
     * 
     * @param int $oaid
     * @param string $title
     * @param string $text
     */    
    public function editAdvanced($oaid, $title, $text) {
        $sql = "UPDATE objects_advanced SET title = :title, val = :val, euid = :uid, edittime = NOW() WHERE id = :oaid LIMIT 1";
        
		$res = $this->registry['db']->prepare($sql);
		$param = array(":title" => $title, ":val" => $text, ":oaid" => $oaid, ":uid" => $this->registry["ui"]["id"]);
		$res->execute($param);

    	$data = $this->getAdvanced($oaid);
    	
    	$string = "Edit object info <a href='" . $this->registry["uri"] . "objects/" . $data["oid"] . "/'>" . $data["oid"] . "</a>";
    	
    	
    	$obj["Title"] = $title;
    	$obj["Text"] = $text;
    	
    	$this->registry["logs"]->set("info", $string, $oaid, $obj);
    }
    
    /**
     * Получить количество информации для объекта
     * 
     * @param int $id
     * @return int
     */
    public function getNumAdvancedInfo($id) {
        $rows = FALSE;
        
        $sql = "SELECT COUNT(id) AS count
                FROM objects_advanced
                WHERE oid = :id";
                
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
        
        return $row[0]["count"];
    }
    
    /**
     * Добавить информацию
     * 
     * @param int $oid
     * @param string $title
     * @param string $text
     * @return int $oaid - ID новой записи
     */
    public function addAdvanced($oid, $title, $text) {
        if ($text != "") {
            $sql = "INSERT INTO objects_advanced (oid, title, val, who) VALUES (:oid, :title, :val, :who)";
        
            $res = $this->registry['db']->prepare($sql);
            $param = array(":oid" => $oid, ":title" => $title, ":val" => $text, ":who" =>$this->registry["ui"]["id"]);
            $res->execute($param);
            
    		$oaid = $this->registry['db']->lastInsertId();
            
            $data = $this->getAdvanced($oaid);
            
    		$string = "Add object info <a href='" . $this->registry["uri"] . "objects/" . $data["oid"] . "/'>" . $data["oid"] . "</a>";
    	
    		$obj["Title"] = $title;
    		$obj["Text"] = $text;
    	
    		$this->registry["logs"]->set("info", $string, $oaid, $obj);
    		
    		return $oaid;
        }
    }
    
    /**
     * Удалить информацию
     * 
     * @param int $oaid
     */
    public function delAdvanced($oaid) {
    	$data = $this->getAdvanced($oaid);
    	
    	if ($data["oid"] == 0) {
    		$string = "Delete info";
    	} else {
    		$string = "Delete object info <a href='" . $this->registry["uri"] . "objects/" . $data["oid"] . "/'>" . $data["oid"] . "</a>";
    	}
    	
    	$obj["Title"] = $data["title"];
    	
    	$log_text = null;
    	if ($post = json_decode($data["val"])) {
    		foreach($post as $key=>$val) {
    			$log_text .= "<b>" . $key . "</b>: " . $val . " ";
    		}
    	} else {
    		$log_text = $data["val"];
    	}
    	
    	$obj["Text"] = $log_text;
    	
    	$this->registry["logs"]->set("info", $string, $oaid, $obj);
    	
        $sql = "DELETE FROM objects_advanced WHERE id = :oaid";
    
        $res = $this->registry['db']->prepare($sql);
        $param = array(":oaid" => $oaid);
        $res->execute($param);
        
        $sql = "DELETE FROM objects_tags WHERE oaid = :oaid";
    
        $res = $this->registry['db']->prepare($sql);
        $param = array(":oaid" => $oaid);
        $res->execute($param);
    }
    
    /**
     * Получить всю информацию
     * 
     * @return array
     */
    public function getAi() {
        $data = array();
        
        $sql = "SELECT oa.id, oa.oid, oa.val, t.name, ot.tag
        FROM objects_advanced AS oa
        LEFT JOIN objects AS o ON (o.id = oa.oid)
        LEFT JOIN templates AS t ON (t.id = o.template)
        LEFT JOIN objects_tags AS ot ON (ot.oaid = oa.id)
        WHERE ot.id != ''
        GROUP BY ot.tag
        ORDER BY t.name";
        
        $res = $this->registry['db']->prepare($sql);
        $res->execute();
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
        
        return $data;
    }

    /**
     * Получить информацию по тегу
     * 
     * @param string $tag
     * @return array
     */
    public function getAIFromTag($tag) {
        
        $sql = "SELECT oaid FROM objects_tags WHERE tag = :tag ORDER BY id DESC";	
        	
        $res = $this->registry['db']->prepare($sql);
        $param = array(":tag" => $tag);
        $res->execute($param);
		$oaids = $res->fetchAll(PDO::FETCH_ASSOC);
        
        $data = array();
        for($i=0; $i<count($oaids); $i++) {
        	$data[$i] = $this->getAdvanced($oaids[$i]["oaid"]);
		}

        return $data;
    }

    /**
     * Изменить теги
     * 
     * @param int $oaid
     * @param string $tags
     */
    public function changeTags($oaid, $tags) {
        
        $sql = "DELETE FROM objects_tags WHERE oaid = :oaid";
    
        $res = $this->registry['db']->prepare($sql);
        $param = array(":oaid" => $oaid);
        $res->execute($param);
        
        $arr = explode(",", $tags);
		$arr = array_unique($arr);
        foreach($arr as $part) {
            $tag = trim($part);
            if ($tag != "") {
                $this->addTags($oaid, $tag);
            }
        }
    }
    
    /**
     * Добавить тег (один)
     * 
     * @param int $oaid
     * @param string $tag
     */
    public function addTags($oaid, $tag) {
        $sql = "REPLACE INTO objects_tags (oaid, tag) VALUES (:oaid, :tag)";
    
        $res = $this->registry['db']->prepare($sql);
        $param = array(":oaid" => $oaid, ":tag" => $tag);
        $res->execute($param);
    }
    
    /**
     * Получить теги
     * 
     * @param int $oaid
     * @return array
     */
    public function getTags($oaid) {
        $tags = array();
        
        $sql = "SELECT tag FROM objects_tags WHERE oaid = :oaid";	
        	
        $res = $this->registry['db']->prepare($sql);
        $param = array(":oaid" => $oaid);
        $res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
        
        $tags = array();
        foreach($data as $tag) {
            $tags[] = $tag["tag"];
        }
        
        return $tags;
    }

    /**
     * Получить список форм
     * 
     * @return array
     */
    public function getForms() {
    	$data = array();
    	
    	$sql = "SELECT id, name FROM objects_forms ORDER BY id";
    	$res = $this->registry['db']->prepare($sql);
    	$res->execute();
    	$data = $res->fetchAll(PDO::FETCH_ASSOC);
    	
    	return $data;
    }
    
    /**
     * Получить имя поля по его ID
     * 
     * @param int $id
     * @return string
     */
    public function getFormFieldFromId($id) {
    	$data = array();
    	
    	$sql = "SELECT field FROM objects_forms_fields WHERE id = :id LIMIT 1";

    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":id" => $id);
    	$res->execute($param);
    	$data = $res->fetchAll(PDO::FETCH_ASSOC);
    	 
    	return $data[0]["field"];
    }
    
    /**
     * Получить имя формы по её ID
     * 
     * @param int $id
     * @return string
     */
    public function getFormName($id) {
    	$data = array();
    	 
    	$sql = "SELECT f.name AS `name`
    	        FROM objects_forms AS f
    	        WHERE f.id = :id";
    	
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":id" => $id);
    	$res->execute($param);
    	$data = $res->fetchAll(PDO::FETCH_ASSOC);
    	
    	return $data[0]["name"];
    }
    
    /**
     * Получить форму (имя, поля, тип, координаты) по её ID
     * 
     * @param int $id
     * @return array
     */
    public function getForm($id) {
    	$data = array();
    	
    	$sql = "SELECT f.id AS id, f.name AS `name`, ff.id AS fid, ff.field AS `field`, ff.type AS `type`, ff.datatype
        FROM objects_forms AS f
        LEFT JOIN objects_forms_fields AS ff ON (f.id = ff.tid)
        WHERE f.id = :id
        ORDER BY ff.id";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
		$fields = $res->fetchAll(PDO::FETCH_ASSOC);
        
		$templates = new Template();
		
		for($i=0; $i<count($fields); $i++) {
			$fields[$i]["sel"] = $templates->getDataVals($fields[$i]["datatype"]);
		}
		
		$sql = "SELECT fid, x, y, w, h
					FROM objects_forms_view
					WHERE tid = :tid";
			
		$res = $this->registry['db']->prepare($sql);
		$param = array(":tid" => $id);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
			
		// если поле есть в таблице  templates_view, присвоим массиву $fields значения
		for ($i=0; $i<count($fields); $i++) {
			for ($j=0; $j<count($data); $j++) {
				if ($fields[$i]["fid"] == $data[$j]["fid"]) {
					$fields[$i]["view"] = $data[$j];
				}
			}
		}
		
		// sort array
		for ($i=0; $i<count($fields); $i++) {
			for ($j=0; $j<count($fields); $j++) {
				if ( (isset($fields[$i]["view"]["y"])) and (isset($fields[$j]["view"]["y"])) ) {
					if ($fields[$i]["view"]["y"] < $fields[$j]["view"]["y"]) {
						$min = $fields[$i];
						$fields[$i] = $fields[$j];
						$fields[$j] = $min;
					}
				}
			}
		}

        return $fields;
    }
    
    /**
     * Создать форму (имя, поля)
     * 
     * @param array $post
     *    $post["name"]
     *    $post["field"]
     *    $post["datatype"]
     *    $post["type"]
     */
    public function addForm($post) {
    	if ($post["name"] != '') {
    		$sql = "INSERT INTO objects_forms (`name`) VALUES (:name)";
    	
    		$res = $this->registry['db']->prepare($sql);
    		$param = array(":name" => htmlspecialchars($post["name"]));
    		$res->execute($param);
    	
    		$tid = $this->registry['db']->lastInsertId();
    	
    		if (isset($post["field"])) {
    			if (count($post["field"]) > 0) {
    				for($i=0; $i<count($post["field"]); $i++) {

    					if (!isset($post["datatype"][$i])) {
    						$post["datatype"][$i] = 0;
    					}
    	
    					$sql = "INSERT INTO objects_forms_fields (`tid`, `field`, `type`, `datatype`) VALUES (:tid, :field, :type, :datatype)";
    	
    					$res = $this->registry['db']->prepare($sql);
    					$param = array(":tid" => $tid, ":field" => htmlspecialchars($post["field"][$i]), ":type" => $post["type"][$i], ":datatype" => $post["datatype"][$i]);
    					$res->execute($param);
    				}
    			}
    		}
    	}
    }
    
    /**
     * Удалить форму
     * 
     * @param int $id - ID формы
     */
    public function delForm($id) {
    	$sql = "DELETE FROM objects_forms WHERE id = :id LIMIT 1";
    	
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":id" => $id);
    	$res->execute($param);
    	
    	$sql = "DELETE FROM objects_forms_fields WHERE tid = :tid";
    	 
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":tid" => $id);
    	$res->execute($param);
    }
    
    /**
     * Правка формы
     * 
     * @param int $tid - ID формы
     * @param array $post
     *    $post["name"]
     *    $post["field"]
     *    $post["datatype"]
     *    $post["type"]
     */
    public function editForm($tid, $post) {
    	$sql = "UPDATE objects_forms SET `name` = :name WHERE id = :tid LIMIT 1";
    
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":tid" => $tid, ":name" => htmlspecialchars($post["name"]));
    	$res->execute($param);
    
    	if (isset($post["field"])) {
    		if (count($post["field"]) > 0) {
    			foreach ($post["field"] as $key=>$part) {
    				if (!isset($post["datatype"][$key])) {
    						$post["datatype"][$key] = 0;
    				}
    
    				if ( ($part != "") and ($post["new"][$key] == 0) ) {
    					$sql = "REPLACE INTO objects_forms_fields (`id`, `tid`, `field`, `type`, `datatype`) VALUES (:id, :tid, :field, :type, :datatype)";
    
    					$res = $this->registry['db']->prepare($sql);
    					$param = array(":id" => $key, ":tid" => $tid, ":field" => htmlspecialchars($part), ":type" => $post["type"][$key], ":datatype" => $post["datatype"][$key]);
    					$res->execute($param);
    				} elseif ($post["new"][$key] == 1) {
    					$sql = "INSERT INTO objects_forms_fields (`tid`, `field`, `type`, `datatype`) VALUES (:tid, :field, :type, :datatype)";
    
    					$res = $this->registry['db']->prepare($sql);
    					$param = array(":tid" => $tid, ":field" => htmlspecialchars($part), ":type" => $post["type"][$key], ":datatype" => $post["datatype"][$key]);
    					$res->execute($param);
    				} elseif ($part == "") {
    					$sql = "DELETE FROM objects_forms_fields WHERE id = :id AND tid = :tid LIMIT 1";
    
    					$res = $this->registry['db']->prepare($sql);
    					$param = array(":id" => $key, ":tid" => $tid);
    					$res->execute($param);
    				}
    			}
    		}
    	}
    }
    
    /**
     * Добавление информации к объекту по форме
     * 
     * @param int $oid - ID объекта
     * @param int $fid - ID формы
     * @param string $title - навзвание информации
     * @param array $post
     */
    public function addObjectFormInfo($oid, $fid, $title, $post) {
    	$log_text = null;
    	foreach($post as $key=>$val) {
    		$new_key = $this->getFormFieldFromId($key);
    		$text[$new_key] = $val;
    		
    		$log_text .= "<b>" . $new_key . "</b>: " . $val . " ";
    	}
    	
    	$text = json_encode($text);
    	
		$sql = "INSERT INTO objects_advanced (oid, fid, title, val, who) VALUES (:oid, :fid, :title, :val, :who)";
        
		$res = $this->registry['db']->prepare($sql);
		$param = array(":oid" => $oid, ":fid" => $fid, ":title" => $title, ":val" => $text, ":who" => $this->registry["ui"]["id"]);
		$res->execute($param);
            
		$oaid = $this->registry['db']->lastInsertId();
            
		$data = $this->getAdvanced($oaid);
            
		$string = "Add object info <a href='" . $this->registry["uri"] . "objects/" . $data["oid"] . "/'>" . $data["oid"] . "</a>";
    	
		$obj["Title"] = $title;
		$obj["Text"] = $log_text;
    	
		$this->registry["logs"]->set("info", $string, $oaid, $obj);
    		
		return $oaid;
    }
    
    /**
     * Правка информации добавленной к объекту с помощью формы
     * 
     * @param int $oaid - ID информации
     * @param array $post
     */
    public function editObjectFormInfo($oaid, $post) {
    	foreach($post as $key=>$val) {
    		$new_key = $this->getFormFieldFromId($key);
    		$text[$new_key] = $val;
    	}
    	 
    	$text = json_encode($text);
    	
    	$sql = "SELECT oid FROM objects_advanced WHERE id = :id LIMIT 1";
    	
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":id" => $oaid);
    	$res->execute($param);
    	$row = $res->fetchAll(PDO::FETCH_ASSOC);
    	
    	$sql = "UPDATE objects_advanced SET val = :text, euid = :uid, edittime = NOW() WHERE oid = :oid LIMIT 1";
    	
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":oid" => $row[0]["oid"], ":text" => $text, ":uid" => $this->registry["ui"]["id"]);
    	$res->execute($param);
    	
    	$string = "Add object info <a href='" . $this->registry["uri"] . "objects/" . $row[0]["oid"] . "/'>" . $row[0]["oid"] . "</a>";
    	
    	$sql = "SELECT `title` FROM objects_advanced WHERE id = :id LIMIT 1";
    	 
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":id" => $oaid);
    	$res->execute($param);
    	$title = $res->fetchAll(PDO::FETCH_ASSOC);
    	
    	$obj["Title"] = $title[0]["title"];
    	
    	$log_text = null;
    	foreach($post as $key=>$val) {
    		$log_text .= "<b>" . $key . "</b>: " . $val . " ";
    	}
    	$obj["Text"] = $log_text;
    	 
    	$this->registry["logs"]->set("info", $string, $oaid, $obj);
    }
    
    /**
     * Получить отображение формы (координаты полей, размеры полей)
     * 
     * @param int $tid - ID формы
     * @return array
     */
    public function getTemplateView($tid) {
    	$sql = "SELECT id AS fid, tid, field, type
        	    				FROM objects_forms_fields
        	    				WHERE tid = :tid";
    
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":tid" => $tid);
    	$res->execute($param);
    	$fields = $res->fetchAll(PDO::FETCH_ASSOC);
    	 
    	$sql = "SELECT fid, x, y, w, h
        				FROM objects_forms_view
        				WHERE tid = :tid";
    	 
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":tid" => $tid);
    	$res->execute($param);
    	$data = $res->fetchAll(PDO::FETCH_ASSOC);
    	 
    	// если поле есть в таблице  templates_view, присвоим массиву $fields значения
    	for ($i=0; $i<count($fields); $i++) {
    		for ($j=0; $j<count($data); $j++) {
    			if ($fields[$i]["fid"] == $data[$j]["fid"]) {
    				$fields[$i]["view"] = $data[$j];
    			}
    		}
    	}
    	 
    	// если у поля нет значения Y, то дадим ему следующее свободное
    	$k=1;
    	for ($i=0; $i<count($fields); $i++) {
    		if (!isset($fields[$i]["view"]["y"])) {
    			$flag = true;
    			while ($flag) {
    				$key = false;
    				for ($j=0; $j<count($fields); $j++) {
    					if (isset($fields[$j]["view"]["y"])) {
    						if ($fields[$j]["view"]["y"] == $k) {
    							$k++;
    							$flag = true; $key = true;
    						}
    					}
    				}
    				if (!$key) {
    					$flag = false;
    				}
    			}
    			$fields[$i]["view"]["y"] = $k;
    			$k++;
    		}
    	}
    
    	// sort array
    	for ($i=0; $i<count($fields); $i++) {
    		for ($j=0; $j<count($fields); $j++) {
    			if ($fields[$i]["view"]["y"] < $fields[$j]["view"]["y"]) {
    				$min = $fields[$i];
    				$fields[$i] = $fields[$j];
    				$fields[$j] = $min;
    			}
    		}
    	}
    
    	return $fields;
    }
    
    /**
     * Сохранить отображение формы (координаты полей)
     * 
     * @param int $tid - ID формы
     * @param int $fid - ID поля
     * @param int $x - координата X поля
     * @param int $y - координата Y поля
     * @return array $data
     *    $data[0]["count"] = 0 - новая запись
     *    $data[0]["count"] = 1 - обновление записи
     */
    public function setTemplateViewXY($tid, $fid, $x, $y) {
    	$sql = "SELECT COUNT(id) AS count
        				FROM objects_forms_view
            			WHERE tid = :tid AND fid = :fid";
    
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":tid" => $tid, ":fid" => $fid);
    	$res->execute($param);
    	$data = $res->fetchAll(PDO::FETCH_ASSOC);
    
    	if ($data[0]["count"] == 0) {
    		$sql = "INSERT INTO objects_forms_view (tid, fid, x, y) VALUES (:tid, :fid, :x, :y)";
    	} else {
    		$sql = "UPDATE objects_forms_view SET x = :x, y =:y WHERE tid = :tid AND fid = :fid";
    	}
    	 
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":tid" => $tid, ":fid" => $fid, ":x" => $x, ":y" => $y);
    	$res->execute($param);
    
    	return $data;
    }
    
    /**
     * Сохранить отображение формы (размеры полей)
     *
     * @param int $tid - ID формы
     * @param int $fid - ID поля
     * @param int $w - ширина поля
     * @param int $h - высота поля
     * @return array $data
     *    $data[0]["count"] = 0 - новая запись
     *    $data[0]["count"] = 1 - обновление записи
     */
    public function setTemplateViewSize($tid, $fid, $w, $h) {
    	$sql = "SELECT COUNT(id) AS count
            				FROM objects_forms_view
                			WHERE tid = :tid AND fid = :fid";
    
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":tid" => $tid, ":fid" => $fid);
    	$res->execute($param);
    	$data = $res->fetchAll(PDO::FETCH_ASSOC);
    
    	if ($data[0]["count"] == 0) {
    		$sql = "INSERT INTO objects_forms_view (tid, fid, w, h) VALUES (:tid, :fid, :w, :h)";
    	} else {
    		$sql = "UPDATE objects_forms_view SET w = :w, h =:h WHERE tid = :tid AND fid = :fid";
    	}
    
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":tid" => $tid, ":fid" => $fid, ":w" => $w, ":h" => $h);
    	$res->execute($param);
    
    	return $data;
    }
}
?>
