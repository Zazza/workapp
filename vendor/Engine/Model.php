<?php

/**
 * This file is part of the Workapp project Engine.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Engine;

/**
 * Model class
 *
 * Класс наследуемый другими Model классами
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Model extends Singleton {

	/**
	 * Экземпляр memcached
	 * 
	 * @var object
	 */
    protected $memcached;

	/**
	 * Переменные для пейджера
	 * 
	 * Полное количество страниц
	 * @var int
	 */
	public $totalPage;
	
	/**
	 * Переменные для пейджера
	 *
	 * Кол-во выводимых статей
	 * @var int
	 */
	public $limit = 20;
	
	
	/**
	 * Переменные для пейджера
	 *
	 * Номер записей на текущей странице
	 * Пример: 80-100, $startRow = 80
	 * @var int
	 */
	public $startRow = 0;
	
	
	/**
	 * Переменные для пейджера
	 *
	 * Текущая страница
	 * @var int
	 */
	public $curPage = 1;
	
	
	/**
	 * Переменные для пейджера
	 *
	 * Кол-во выводимых страниц в педжере
	 * @var int
	 */
    private $num = 5;
    
    
    /**
     * Переменные для пейджера
     *
     * Постоянная ссылка
     * @var string
     */
    public $links = "";
    
    
    /**
     * Переменные для пейджера
     *
     * Массив кнопок пейджера
     * @var array
     */
	public $pager = array();
	
	
	/**
	 * Переменные для пейджера
	 *
	 * Последняя страница пейджера
	 * @var int
	 */
    public $maxPage = 1;
    
    
    /**
     * Переменные для пейджера
     *
     * Разделитель
     * @var char
     */
    public $sep = "&";

	/**
	 * Антифлуд
	 * 
	 * @var boolean
	 */
	public $pause = FALSE;
	
	/**
	 * Время задержки в секундах для механизма антифлуда
	 * 
	 * @var int
	 */
	public $pause_time = 10;

	function __construct() {
		parent::__construct();
		    
        $this->memcached = $this->registry['memcached'];
	}

	/**
	 * Установка выбранной страницы
	 * 
	 * @param int $page
	 * @return boolean
	 */
	public function setPage($page) {
		if ( ($page > 0) and ($page < 1000000000) ) {
			$this->curPage = $page;
			$this->startRow = $this->limit * ($page-1);
            
			return TRUE;
		} else {
			return FALSE;
		};
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
		} else {
			$template = $template . '.tpl';
		};
	
		return $template;
	}
	
	/**
	 * Twig render
	 * 
	 * @param unknown_type $name (пример: fm_content)
	 * @param array $params - параметры
	 */
	protected function render($name, $params) {
		$param = array("registry" => $this->registry);
	
		$template = $this->registry['templates']->loadTemplate($this->getTemplate($name));
	
		if (isset($params)) {
			$content = $template->render($param + $params);
		} else {
			$content = $template->render($param);
		};
	
		return $content;
	}
	
	/**
	 * Пейджер
	 */
	public function Pager() {
		$numPage = ceil( $this->totalPage / $this->limit );
        $this->maxPage = $numPage;

		//Предыдущая страница
		$prev = ($this->curPage-1);
		if ($prev != 0) {
            $this->pager[0] = "<li><a onclick='pager()' href='" . $this->registry["uri"] . $this->registry["action"] . $this->links . $this->sep . "page=" . $prev ."'>←</a></li>";
		};

		for ($i=1; $i <= $numPage; $i++) {
			if ($i == $this->curPage) {
				$this->pager[] = "<li class='active'><a>" . $i . "</a></li>";
			} else {
				// Выводим не более $this->num страниц вокруг выбранной
				if ( ($i >= $this->curPage - $this->num ) and ($i <= $this->curPage + $this->num) ) {
                    $this->pager[] = "<li><a onclick='pager()' href='" . $this->registry["uri"] . $this->registry["action"] . $this->links . $this->sep . "page=" . $i ."'>$i</a></li>";
				};
			};
		};

		//Следующая страница
		$next = ($this->curPage+1);
		if ($next <= $numPage) {
            $this->pager[$i+1] = "<li><a onclick='pager()' href='" . $this->registry["uri"] . $this->registry["action"] . $this->links . $this->sep . "page=" . $next ."'>→</a></li>";
		};

		$this->pager;
	}

	/**
	 * Форматирует дату в соответствии с локалью
	 * 
	 * @param string(date) $date
	 * @return string(date)
	 */
	public function editDate($date) {
		setlocale(LC_ALL, 'ru_RU.utf-8', 'rus_RUS.utf-8', 'ru_RU.utf8');

		if (substr($date, 11, 8) != "") {
			$date = strtotime($date);
			
			$mk = mktime(date("H", $date), date("i", $date), date("s", $date), date("m", $date), date("d", $date), date("Y", $date));
			$res = strftime("%H:%M:%S, %A, %d %B %Y", $mk);
		} else {
			$date = strtotime($date);
			
			$mk = mktime(0, 0, 0, date("m", $date), date("d", $date), date("Y", $date));
			$res = strftime("%A, %d %B %Y", $mk);
		}
		return $res;
	}
    
	/**
	 * Инициализирует механизм антифлуда
	 * 
	 * @param string $type - для определения для чего используется антифлуд
	 *    пример: initPause("task")
	 */
	public function initPause($type) {
		$this->memcached->mid = $type . $this->registry["ui"]["id"];
		if ($this->memcached->load()) {
			$this->pause = TRUE;
		} else {
			$this->pause = FALSE;
			$this->memcached->saveTime("pause", $this->pause_time);
		}
	}
}
?>
