<?php

/**
 * This file is part of the Workapp project Engine\Modules.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Engine\Modules;

/**
 * View\Module class
 *
 * Класс наследуемый View классами модулей
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class View extends \Engine\View {

	/**
     * Конфиг модуля
     *
     * @var array
     */
    protected $config = NULL;
	
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
	 * Шаблоны
	 * $this->registry['templates']
	 * 
	 * @var object
	 */
	protected $twig;
	
	/**
	 * Главный экземпяр вида $this->registry["view"]
	 * 
	 * @var object
	 */
	protected $mainView;

	function __construct($twig) {
		parent::__construct();

        $this->twig = $twig;        
        $this->mainView = $this->registry["view"];
	}
	
	/**
	 * Присвоение $this->config = $config
	 * 
	 * @param array $config
	 */
	function setConfig($config) {
		$this->config = $config;
	}
	
	/**
	 * Getter
	 * 
	 * @return string $this->mainContent
	 */
	function get() {
		return $this->mainContent;
	}

	/**
	 * Render и передача результата в главну часть страницы
	 * 
	 * @param string $name (пример: fm_content)
	 * @param array $params - параметры
	 */
	function setMain($name, $params) {
		$this->setMainContent($this->mainView->render($name, $params));
	}

	/**
	 * Получение реального имени файла в ФС для шаблона
	 *
	 * @param string $template (пример: fm_content)
	 * @return string (пример: fm/content.tpl)
	 */
	public function getTemplate($template) {
		$dirClass = explode("_", $template);
	
		if (sizeof($dirClass) > 1) {
			$template = implode(DIRECTORY_SEPARATOR, $dirClass) . '.tpl';
		} else {
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
	 * Добавить CSS файл из модуля
	 * Передача в $this->mainView->addCSS($css)
	 * @param string $css
	 */
	public function addCSS($css) {
		$this->mainView->addCSS($css);
	}
	
	/**
	 * Добавить JS файл из модуля
	 * Передача в $this->mainView->addJS($js)
	 * @param string $css
	 */
	public function addJS($js) {
		$this->mainView->addJS($js);
	}

	/**
	 * Добавить "специальный" content после верхнего меню
	 * Пример: кнопка добавить файл с компьютера
	 * Передача в $this->mainView->setContent
	 *
	 * @param string $content
	 */
	public function setContent($content) {
		$this->mainView->setContent($content);
	}
	
	/**
	 * Добавить content на панель вверху страницы.
	 * Пример: кнопки пользователи, события, чаты
	 * Передача в $this->mainView->setBottomPanel
	 *
	 * @param string $content
	 * @param int $num - порядковый номер 
	 */
	public function setBottomPanel($content, $num = null) {
		$this->mainView->setBottomPanel($content, $num);
	}

	/**
	 * Добавить content на дополнительную панель внизу страницы.
	 * Примеры: файловый менеджер, фотоальбом.
	 * Передача в $this->mainView->setAdvBottomPanel
	 *
	 * @var string
	 */
	public function setAdvBottomPanel($content) {
		$this->mainView->setAdvBottomPanel($content);
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
	 * Добавить контент в главную часть страницы
	 *
	 * @param string $text
	 */
	public function setMainContent($text) {
		$this->mainContent .= $text;
	}

	/**
	 * Перезагрузка страница
	 * 
	 * @param array $array
	 *    пример: array("timer" => "1", "url" => "task/draft/")
	 */
    public function refresh($array) {
    	$content = $this->mainView->render("refresh", $array);
    	$this->setMainContent($content);
    }

    /**
     * Пейджер
     * 
     * @param array $array
     *    пример: array("pages" => $find->pager)
     */
    public function pager($array) {
    	$content = $this->mainView->render("pager", $array);
    	$this->setMainContent($content);
    }

    /**
     * Добавить content в правую панель
     * Передача в $this->mainView->setLeftContent
     *
     * @param string $content
     */
    public function setLeftContent($content) {
    	$content = $this->mainView->setLeftContent($content);
    }
    
    /**
     * Передача параметров title, description, keywords и контента в $this->mainView
     * 
     * (non-PHPdoc)
     * @see Engine.View::showPage()
     */
    public function showPage() {
    	$this->mainView->title = $this->title;
    	$this->mainView->description = $this->description;
    	$this->mainView->keywords = $this->keywords;
    	$this->mainView->setMainContent($this->get());
    }
}
?>
