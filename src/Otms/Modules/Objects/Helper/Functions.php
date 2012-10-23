<?php
namespace Otms\Modules\Objects\Helper;

use Engine\Modules;
use Otms\Modules\Objects\Model;

class Functions extends Modules\Functions {
	private $ai;
	
	function __construct($config) {
		parent::__construct($config);
	
		$this->ai = new Model\Ai();
	}
	
	function renderObject($param) {
		if (isset($this->registry["module_mail"])) {
			$mail = true;
		} else {
			$mail = false;
		}
		
		return $this->view->render("objectMain", array("ui" => $param[0],
		"mail" => $mail,
		"obj" => $param[1],
		"advInfo" => $param[2],
		"numAdvInfo" => $param[3],
		"forms" => $param[4],
		"numTroubles" => $param[5],
		"group" => $param[6]));
	}
	
	function addObject($param) {
		$object = new Model\Object();
		$object->addObject($param[0], $param[1]);
	}
	
	function editObject($param) {
		$object = new Model\Object();
		$object->editObject($param[0], $param[1]);
	}
	
	function getFidFromFname($param) {
		$template = new Model\Template();
		return $template->getFidFromFname($param[0], $param[1]);
	}
	
	function getOidFromUniqId($param) {
		$object = new Model\Object();
		return $object->getOidFromUniqId($param[0], $param[1]);
	}
	
	function getAdvanced($params) {
		return $this->ai->getAdvanced($params[0]);
	}
	
	function addAdvanced($params) {
		$this->ai->addAdvanced($params[0], $params[1], $params[2]);
	}
	
	function editAdvanced($params) {
		$this->ai->editAdvanced($params[0], $params[1], $params[2]);
	}
	
	function delAdvanced($params) {
		$this->ai->delAdvanced($params[0]);
	}
	
	
	
	function getAdvancedInfo($params) {
		return $this->ai->getAdvancedInfo($params[0]);
	}
	
	function getNumAdvancedInfo($params) {
		return $this->ai->getNumAdvancedInfo($params[0]);
	}
	
	function editObjectFormInfo($params) {
		$this->ai->editObjectFormInfo($params[0], $params[1]);
	}
	
	
	
	function getTags($params) {
		return $this->ai->getTags($params[0]);
	}
	
	function addTags($params) {
		$this->ai->addTags($params[0], $params[1]);
	}
	
	function changeTags($params) {
		$this->ai->changeTags($params[0], $params[1]);
	}
	
	
	
	
	function getForms() {
		return $this->ai->getForms();
	}
	
	function addForm($params) {
		$this->ai->addForm($params[0]);
	}
	
	function editForm($params) {
		$this->ai->editForm($params[0], $params[1]);
	}
	
	function getForm($params) {
		return $this->ai->getForm($params[0]);
	}
	
	function getFormName($params) {
		return $this->ai->getFormName($params[0]);
	}
	
	function addObjectFormInfo($params) {
		return $this->ai->addObjectFormInfo($params[0], $params[1], $params[2]);
	}
}
?>