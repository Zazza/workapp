<?php
class Model_Process extends Modules_Model {
	private $_cur_route;
	private $_real_route;
	private $_prev_tid;
	
	function runProcess($rid) {
		$sql = "INSERT INTO process (rid, start) VALUES (:rid, NOW())";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":rid" => $rid);
		$res->execute($param);
		
		$this->_cur_route = $this->registry['db']->lastInsertId();
		
		$this->addTaskFromRouteStep($rid, $this->getStepFromID($rid, 0));
	}
	
	function getStepFromID($rid, $step) {
		$sql = "SELECT rrt.step_id
				FROM route_route_tasks AS rrt
				LEFT JOIN route_step AS rs ON (rs.id = rrt.step_id)
				WHERE rrt.rid = :rid
				GROUP BY rrt.step_id
				ORDER BY rs.order";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":rid" => $rid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		if (isset($data[$step])) {
			return $data[$step]["step_id"];
		} else {
			return "#end";
		}
	}
	
	function addTaskFromRouteStep($rid, $step_id) {
		$sql = "SELECT rrt.step_id, rrt.tid, rs.name, rt.json
		FROM route_route_tasks AS rrt
		LEFT JOIN route_step AS rs ON (rs.id = rrt.step_id)
		LEFT JOIN route_tasks AS rt ON (rt.id = rrt.tid)
		WHERE rrt.rid = :rid AND step_id = :step_id
		ORDER BY rs.order";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":rid" => $rid, ":step_id" => $step_id);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		$tt = new Model_Tt();
		
		foreach($data as $part) {
			$results = $this->resultForStep($part["tid"]);
			$task = json_decode($part["json"], true);
			if (count($results) > 0) {
				foreach($results as $val) {
					$task = str_replace("$[" . $val["id"] . "]", $this->getResult($this->_cur_route, $val["id"]), $task);
				}
			}
			$tt->setRoute();
			$task["task"] = $task["textfield"];
			
			$task["startdate"] = date("Y-m'd");
			$task["starttime"] = date("H:i:s");
			
			if (is_numeric($task["selObjHid"])) {
				$tid = $tt->addTask($task["selObjHid"], $task);
			} else {
				$tid = $tt->addTask(0, $task);
			}
			
			$sql = "INSERT INTO process_tasks (rid, step_id, route_tid, tid) VALUES (:rid, :step_id, :route_tid, :tid)";
			
			$res = $this->registry['db']->prepare($sql);
			$param = array(":rid" => $this->_cur_route, ":step_id" => $step_id, ":route_tid" => $part["tid"], ":tid" => $tid);
			$res->execute($param);
		}
	}
	
	function resultForStep($tid) {
		$routes = new Model_RealRoute();
		
		$data = $routes->getTaskData($tid);
		return $data[0];
	}
	
	function getResult($rid, $key) {
		if (is_numeric($key)) {
			$sql = "SELECT ptr.val
			FROM process_tasks_results AS ptr
			LEFT JOIN process_tasks AS pt ON (pt.id = ptr.pid)
			WHERE pt.rid = :rid AND ptr.key = :key
			LIMIT 1";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":rid" => $rid, ":key" => $key);
			$res->execute($param);
			$row = $res->fetchAll(PDO::FETCH_ASSOC);
			
			return $row[0]["val"];
		}
	}
	
	function getTaskResultsForms($tid) {
		$sql = "SELECT route_tid FROM process_tasks WHERE tid = :tid LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":tid" => $tid);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
		
		$routes = new Model_RealRoute();

		$result =  $routes->getResult($row[0]["route_tid"]);
		
		return $result;
	}
	
	function closeTask($tid, $results) {
		$sql = "SELECT id, rid, step_id FROM process_tasks WHERE tid = :tid LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":tid" => $tid);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
		
		$pid = $row[0]["id"];
		$this->_cur_route = $row[0]["rid"];
		$step_id = $row[0]["step_id"];
		
		$sql = "SELECT rid FROM process WHERE id = :rid LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":rid" => $this->_cur_route);
		$res->execute($param);
		$process = $res->fetchAll(PDO::FETCH_ASSOC);
		
		$this->real_route = $process[0]["rid"];

		if (count($results) > 0) {
			foreach($results as $key=>$val) {
				$sql = "INSERT INTO process_tasks_results (pid, `key`, `val`) VALUES (:pid, :key, :val)";
				
				$res = $this->registry['db']->prepare($sql);
				$param = array(":pid" => $pid, ":key" => $key, ":val" => $val["val"]);
				$res->execute($param);
			}
		}

		$sql = "UPDATE process_tasks SET close = 1 WHERE tid = :tid LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":tid" => $tid);
		$res->execute($param);
			
		$tt = new Model_Tt();
		$tt->closeTask($tid);
		
		//Проверка не кончился ли step, если да - runAction()
		$sql = "SELECT close FROM process_tasks WHERE rid = :rid AND step_id = :step_id";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":rid" => $this->_cur_route, ":step_id" => $step_id);
		$res->execute($param);
		$process = $res->fetchAll(PDO::FETCH_ASSOC);
		
		$flag = true;
		foreach($process as $part) {
			if ($part["close"] == "0") { $flag = false; }
		}

		if ($flag) {
			$goto = $this->runAction($this->_cur_route, $step_id, $results);
			$this->_prev_tid = $tid;
			if ($goto != "#end") {
				$this->addTaskFromRouteStep($this->real_route, $goto);
			} else {
				$this->endProcess($this->_cur_route);
			}
		}
	}
	
	public function runAction($rid, $step_id, $results) {
		$goto = null;
		
		$routes = new Model_RealRoute();
		
		$action = $routes->getRouteAction($step_id);
		
		if (count($action) == 0) {
			$goto = $this->countStep($rid, $step_id);
		} else {
			foreach($action as $part) {
				$templates = new Model_Template();

				$sql = "SELECT datatype FROM route_tasks_results WHERE id = :ifdata LIMIT 1";
				
				$res = $this->registry['db']->prepare($sql);
				$param = array(":ifdata" => $part["ifdata"]);
				$res->execute($param);
				$row = $res->fetchAll(PDO::FETCH_ASSOC);
				
				$vals = $templates->getDataVals($row[0]["datatype"]);

				foreach($results as $key=>$val) {
	
					foreach($vals as $temps) {
						if ($temps["id"] == $val) {
							$id = $temps["val"];
						}
					}

					if ($key == $part["ifdata"]) {
						if ($part["ifcon"] == "=") {
							if ($id == $part["ifval"]) {
								$goto = $part["goto"];
							}
						} else if ($part["ifcon"] == "<") {
							if ($id < $part["ifval"]) {
								$goto = $part["goto"];
							}
						} else if ($part["ifcon"] == ">") {
							if ($id > $part["ifval"]) {
								$goto = $part["goto"];
							}
						} else if ($part["ifcon"] == "!=") {
							if ($id != $part["ifval"]) {
								$goto = $part["goto"];
							}
						}
					}
				}
			}

			if ($goto == null) {
				$goto = $this->countStep($rid, $step_id);
			}
		}
		
		return $goto;
	}
	
	public function countStep($rid, $step_id) { echo $rid . " " . $step_id;
		$sql = "SELECT `order` FROM route_step WHERE id = :step_id LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":step_id" => $step_id);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
		
		$sql = "SELECT rs.id
		FROM process AS p
		LEFT JOIN route_route_tasks AS rrt ON (rrt.rid = p.rid)
		LEFT JOIN route_step AS rs ON (rs.id = rrt.step_id)
		WHERE p.id = :rid AND rs.order > :order
		ORDER BY rs.order
		LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":rid" => $rid, ":order" => $row[0]["order"]);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
		
		if (isset($row[0])) {
			return $row[0]["id"];
		} else {
			return "#end";
		}
	}
	
	public function endProcess($rid) {
		$sql = "UPDATE process SET `end` = NOW() WHERE id = :rid LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":rid" => $rid);
		$res->execute($param);
	}
}
?>