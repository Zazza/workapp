<?php
class Engine_Redis extends Engine_Interface {
	private $memdata = array();
	private $cache;
	private $mid = null;
	private $rediska;
    private $timeLife = 2592000; // 1 месяц
	
	public function __construct() {
		parent::__construct();
		
		if ($this->registry["redis"]["enable"]) {
			$options = array(
				'servers'   => array(
					array('host' => $this->registry["redis"]["host"], 'port' => $this->registry["redis"]["port"])
				)
			);
			
			$this->rediska = new Rediska($options);
		}
	}
	
	public function set($key) {
		$this->mid = $key;
		$this->cache = new Rediska_Key($key);
	}
	
	public function get() {
		return $this->memdata;
	}

	public function load() {
		if ($this->registry["redis"]["enable"]) {
			$data = $this->rediska->pipeline()
			->exists($this->mid)
			->execute();
			
			if (!$data[0]) {
				return false;
			} else {
				$this->memdata = $this->cache->getValue();
				
				return true;
			}
		} else {
			return false;
		}
	}
	
	public function save($data) {
		if ($this->registry["redis"]["enable"]) {
			$this->cache->setValue($data);
			
			$this->cache->expire($this->timeLife);
		} else {
			return false;
		}
	}
	
	public function saveTime($data, $time) {
		if ($this->registry["redis"]["enable"]) {
			$this->cache->setValue($data);
				
			$this->cache->expire($time);
		} else {
			return false;
		}
	}
	
	public function delete() {
		if ($this->registry["redis"]["enable"]) {
			$this->cache->delete();
		} else {
			return false;
		}		
	}

	public function __destruct() {
		if ($this->registry["redis"]["enable"]) {
			$data = $this->rediska->pipeline()
			->quit()
			->execute();
		} else {
			return false;
		}
	}
}
?>