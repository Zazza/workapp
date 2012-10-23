<?php

/**
 * This file is part of the Workapp project Engine\Modules.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Engine\Modules;

use Engine\Singleton;
use \Twig_Autoloader;
use \Twig_Loader_Filesystem;
use \Twig_Environment;

/**
 * PreModule class
 *
 * Основной для модуля класс, вызывается первый раз при инициализации модуля. Тогда же создаёт необходимые сущности.
 * Затем вызывается из роутера.
 * И в конце postInit() в Bootstrap выполняет окончательные действия:
 *    загрузка JS, CSS файлов, передача нужных шаблонов в $this->registry["view"]
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class PreModule extends Singleton {
	/**
	 * Имя модуля строчными буквами
	 * 
	 * @var string
	 */
	protected $short_module_name;
	/**
	 * Имя модуля
	 * 
	 * @var string
	 */
    protected $module_name;
    
    /**
     * Путь к модулю
     *
     * @var string
     */
    protected $module_path;
    
    /**
     * Экземпляр Вида
     *
     * @var object
     */
	protected $view;
	
	/**
	 * Время хранения значения в кеше
	 * Здесь: сколько времени хранить настройки в кеше
	 *
	 * @var unknown_type
	 */
	private $timeLife = 2592000; // 1 месяц
	
	/**
	 * Конфиг модуля
	 *
	 * @var array
	 */
	protected $config;
	
	/**
	 * Полный список модулей
	 * 
	 * @var unknown_type
	 */
	protected $modules = array();
	
	/**
	 * Причина невозможности загрузки модуля(зависимости)
	 * 
	 * @var array
	 */
	private $reason = array();

	function __construct($module_name) {
		parent::__construct();
		
		$this->module_name = mb_substr($module_name, mb_strpos($module_name, 'Otms/Modules\\')+13, mb_strrpos($module_name, '\\')-mb_strpos($module_name, 'Otms/Modules\\')-13);

		$this->_setConfig($module_name);
		$this->_initTwig();
	}

	/**
	 * Вызов из Bootstrap после роутинга, до отображения страницы
	 */
	function postRouterInit() {
		$this->initView();

		$this->initModule();
	}

	/**
	 * Настройки
	 */
	private function _setConfig() {
		$this->short_module_name = mb_strtolower($this->module_name);
		$this->module_path = '../' . $this->registry['path']['src'] . '/Otms/Modules/' . $this->module_name . "/";
		
		$this->registry["memcached"]->set("module_" . $this->module_name);

		if ($this->registry["memcached"]->load()) {
			$config = $this->registry["memcached"]->get();
		} else {
			$module_config = $this->registry["rootPublic"] . $this->module_path . 'config.ini';

			$config = array_merge(parse_ini_file($module_config, true));

			$this->registry["memcached"]->save($config, false, $this->timeLife);
		}

		$config["module_name"] = $this->module_name;
		$config["module_path"] = $this->module_path;

		$this->config = $config;
	}

	/**
	 * Инициализация экземпляра Twig.
	 * Поиск шаблонов в /src/Otms/Modules/$this->module_name/$this->config["path"]["tpl"]/
	 */
	private function _initTwig() {
		if (isset($this->config["path"]["tpl"])) {
			$loader = new Twig_Loader_Filesystem($this->module_path . $this->config["path"]["tpl"]);
			if ($this->registry["twig_cache"]) {
				$twig = array('cache' => $this->registry["cache"], 'autoescape' => FALSE);
			} else {
				$twig = array('cache' => FALSE, 'autoescape' => FALSE);
			}
			$templates = new Twig_Environment($loader, $twig);

			$this->registry["twig_" . $this->short_module_name] = $templates;
		}
	}
	
	/**
	 * Инициализация экземпляра Вида
	 */
	protected function initView() {
		$this->view = new View($this->registry["twig_" . $this->short_module_name]);
		$this->view->setConfig($this->config);
	}

	/**
	 * Вызывается из postRouterInit
	 * Читает CSS и JS файлы, с помощью http://domen.com/resource/
	 * и передаёт результат в главный вид
	 */
	private function initModule() {
		if (isset($this->config["css"])) {
			if (is_array($this->config["css"])) {
				foreach($this->config["css"] as $css) {
					$this->view->addCss($this->registry["siteName"] . '/resource/?module=' . $this->module_name . '&file=' . $css);
				}
			} else {
				$this->view->addCss($this->registry["siteName"] . '/resource/?module=' . $this->module_name . '&file=' . $this->config["css"]);
			}
		}

		if (isset($this->config["js"])) {
			if (is_array($this->config["js"])) {
				foreach($this->config["js"] as $js) {
					$this->view->addJs($this->registry["siteName"] . '/resource/?module=' . $this->module_name . '&file=' . $js);
				}
			} else {
				$this->view->addJs($this->registry["siteName"] . '/resource/?module=' . $this->module_name . '&file=' . $this->config["js"]);
			}
		}
	}

	/**
	 * Getter
	 * 
	 * @return array
	 */
	public function getConfig() {
		return $this->config;
	}

	/**
	 * "Волшебный" метод для поиска публичного метода.
	 * 
	 * @param string $name
	 * @param array $args
	 * @return результат вызова публичного метода
	 */
	public function __call($name, $args) {
		return $this->publicMethods($name, $args);
	}

	/**
	 * Вызов "публичного" метода модуля
	 * Например: $this->registry["module_task"]->taskshort($id);
	 * 
	 * @param string $name
	 * @param array $args
	 * @return результат вызова публичного метода
	 */
	public function publicMethods($name, $args) {

		if (isset($this->config["publicMethods"])) {
			$class = $this->config["publicMethods"];
			$path = str_replace('_', '/', $this->config["publicMethods"]) . '.php';
		} else {
			$prefix =  str_replace("/", "\\", mb_substr($this->module_path, 7, mb_strlen($this->module_path) - 8));
			$class = $prefix . '\Helper\Functions';
			$path = 'Helper/Functions.php';
		}

		if (is_file($this->registry["rootPublic"] . $this->module_path . $path)) {
			$functions = new $class($this->config);
			return $functions->$name($args);
		}
	}

	/**
	 * Добавление причины, почему модуль не может быть загружен
	 * 
	 * @param string $reason
	 */
	public function setReason($reason) {
		$this->reason[] = $reason; 
	}
	
	/**
	 * Отобразить причину, почему модуль не может быть загружен
	 */
	public function showReason() {
		$this->registry['view']->setMainContent($this->registry['view']->render("errorModule", array("error" => $this->reason)));
	}
}