<?php
$start_time = microtime();

function __autoload($class_name) {
	$dirClass = explode("_", $class_name);

	if (sizeof($dirClass) > 1) {
		$class_name = implode(DIRECTORY_SEPARATOR, $dirClass) . '.php';
	} else {
		$class_name = $class_name . '.php';
	};

	require_once $class_name;
}

$timeLife = 2592000; // 1 месяц

$base_config = 'system/library/Engine/settings/config.ini';
$app_config = 'system/config.ini';
	
$config = array_merge(parse_ini_file($base_config, true), parse_ini_file($app_config, true));

$config["path"]["root"] = dirname(__FILE__);
$config["url"] = $_SERVER["HTTP_HOST"];
$config["ip"] = $_SERVER['REMOTE_ADDR'];
$config["uri"] = $_SERVER["REQUEST_URI"];

$paths = implode(PATH_SEPARATOR, array(
	$config["path"]["root"] . $config['path']['library'],
	$config["path"]["root"] . $config['path']['application'],
	$config["path"]["root"] . "/" . $config['path']['modules']
));

set_include_path($paths);

$bootstrap = new Engine_Bootstrap();
$bootstrap->run($config);

$g_time = floatval( microtime() - $start_time );
//echo $g_time;
?>
