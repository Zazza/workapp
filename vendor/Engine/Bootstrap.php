<?php

/**
 * This file is part of the Workapp project Engine.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Engine;

require_once __DIR__ . '/../../app/Preload.php';

use Symfony\Component\HttpFoundation\Request;
use \Twig_Autoloader;
use \Twig_Loader_Filesystem;
use \Twig_Environment;
use PDO;
use app\Preload;
use Engine\Registry;
use Engine\Modules\Modules;
use Engine\ErrorCatcher;

/**
 * Bootstrap class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */

class Bootstrap {
        /**
         * Конфиг
         *
         * @var array
         */
    private $_config = array();

    /**
     * Реестр (singleton)
     *
     * @var array
     */
    protected $registry = array();

    /**
     * Экземпляр Вида
     *
     * @var object
     */
    protected $view = null;

    /**
     * PDO
     *
     * @var object
     */
    private $DBH = NULL;

    function __construct() {
        $this->registry = Registry::getInstance();
    }

    /**
     * Поочерёдное выполнение методов класса Bootstrap
     *
     * @param array $config
     */
    public function run($config) {
        $this->_setConfig($config);

        $this->_setView();
        
        $error = new ErrorCatcher();

        $this->_setDbAdapter();

        $this->_setInit();

        // /app/Preload() - пользовательский файл с предустановками приложения
        $preload = new Preload();
        if ($preload->start()) {
                // Инициализация модулей
                $modules = new Modules();
                $modules->load();

                // Запуск роутера
                $this->_setRouter();
        }

        $this->_end();
    }

    /**
     * Заполнение реестра
     *
     * @param array $config
     */
     private function _setConfig($config) {
        $this->_config = $config;

        $request = Request::createFromGlobals();

        $this->_config["url"] = $request->server->get("HTTP_HOST");
        $this->_config["ip"] = $request->server->get("REMOTE_ADDR");
        $this->_config["uri"] = $request->server->get("REQUEST_URI");
        $this->_config["system"] = '/../' . $this->_config['path']['src'] . '/Otms/System/';

        $this->registry->set('siteName', "http://" . $this->_config["url"]);

        $this->registry->set('controller', $this->_config["path"]["root"] . $this->_config["system"] . $this->_config['path']['controller']);
        $this->registry->set('cache', $this->_config["path"]["root"] . $this->_config['path']['cache']);
        $this->registry->set('rootPublic', $this->_config["path"]["root"] . "/");
        $this->registry->set('rootDir', substr($this->_config["path"]["root"], 0, strpos($this->_config["path"]["root"], "public")));

                $action = (empty($_GET['main'])) ? '' : $_GET['main'];
        if (empty($action)) { $action = ''; };

        $this->_config["url"] = "/" . $action;


        if (!empty($action)) {
            $this->_config["uri"] = substr($this->_config["uri"], 0, strrpos($this->_config["uri"], $action));
        }

        $this->_config["uripath"] = substr($this->registry["uri"], 0, strlen($this->registry["uri"])-1) . $this->registry["url"];
        $this->registry->set('url_convert', str_replace('/', '_', $action));

        foreach($this->_config as $key=>$val) {
                $this->registry->set($key, $val);
        }
     }

     /**
      * Инициализация экземпляра Вида ($this->view)
      * Twig
      *    layouts - главный шаблон страницы layouts.html
      *    templates - остальные системные шаблоны
      */
     private function _setView() {
        Twig_Autoloader::register();

        $content = new Twig_Loader_Filesystem($this->registry["path"]["root"] . $this->_config["system"] . $this->registry['path']['layouts']);
        if ($this->registry["twig_cache"]) {
                $twig = array('cache' => $this->registry["cache"], 'autoescape' => FALSE);
        } else {
                $twig = array('cache' => FALSE, 'autoescape' => FALSE);
        }

        $layouts = new Twig_Environment($content, $twig);

        $loader = new Twig_Loader_Filesystem($this->registry["path"]["root"] . $this->_config["system"] . $this->registry['path']['templates']);
        $templates = new Twig_Environment($loader, $twig);

        $this->registry->set('layouts', $layouts);
        $this->registry->set('templates', $templates);
     }

     /**
      * Инициализация экхемпляра PDO для доступа к БД.
      * $this->registry["db"]
      */
     private function _setDbAdapter() {
        try {
                $this->DBH = new PDO($this->_config['db']['adapter'] . ':host=' . $this->_config['db']['host'] . ';dbname=' . $this->_config['db']['dbname'], $this->_config['db']['username'], $this->_config['db']['password']);
        } catch(PDOException $e) {
                echo $e->getMessage();
        }

        $this->registry->remove('db');
        $this->registry->set('db', $this->DBH);

        $this->DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );

        $this->DBH->query('SET NAMES UTF8');
     }

     /**
      * Дополнительные настройки
      */
     private function _setInit() {
		mb_internal_encoding("UTF-8");

		$memcached = new Memcached();
		$this->registry->set('memcached', $memcached);
     }

     /**
      * Выполнение роутера
      */
     private function _setRouter() {
        $router = new Router();
        if ($router->showContent()) {
                $this->_post();
        }
     }

     /**
      * Подключение CSS и JS файлов модулей
      * Выполнение задач модулями
      *
      * Отображение страницы $this->view->showPage()
      */
     private function _post() {
        $this->view = $this->registry["view"];

        $mods = $this->registry["mods"];
        foreach($mods as $module) {
                $this->registry["module_" . $module]->postRouterInit();
                $this->registry["module_" . $module]->postInit();
        }

        $this->view->showPage();
     }

     /**
      * Удаление экземпялра PDO
      */
     private function _end() {
        $this->DBH = NULL;
     }
 }
