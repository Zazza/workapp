<?php
$file_config = __DIR__ . '/../app/config.ini';
$config = parse_ini_file($file_config, true);

$config["path"]["root"] = __DIR__;

require_once __DIR__ . '/../app/autoloader.php';

require_once __DIR__ . '/../vendor/Engine/Bootstrap.php';

$bootstrap = new Engine\Bootstrap();
$bootstrap->run($config);