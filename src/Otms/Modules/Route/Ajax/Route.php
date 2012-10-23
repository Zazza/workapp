<?php

/**
 * This file is part of the Workapp project.
 *
 * Route (Бизнес-процессы) Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Route\Ajax;

use Engine\Modules\Ajax;
use Otms\Modules\Route\Model;
use Otms\Modules\Objects\Model\Template;

/**
 * Ajax\Route class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Route extends Ajax {

	/**
	 * Добавить задачу в черновик
	 * 
	 * @param array
	 *    $params["rid"] - ID БП
	 *    $params["step_id"] - ID этапа
	 * @return int - ID новой задачи
	 */
	function addTask($params) {
		$rid = $params["rid"];
		$step_id = $params["step_id"];
		
		$routes = new Model\Route();
		$routes->addDraftRouteTask($rid, $step_id);
		
		return $routes->getTid();
	}
	
	/**
	 * Получить все задачи БП (черновик)
	 * 
	 * @param array
	 *    $params["rid"] - ID БП
	 * @return string - JSON array $routes->getTasks($rid)
	 */
	function getTasks($params) {
		$rid = $params["rid"];
		
		$routes = new Model\Route();
		return json_encode($routes->getTasks($rid));
	}
	
	/**
	 * Получить все задачи БП
	 * 
	 * @param array
	 *    $params["rid"] - ID БП
	 * @return string - JSON array $routes->getTasks($rid)
	 */
	function getRealTasks($params) {
		$rid = $params["rid"];
	
		$routes = new Model\RealRoute();
		return json_encode($routes->getTasks($rid));
	}
	
	/**
	 * Удалить задачу
	 * 
	 * @param array
	 *    $params["tid"] - ID задачи
	 */
	function delTask($params) {
		$tid = $params["tid"];
		
		$routes = new Model\Route();
		$routes->delDraftRouteTask($tid);
	}
	
	/**
	 * Сохранить имя БП
	 * 
	 * @param array
	 *    $params["rid"] - ID БП
	 *    $params["name"]
	 */
	function savebpname($params) {
		$rid = $params["rid"];
		$name = $params["name"];
		
		$routes = new Model\Route();
		$routes->setDraftRouteName($rid, $name);
	}
	
	/**
	 * Добавить этап в конец БП
	 * 
	 * @param array
	 *    $params["rid"] - ID БП
	 * @return string - JSON array
	 */
	function addstep($params) {
		$rid = $params["rid"];
	
		$routes = new Model\Route();
		$routes->addDraftStep($rid);
		
		$row["step_id"] = $routes->getStep_id();
		$row["tid"] = $routes->getTid();
		
		return json_encode($row);
	}
	
	/**
	 * Добавить этап до этапа c ID
	 * 
	 * @param array
	 *    $params["rid"] - ID БП
	 *    $params["step_id"] - ID этапа
	 */
	function addstepbefore($params) {
		$rid = $params["rid"];
		$step_id = $params["step_id"];
	
		$routes = new Model\Route();
		$routes->addDraftStepBefore($rid, $step_id);
	}
	
	/**
	 * Удалить этап
	 * 
	 * @param array
	 *    $params["step_id"] - ID этапа
	 */
	function stepremove($params) {
		$step_id = $params["step_id"];
		
		$routes = new Model\Route();
		$routes->delDraftStep($step_id);
	}
	
	/**
	 * Переименовать этап
	 * 
	 * @param array
	 *    $params["step_id"] - ID этапа
	 *    $params["name"]
	 */
	function steprename($params) {
		$step_id = $params["step_id"];
		$name = $params["name"];
		
		$routes = new Model\Route();
		$routes->renameDraftStep($step_id, $name);
	}
	
	/**
	 * Удалить черновик БП
	 * 
	 * @param array
	 *    $params["rid"] - ID БП
	 */
	function delRoute($params) {
		$rid = $params["rid"];
		
		$routes = new Model\Route();
		$routes->delDraftRoute($rid);
	}
	
	/**
	 * Получить значения типа данных селективных полей
	 * 
	 * @param array
	 *    $params["datatype"] - ID типа данных
	 * @return string (twig render) - .../datatype.tpl
	 */
	function getDatatype($params) {
		$datatype = $params["datatype"];
		
		$template = new Template();
		$data = $template->getDataVals($datatype);

		return $this->view->render("datatype", array("data" => $data));
	}
	
	/**
	 * Добавить рендер действия для завершения этапа
	 * 
	 * @param array
	 *    $params["ifdata"]
	 *    $params["ifdataval"]
	 *    $params["ifcon"]
	 *    $params["ifval"]
	 *    $params["goto"]
	 *    $params["gotoval"]
	 * @return string (twig render) - .../goto.tpl
	 */
	function addGoto($params) {
		$ifdata = $params["ifdata"];
		$ifdataval = $params["ifdataval"];
		$ifcon = $params["ifcon"];
		$ifval = $params["ifval"];
		$goto =  $params["goto"];
		$gotoval =  $params["gotoval"];
		
		if ( (is_numeric($ifdata)) and ($ifcon != "") and ($ifval != "") and (is_numeric($goto)) ) {
			return $this->view->render("goto", array("ifdata" => $ifdata, "ifdataval" => $ifdataval, "ifcon" => $ifcon, "ifval" => $ifval, "goto" => $goto, "gotoval" => $gotoval));
		}
	}
	
	/**
	 * Переместить черновик БП в реальные БП
	 * 
	 * @param array
	 *    $params["rid"] - ID БП
	 */
	function addRealRoute($params) {
		$rid = $params["rid"];
		
		$routes = new Model\Route();
		
		$routes->addRealRoutes($rid);
	}
	
	/**
	 * Удалить БП
	 * 
	 * @param array
	 *    $params["rid"] - ID БП
	 */
	function delRealRoute($params) {
		$rid = $params["rid"];
	
		$routes = new Model\RealRoute();
		$routes->delRoute($rid);
	}
	
	/**
	 * Запустить БП
	 * 
	 * @param array
	 *    $params["rid"] - ID БП
	 */
	function runProcess($params) {
		$rid = $params["rid"];
		
		$process = new Model\Process();
		$process->runProcess($rid);
	}
	
	/**
	 * Показать форму результата для завершения задачи БП (если результат требуется)
	 * @param array
	 *    $params["tid"] - ID задачи
	 * @return string (twig render) - .../formresults.tpl
	 * @return "null"
	 */
	function setResult($params) {
		$tid = $params["tid"];
	
		$process = new Model\Process();
		$form = $process->getTaskResultsForms($tid);
	
		if (count($form) > 0) {
			$template = new Template();
			$datatypes = $template->getDataTypes();
			
			for($i=0; $i<count($form); $i++) {
				for($k=0; $k<count($datatypes); $k++) {
					if ($form[$i]["datatype"] == $datatypes[$k]["id"]) {
						$form[$i]["select"] = $datatypes[$k]["vals"];
					}
				}
			}

			return $this->view->render("formresults", array("form" => $form));
		} else {
			return "null";
		}
	}
	
	/**
	 * Закрыть задачу БП.
	 * Записать результат. Перейти к следю действию
	 * 
	 * @param array
	 *    $params["tid"] - ID задачи
	 *    $params["result"] - JSON string (результаты)
	 */
	function closeTask($params) {
		$tid = $params["tid"];
		$result = json_decode($params["result"], true);
		
		$process = new Model\Process();
		$process->closeTask($tid, $result);
	}
}
?>