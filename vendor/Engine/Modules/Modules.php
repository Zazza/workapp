<?php

/**
 * This file is part of the Workapp project Engine\Modules.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Engine\Modules;

use app\Preload;
use ReflectionClass;
use Engine\Modules\Module;

/**
 * Modules Class
 *
 * Вызывается из Bootstrap.
 * Ищет доступные модули. Читает их настройки. Необходимые позже параметры - сохраняет в реестре. 
 * Выполняет preInit().
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Modules extends Preload {
	/**
	 * Единственный метод класса. Выполняет всю работу.
	 * 
	 * @throws Exception
	 */
	public function load() {
		$module_name = array();
		
		$interface = new ReflectionClass("Engine\Modules\Module");
		
		$this->registry["memcached"]->set("modules");
		
		if (!$this->registry["memcached"]->load()) {
			$modules = array();

			if ($dh  = opendir($this->registry["rootPublic"] . "/../" . $this->registry['path']['src'] . '/Otms/Modules/')) {
				while (false !== ($filename = readdir($dh))) {
					if ( ($filename != ".") and ($filename != "..") ) {
						$modules[] = $filename;
					}
				}
			}
			
			$this->registry["memcached"]->save($modules);
		} else {
			$modules = $this->registry["memcached"]->get();
		}

		foreach($modules as $part) {
			require_once $this->registry["rootPublic"] . "../" . $this->registry['path']['src'] . '/Otms/Modules/' . $part . '/Index.php';
			$module = new ReflectionClass('Otms\Modules\\' . $part . '\\Index');
			
			if (!$module->isSubclassOf($interface)) {
				throw new Exception("Unknow module: " . $module);
			} else {
				$obj = $module->newInstance();
				$this->registry->set("module_" . mb_strtolower($part), $obj);
				$module_name[] = mb_strtolower($part);
				
				$obj->preInit();
			}
		}
		
		$this->registry["mods"] = $module_name;
	}
}