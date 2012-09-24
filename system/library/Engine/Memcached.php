<?php
class Engine_Memcached extends Engine_Interface {
	private $memdata = array();
	private $mid;
	private $cache;
    private $timeLife = 2592000; // 1 месяц
	
	public function __construct() {
		parent::__construct();
		
		if ($this->registry["memc"]["enable"]) {
			$this->cache = new Memcache();
			$this->cache->connect($this->registry["memc"]["adres"], $this->registry["memc"]["port"]);
		}
	}
	
	public function set($key) {
		$this->mid = $key;
	}
	
	public function get() {
		return $this->memdata;
	}
	
	public function getKey() {
		return $this->mid;
	}

	public function load() {
		if ($this->registry["memc"]["enable"]) {
			if ( ($this->memdata = $this->cache->get($this->mid)) === false ) {
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}
	
	public function save($data) {
		if ($this->registry["memc"]["enable"]) {
			$this->cache->set($this->mid, $data, false, $this->timeLife);
		} else {
			return false;
		}
	}
	
	public function saveTime($data, $time) {
		if ($this->registry["memc"]["enable"]) {
			$this->cache->set($this->mid, $data, false, $time);
		} else {
			return false;
		}
	}
	
	public function delete() {
		if ($this->registry["memc"]["enable"]) {
			$this->cache->delete($this->mid, 0);
		} else {
			return false;
		}		
	}

	public function __destruct() {
		if ($this->registry["memc"]["enable"]) {
			$this->cache->close();
		} else {
			return false;
		}
	}
}
?>