<?php

/**
 * This file is part of the Workapp project Engine\Modules.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Engine\Modules;

/**
 * Model\Module class
 *
 * Класс наследуемый Model классами модулей
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Model extends \Engine\Model {
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
     * Конфиг модуля
     *
     * @var array
     */
    protected $config;
    
    /**
     * Экземпляр Twig
     * 
     * @var object
     */
	protected $twig;

	function __construct($config = array()) {
		parent::__construct();

		if (count($config) > 0) {
			$this->config = $config;

			if (isset($config["module_name"])) {
				$this->module_name = $config["module_name"];
			}
			if (isset($config["module_path"])) {
				$this->module_path = $config["module_path"];
			}

			$this->twig = $this->registry["twig_" . mb_strtolower($this->module_name)];
		}
	}
	
	/**
	 * Получение реального имени файла в ФС для шаблона
	 *
	 * @param string $template (пример: fm_content)
	 * @return string (пример: fm/content.tpl)
	 */
	function getTemplate($template) {
		$dirClass = explode("_", $template);
	
		if (sizeof($dirClass) > 1) {
			$template = implode(DIRECTORY_SEPARATOR, $dirClass) . '.tpl';
		} else
		{
			$template = $template . '.tpl';
		};
	
		return $template;
	}
	
	/**
	 * Twig render
	 *
	 * @param string $name (пример: fm_content)
	 * @param array $params - параметры
	 */
	protected function render($name, $params) {
		$param = array("registry" => $this->registry);
	
		$template = $this->twig->loadTemplate($this->getTemplate($name));
	
		if (isset($params)) {
			$content = $template->render($param + $params);
		} else {
			$content = $template->render($param);
		};
	
		return $content;
	}
}