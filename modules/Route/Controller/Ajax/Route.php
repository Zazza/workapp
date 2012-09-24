<?php
class Controller_Ajax_Route extends Modules_Ajax {

	function addTask($params) {
		$rid = $params["rid"];
		$step_id = $params["step_id"];
		
		$routes = new Model_Route();
		$routes->addDraftRouteTask($rid, $step_id);
		
		echo $routes->getTid();
	}
	
	function getTasks($params) {
		$rid = $params["rid"];
		
		$routes = new Model_Route();
		echo json_encode($routes->getTasks($rid));
	}
	
	function getRealTasks($params) {
		$rid = $params["rid"];
	
		$routes = new Model_RealRoute();
		echo json_encode($routes->getTasks($rid));
	}
	
	function delTask($params) {
		$tid = $params["tid"];
		
		$routes = new Model_Route();
		$routes->delDraftRouteTask($tid);
	}
	
	function savebpname($params) {
		$rid = $params["rid"];
		$name = $params["name"];
		
		$routes = new Model_Route();
		$routes->setDraftRouteName($rid, $name);
	}
	
	function addstep($params) {
		$rid = $params["rid"];
	
		$routes = new Model_Route();
		$routes->addDraftStep($rid);
		
		$row["step_id"] = $routes->getStep_id();
		$row["tid"] = $routes->getTid();
		
		echo json_encode($row);
	}
	
	function stepremove($params) {
		$step_id = $params["step_id"];
		
		$routes = new Model_Route();
		$routes->delDraftStep($step_id);
	}
	
	function steprename($params) {
		$step_id = $params["step_id"];
		$name = $params["name"];
		
		$routes = new Model_Route();
		$routes->renameDraftStep($step_id, $name);
	}
	
	function delRoute($params) {
		$rid = $params["rid"];
		
		$routes = new Model_Route();
		$routes->delDraftRoute($rid);
	}
	
	function addstepbefore($params) {
		$rid = $params["rid"];
		$step_id = $params["step_id"];
		
		$routes = new Model_Route();
		$routes->addDraftStepBefore($rid, $step_id);
	}
	
	function getDatatype($params) {
		$datatype = $params["datatype"];
		
		$template = new Model_Template();
		$data = $template->getDataVals($datatype);

		echo $this->view->render("datatype", array("data" => $data));
	}
	
	function addGoto($params) {
		$ifdata = $params["ifdata"];
		$ifdataval = $params["ifdataval"];
		$ifcon = $params["ifcon"];
		$ifval = $params["ifval"];
		$goto =  $params["goto"];
		$gotoval =  $params["gotoval"];
		
		if ( (is_numeric($ifdata)) and ($ifcon != "") and ($ifval != "") and (is_numeric($goto)) ) {
			echo $this->view->render("goto", array("ifdata" => $ifdata, "ifdataval" => $ifdataval, "ifcon" => $ifcon, "ifval" => $ifval, "goto" => $goto, "gotoval" => $gotoval));
		}
	}
	
	function addRealRoute($params) {
		$rid = $params["rid"];
		
		$routes = new Model_Route();
		
		$routes->addRealRoutes($rid);
	}
	
	function delRealRoute($params) {
		$rid = $params["rid"];
	
		$routes = new Model_RealRoute();
		$routes->delRoute($rid);
	}
	
	function runProcess($params) {
		$rid = $params["rid"];
		
		$process = new Model_Process();
		$process->runProcess($rid);
	}
	
	function setResult($params) {
		$tid = $params["tid"];
	
		$process = new Model_Process();
		$form = $process->getTaskResultsForms($tid);
	
		if (count($form) > 0) {
			$template = new Model_Template();
			$datatypes = $template->getDataTypes();
			
			for($i=0; $i<count($form); $i++) {
				for($k=0; $k<count($datatypes); $k++) {
					if ($form[$i]["datatype"] == $datatypes[$k]["id"]) {
						$form[$i]["select"] = $datatypes[$k]["vals"];
					}
				}
			}

			echo $this->view->render("formresults", array("form" => $form));
		} else {
			//$params["results"] = array();
			//$this->closeTask($params);
			
			echo "null";
		}
	}
	
	function closeTask($params) {
		$tid = $params["tid"];
		$result = json_decode($params["result"], true);
		
		$process = new Model_Process();
		$process->closeTask($tid, $result);
	}
}
?>