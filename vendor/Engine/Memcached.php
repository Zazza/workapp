<?php

/**
 * This file is part of the Workapp project Engine.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Engine;

use Memcache;

/**
 * Memcached class
 *
 * Класс для работы с memcached
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Memcached extends Singleton {

	/**
	 * Результат извлечённый из кеша
	 * 
	 * @var unknown_type
	 */
	private $memdata = array();
	
	/**
	 * key
	 * 
	 * @var unknown_type
	 */
	private $mid;
	
	/**
	 * Экземпляр new Memcache()
	 * 
	 * @var unknown_type
	 */
	private $cache;
	
	/**
	 * Время хранения значения в кеше
	 * 
	 * @var unknown_type
	 */
    private $timeLife = 2592000; // 1 месяц
	
	public function __construct() {
		parent::__construct();
		
		if ($this->registry["memc"]["enable"]) {
			$this->cache = new Memcache();
			$this->cache->connect($this->registry["memc"]["adres"], $this->registry["memc"]["port"]);
		}
	}
	
	/**
	 * Setter
	 * 
	 * @param string $key
	 */
	public function set($key) {
		$this->mid = $key;
	}
	
	/**
	 * Getter
	 * Получить значение из кеша
	 * 
	 * @return $this->memdata
	 */
	public function get() {
		return $this->memdata;
	}
	
	/**
	 * Getter
	 * Получить key
	 * 
	 * @param string $this->mid
	 */
	public function getKey() {
		return $this->mid;
	}

	/**
	 * Извлечь значение из кеша в переменную $this->memdata
	 * @return boolean
	 */
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
	
	/**
	 * Сохранить значение в кеше
	 * 
	 * @param unknown_type $data
	 * @return FALSE - в случае ошибки
	 */
	public function save($data) {
		if ($this->registry["memc"]["enable"]) {
			$this->cache->set($this->mid, $data, false, $this->timeLife);
		} else {
			return false;
		}
	}
	
	/**
	 * Сохранить значение в кеше на заданное время
	 * 
	 * @param unknown_type $data
	 * @param int $time
	 * @return FALSE - в случае ошибки
	 */
	public function saveTime($data, $time) {
		if ($this->registry["memc"]["enable"]) {
			$this->cache->set($this->mid, $data, false, $time);
		} else {
			return false;
		}
	}
	
	/**
	 * Удалить значение из кеша
	 * 
	 * @return FALSE - в случае ошибки
	 */
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
		}
	}
}
?>