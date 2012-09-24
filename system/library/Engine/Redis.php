<?php
class Engine_Redis extends Engine_Interface {
	private $memdata = array();
	private $mid = null;
    private $timeLife = 2592000; // 1 месяц
	
	public function __construct() {
		parent::__construct();
		
		if ($this->registry["redis"]["enable"]) {
			$this->cache = new Redis();
			
			if (isset($this->registry["redis"]["socket"])) {
				$this->cache->connect($this->registry["redis"]["socket"]);
			} else if ( (isset($this->registry["redis"]["host"])) and (isset($this->registry["redis"]["port"])) ) {
				$this->cache->connect($this->registry["redis"]["host"], $this->registry["redis"]["port"]);
			} else if (isset($this->registry["redis"]["host"])) {
				$this->cache->connect($this->registry["redis"]["host"]);
			}
			
			$this->cache->setOption(Redis::OPT_PREFIX, 'ttw:');
		}
	}
	
	public function set($key) {
		$this->mid = $key;
	}
	
	public function get() {
		return $this->memdata;
	}

	public function load() {
		if ($this->registry["redis"]["enable"]) {

			if (!$this->cache->exists($this->mid)) {
				return false;
			} else {
				$this->memdata = json_decode($this->cache->get($this->mid), true);
				
				return true;
			}
		} else {
			return false;
		}
	}
	
	public function save($data) {
		if ($this->registry["redis"]["enable"]) {
			$this->cache->setex($this->mid, $this->timeLife, json_encode($data));
		} else {
			return false;
		}
	}
	
	public function saveTime($data, $time) {
		if ($this->registry["redis"]["enable"]) {
			$this->cache->setex($this->mid, $time, $data);
		} else {
			return false;
		}
	}
	
	public function delete() {
		if ($this->registry["redis"]["enable"]) {
			$this->cache->delete($this->mid);
		} else {
			return false;
		}		
	}

	public function __destruct() {
		if ($this->registry["redis"]["enable"]) {
			$this->cache->close();
		} else {
			return false;
		}
	}
}
?>