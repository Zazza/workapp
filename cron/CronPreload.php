<?php
class CronPreload {
	private $_config = null;
	protected $registry = null;

	private $DBH = null;
	
	function __construct() {
		$this->registry = Engine_Registry::getInstance();
	}

	function run($config) {
		$this->setConfig($config);
		
		$this->setDbAdapter();

		$this->setView();

		$this->setInit();

		$cron = new Cron($this->registry);
		$cron->index();
	}

	public function setConfig($config) {
		$this->_config = $config;

        $this->registry = Engine_Registry::getInstance();

        $this->registry->set('siteName', "http://" . $this->_config["url"]);
                
        $this->registry->set('controller', $this->_config["path"]["root"] . $this->_config['path']['controller']);
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
	 
	public function setDbAdapter() {
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
	 
	public function setView() {
		require_once 'Twig/Autoloader.php';

		$content = new Twig_Loader_Filesystem($this->registry["path"]["root"] . $this->registry['path']['layouts']);
		if ($this->registry["twig_cache"]) {
			$twig = array('cache' => $this->registry["cache"], 'autoescape' => FALSE);
		} else {
			$twig = array('cache' => FALSE, 'autoescape' => FALSE);
		}

		$layouts = new Twig_Environment($content, $twig);

		$loader = new Twig_Loader_Filesystem($this->_config["path"]["root"] . $this->_config['path']['templates']);
		$templates = new Twig_Environment($loader, $twig);

		$this->registry->set('layouts', $layouts);
		$this->registry->set('templates', $templates);

		$view = new View_Index();
        $this->registry->set('view', $view);
	}
	 
	public function setInit() {
		mb_internal_encoding("UTF-8");
		date_default_timezone_set('Europe/Moscow');

        $memcached = new Engine_Memcached();
        $this->registry->set('memcached', $memcached);
        
        $user = new Model_User();
        $this->registry->set('user', $user);
        
		$logs = new Model_Logs($this->registry);
		$this->registry->set('logs', $logs);
        
		$tt = new Model_Tt($this->registry);
		$this->registry->set('tt', $tt);

		session_start();
	}
}
?>