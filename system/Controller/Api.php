<?php
class Controller_Api extends Engine_Controller {
	private $_login = null;
	private $_password = null;
	private $_uid = 0;
	private $_oid = 0;
	public $json;
	
	private function getOidFromUniqId($tid, $uniqId) {
		$objects = new Model_Object();
		return $objects->getOidFromUniqId($tid, $uniqId);
	}
	
	public function index() {
		$this->get = $_REQUEST;
		
		$json = new Helpers_JsonGet();
		if (isset($_GET['callback'])) {
			$json->call = $_GET['callback'];
		}

		$this->_login = $this->get["login"];
		$this->_password = $this->get["password"];
		$this->_uid = $this->registry["user"]->getUserId($this->_login);
		
		$params = array();
		if (isset($this->get["class"])) {
			if (isset($this->get["method"])) {
				$module = new ReflectionClass($this->get["class"]);
				$obj = $module->newInstance();
				$method = $this->get["method"];
				foreach($this->get as $key=>$args) {
					if ($key == "args") { 
						foreach($args as $part) {
							$params[] = $part;
						}
					};
				}
								$row = call_user_func_array(array($obj, $method), $params);				
				$json->JSONGet($row);
			}
		}
 	}
 }
 ?>