<?php

/**
 * This file is part of the Workapp project Engine.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Engine;

use Engine\Singleton;
use Otms\System;
use Symfony\Component\HttpFoundation\Response;

/**
 * View class
 *
 * Класс наследуемый другими View классами
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class View extends Singleton {

	/**
	 * <title></title>
	 * 
	 * @var string
	 */
	protected $title = NULL;
	
	/**
	 * <meta name="description" content="" />
	 * 
	 * @var string
	 */
	protected $description = array();
	
	/**
	 * <meta name="keywords" content="" />
	 *
	 * @var string
	 */
	protected $keywords = array();
	
	/**
	 * Содержимое страницы
	 * 
	 * @var string
	 */
	protected $mainContent = NULL;

	/**
	 * Главный шаблон страницы layouts.html
	 * $this->registry['layouts']
	 * 
	 * @var object
	 */
	protected $main;
	
	/**
	 * Остальные системные шаблоны
	 * $this->registry['templates']
	 * 
	 * @var object
	 */
	protected $twig;
	
	/**
	 * Контент правого блока
	 * 
	 * @var array
	 */
	public $rightBlock = NULL;

	/**
	 * Контент центральной части страницы
	 *
	 * @var string
	 */
	private $content = NULL;
	
	/**
	 * Панель вверху страницы.
	 * Пример: кнопки пользователи, события, чаты
	 * 
	 * @var array
	 */
	private $bottomPanel = array();
	
	/**
	 * Панель вверху страницы, кнопки справа.
	 * Пример: буфер обмена
	 * 
	 * @var array
	 */
	private $bottomPanelRight = array();
	
	/**
	 * Дополнительная панель внизу страницы.
	 * Примеры: файловый менеджер, фотоальбом.
	 * 
	 * @var string
	 */
	private $advBottomPanel = NULL;
	
	/**
	 * Массив CSS файлов модулей
	 * 
	 * @var array
	 */
	private $css = array();
	
	/**
	 * Массив JS файлов модулей
	 * 
	 * @var array
	 */
	private $js = array();

	function __construct() {
		parent::__construct();
		
        $this->main = $this->registry['layouts'];
        $this->twig = $this->registry['templates'];
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
	public function render($name, $params) {
		$param = array("registry" => $this->registry);
	
		$template = $this->twig->loadTemplate($this->getTemplate($name));
	
		if (isset($params)) {
			$content = $template->render($param + $params);
		} else {
			$content = $template->render($param);
		};
	
		return $content;
	}
	
	/**
	 * Читает "реальный" файл.
	 * Например: $this->registry["fm_content"] вызывает $this->setMainContent($this->twig->loadTemplate($this->getTemplate("fm_content")))
	 * 
	 * @param unknown_type $name (пример: fm_content)
	 * @param array $params - параметры
	 */
	public function __call($name, $params) {
		$param = array("registry" => $this->registry);
	
		$template = $this->twig->loadTemplate($this->getTemplate($name));
	
		if (isset($params[0])) {
			$content = $template->render($param + $params[0]);
		} else {
			$content = $template->render($param);
		};
	
		$this->setMainContent($content);
	}

	/**
	 * Добавить <title></title>
	 * 
	 * @param string $text
	 */
	public function setTitle($text) {
		$this->registry["title"] = $text;
		$this->title .= $text;
	}
	
	/**
	 * Добавить <meta name="description" content="" />
	 * 
	 * @param string $text
	 */
	public function setDescription($text) {
		$this->description[] = str_replace('"',"",$text);
	}
	
	/**
	 * Добавить <meta name="keywords" content="" />
	 * 
	 * @param string $text
	 */
	public function setKeywords($text) {
		$this->keywords[] = str_replace('"',"",$text);
	}
	
	/**
	 * Добавить content в правую панель
	 * 
	 * @param string $text
	 */
	public function setLeftContent($text) {
		$this->rightBlock .= $text;
	}
	
	/**
	 * Добавить CSS файл из модуля
	 * @param string $css
	 */
	public function addCSS($css) {
		$this->css[] = '<link href="' . $css . '" rel="stylesheet" type="text/css" />';
	}
	
	/**
	 * Добавить JS файл из модуля
	 * @param string $css
	 */
	public function addJS($js) {
		$this->js[] = '<script type="text/javascript" src="' . $js . '"></script>';
	}
	
	/**
	 * Добавить "специальный" content после верхнего меню
	 * Пример: кнопка добавить файл с компьютера
	 * 
	 * @param string $content
	 */
	public function setContent($content) {
		$this->content .= $content;
	}
	
	/**
	 * Добавить контент в главную часть страницы
	 * 
	 * @param string $text
	 */
	public function setMainContent($text) {
		$this->mainContent .= $text;
	}
	
	/**
	 * Добавить content на панель вверху страницы.
	 * Пример: кнопки пользователи, события, чаты
	 *
	 * @param string $content
	 * @param int $num - порядковый номер 
	 */
	public function setBottomPanel($content, $num) {
		if (is_numeric($num)) {
			if (array_key_exists($num, $this->bottomPanel)) {
				$temp = $this->bottomPanel[$num];
				$this->bottomPanel[$num] = $content;
				$this->bottomPanel[] = $temp;
			} else {
				$this->bottomPanel[$num] = $content;
			}
		} else if ($num == "right") {
			$this->bottomPanelRight[] = $content;
		} else {
			$this->bottomPanel[] = $content;
		}
	}
	
	/**
	 * Добавить content на дополнительную панель внизу страницы.
	 * Примеры: файловый менеджер, фотоальбом.
	 *
	 * @var string
	 */
	public function setAdvBottomPanel($content) {
		$this->advBottomPanel = $content;
	}
    
    /**
     * Показать всю страницу
     */
    public function showPage() {
    	$template = $this->main->loadTemplate("layouts.html");
    	$html = $template->render(array("registry" => $this->registry,
    			"description" => implode(",", $this->description),
    			"keywords" => implode(",", $this->keywords),
    			"title" => $this->title,
    			"css" => $this->css,
    			"js" => $this->js,
    			"rightBlock" => $this->rightBlock,
    			"main_content" => $this->mainContent,
    			"content" => $this->content,
    			"bottomPanel" => $this->bottomPanel,
    			"bottomPanelRight" => $this->bottomPanelRight,
    			"advBottomPanel" => $this->advBottomPanel));
    
    	$response = new Response($html);
    	$response->send();
    }
}