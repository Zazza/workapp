<?php
class Model_Template extends Modules_Model {
    public function getTemplates() {
		$sql = "SELECT t.id AS id, t.name AS `name`
        FROM templates AS t
        ORDER BY t.id";
		
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
        
        return $data;
    }
    
    public function getTemplate($tid) {
		$sql = "SELECT t.id AS id, t.name AS `name`, f.id AS fid, f.field AS `field`, f.main AS `main`, f.type AS `type`, f.datatype AS datatype
        FROM templates AS t
        LEFT JOIN templates_fields AS f ON (t.id = f.tid)
        WHERE t.id = :tid
        ORDER BY f.id";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":tid" => $tid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
        
        return $data;
    }
    
    public function getField($fid) {
		$sql = "SELECT f.id AS fid, f.field AS `field`,  f.main AS `main`, f.type AS `type`, f.datatype AS datatype
		FROM templates_fields AS f
		WHERE f.id = :fid";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":fid" => $fid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		if ($data[0]["type"] == "2") {
			$data[0]["sel"] = $this->getDataVals($data[0]["datatype"]);
		}
        
        return $data[0];
	}
    
    public function getTypeTemplate($id) {
		$sql = "SELECT t.id AS id, t.name AS `name`, f.id AS fid, f.field AS `field`, f.main AS `main`, f.type AS `type`, f.datatype AS datatype
        FROM templates AS t
        LEFT JOIN templates_fields AS f ON (t.id = f.tid)
        WHERE t.id = :id
        ORDER BY f.id";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
		$fields = $res->fetchAll(PDO::FETCH_ASSOC);
		
		for($i=0; $i<count($fields); $i++) {
			$fields[$i]["sel"] = $this->getDataVals($fields[$i]["datatype"]);
		}
		
		$sql = "SELECT fid, x, y, w, h
			FROM templates_view
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
    
    public function getTidFromPid($oid) {
        $data = "0";
        
		$sql = "SELECT t.id AS id
        FROM templates AS t
        LEFT JOIN objects AS o ON (o.template = t.id)
        WHERE o.id = :oid
        LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":oid" => $oid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
        
        return $data[0]["id"];
    }
    
    public function delTemplate($tid) {
        $sql = "DELETE FROM templates WHERE id = :tid";
        
        $res = $this->registry['db']->prepare($sql);
        $param = array(":tid" => $tid);
        $res->execute($param);
        
        $sql = "DELETE FROM templates_fields WHERE tid = :tid";
        
        $res = $this->registry['db']->prepare($sql);
        $param = array(":tid" => $tid);
        $res->execute($param);
    }
    
    public function addTemplate($post) {
        if ($post["name"] != '') {
            $sql = "INSERT INTO templates (`name`) VALUES (:name)";
            
            $res = $this->registry['db']->prepare($sql);
            $param = array(":name" => htmlspecialchars($post["name"]));
            $res->execute($param);

    		$tid = $this->registry['db']->lastInsertId();
            
            if (isset($post["field"])) {
                if (count($post["field"]) > 0) {
                    for($i=0; $i<count($post["field"]); $i++) {
                        
                        if (!isset($post["main"][$i])) { $post["main"][$i] = 0; } else { $post["main"][$i] = 1; }
                        if (!isset($post["datatype"][$i])) { $post["datatype"][$i] = 0; }

                        $sql = "INSERT INTO templates_fields (`tid`, `field`, `main`, `type`, datatype) VALUES (:tid, :field, :main, :type, :datatype)";
                        
                        $res = $this->registry['db']->prepare($sql);
                        $param = array(":tid" => $tid, ":field" => htmlspecialchars($post["field"][$i]), ":main" => $post["main"][$i], ":type" => $post["type"][$i], ":datatype" => $post["datatype"][$i]);
                        $res->execute($param);
                    }
                }
            }
        }
    }
    
    public function editTemplate($tid, $post) {
        $sql = "UPDATE templates SET `name` = :name WHERE id = :tid LIMIT 1";
        
        $res = $this->registry['db']->prepare($sql);
        $param = array(":tid" => $tid, ":name" => htmlspecialchars($post["name"]));
        $res->execute($param);
        
        if (isset($post["field"])) {
            if (count($post["field"]) > 0) {
                foreach ($post["field"] as $key=>$part) {
                    if (!isset($post["main"][$key])) { $post["main"][$key] = 0; } else { $post["main"][$key] = 1; }
                    if (!isset($post["datatype"][$key])) { $post["datatype"][$key] = 0; }

                    if ( ($part != "") and ($post["new"][$key] == 0) ) {
                        $sql = "REPLACE INTO templates_fields (`id`, `tid`, `field`, `main`, `type`, datatype) VALUES (:id, :tid, :field, :main, :type, :datatype)";
                        
                        $res = $this->registry['db']->prepare($sql);
                        $param = array(":id" => $key, ":tid" => $tid, ":field" => htmlspecialchars($part), ":main" => $post["main"][$key], ":type" => $post["type"][$key], ":datatype" => $post["datatype"][$key]);
                        $res->execute($param);
                    } elseif ($post["new"][$key] == 1) {
                        $sql = "INSERT INTO templates_fields (`tid`, `field`, `main`, `type`, datatype) VALUES (:tid, :field, :main, :type, :datatype)";
                        
                        $res = $this->registry['db']->prepare($sql);
                        $param = array(":tid" => $tid, ":field" => htmlspecialchars($part), ":main" => $post["main"][$key], ":type" => $post["type"][$key], ":datatype" => $post["datatype"][$key]);
                        $res->execute($param);
                    } elseif ($part == "") {
                        $sql = "DELETE FROM templates_fields WHERE id = :id AND tid = :tid LIMIT 1";
                        
                        $res = $this->registry['db']->prepare($sql);
                        $param = array(":id" => $key, ":tid" => $tid);
                        $res->execute($param);
                    }
                }
            }
        }
    }
    
    public function getFidFromFname($tid, $fname) {
    	$sql = "SELECT id
        FROM templates_fields
        WHERE tid = :tid AND field = :fname
        LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":tid" => $tid, ":fname" => $fname);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
        
        return $data[0]["id"];
    }
    
    public function getDataTypes() {
    	$sql = "SELECT id, name
			FROM templates_datatypes
			ORDER BY name";
    
    	$res = $this->registry['db']->prepare($sql);
    	$res->execute();
    	$data = $res->fetchAll(PDO::FETCH_ASSOC);
    	
    	for($i=0; $i<count($data); $i++) {
    		$data[$i]["vals"] = $this->getDataVals($data[$i]["id"]);
    	}
    
    	return $data;
    }
    
    public function getDataVals($did) {
    	$sql = "SELECT id, val
			FROM templates_datavals
			WHERE did = :did
    		ORDER BY id";
    	
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":did" => $did);
		$res->execute($param);
    	$data = $res->fetchAll(PDO::FETCH_ASSOC);
    	
    	return $data;
    }
    
    public function addDataTree($id, $name) {
    	$sql = "INSERT INTO templates_datavals (did, `val`) VALUES (:did, :val)";
    	
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":did" => $id, ":val" => $name);
    	$res->execute($param);
    }
    
    public function delDataCat($id) {
    	$sql = "DELETE FROM templates_datavals WHERE id = :id LIMIT 1";
    	 
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":id" => $id);
    	$res->execute($param);
    }
    
    public function editDataCat($id, $name) {
    	$sql = "UPDATE templates_datavals SET val = :val WHERE id = :id";
    	 
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":id" => $id, ":val" => $name);
    	$res->execute($param);
    }
    
    public function getDataCatName($id) {
    	$sql = "SELECT val
			FROM templates_datavals
			WHERE id = :id
    		LIMIT 1";
    	 
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":id" => $id);
    	$res->execute($param);
    	$data = $res->fetchAll(PDO::FETCH_ASSOC);
    	
    	return $data[0]["val"];
    }
    
    public function rmAllData($did) {
    	$sql = "DELETE FROM templates_datavals WHERE did = :did";
    
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":did" => $did);
    	$res->execute($param);
    }
    
    public function addDataType($name) {
    	$sql = "INSERT INTO templates_datatypes (`name`) VALUES (:name)";
    	 
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":name" => $name);
    	$res->execute($param);
    }
    
    public function delDataType($id) {
    	$sql = "DELETE FROM templates_datatypes WHERE id = :id";
    	
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":id" => $id);
    	$res->execute($param);
    	
    	$sql = "DELETE FROM templates_datavals WHERE did = :did";
    	 
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":did" => $id);
    	$res->execute($param);
    }
    
    public function getTemplateView($tid) {
    	$sql = "SELECT id AS fid, tid, field, type
    	    				FROM templates_fields
    	    				WHERE tid = :tid";
    	 
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":tid" => $tid);
    	$res->execute($param);
    	$fields = $res->fetchAll(PDO::FETCH_ASSOC);
    	
    	$sql = "SELECT fid, x, y, w, h
    				FROM templates_view
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
	    			if (!$key) { $flag = false; }
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
    
    public function setTemplateViewXY($tid, $fid, $x, $y) {
    	$sql = "SELECT COUNT(id) AS count
    				FROM templates_view
        			WHERE tid = :tid AND fid = :fid";
    	 
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":tid" => $tid, ":fid" => $fid);
    	$res->execute($param);
    	$data = $res->fetchAll(PDO::FETCH_ASSOC);
 
    	if ($data[0]["count"] == 0) {
    		$sql = "INSERT INTO templates_view (tid, fid, x, y) VALUES (:tid, :fid, :x, :y)";
    	} else {
    		$sql = "UPDATE templates_view SET x = :x, y =:y WHERE tid = :tid AND fid = :fid";
    	}
    	
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":tid" => $tid, ":fid" => $fid, ":x" => $x, ":y" => $y);
    	$res->execute($param);
    
    	return $data;
    }
    
    public function setTemplateViewSize($tid, $fid, $w, $h) {
    	$sql = "SELECT COUNT(id) AS count
        				FROM templates_view
            			WHERE tid = :tid AND fid = :fid";
    
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":tid" => $tid, ":fid" => $fid);
    	$res->execute($param);
    	$data = $res->fetchAll(PDO::FETCH_ASSOC);
    	 
    	if ($data[0]["count"] == 0) {
    		$sql = "INSERT INTO templates_view (tid, fid, w, h) VALUES (:tid, :fid, :w, :h)";
    	} else {
    		$sql = "UPDATE templates_view SET w = :w, h =:h WHERE tid = :tid AND fid = :fid";
    	}
    	 
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":tid" => $tid, ":fid" => $fid, ":w" => $w, ":h" => $h);
    	$res->execute($param);
    
    	return $data;
    }
}
?>
