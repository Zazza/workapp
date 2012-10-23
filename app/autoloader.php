<?php
require_once __DIR__ . '/../vendor/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
                'Engine'        => __DIR__ . '/../' . $config['path']['library'],
                'Symfony'               => __DIR__ . '/../' . $config['path']['library'] . '/symfony/src/',
                'System'        => __DIR__ . '/../' . $config['path']['library'] . $config['path']['application'],
                'Otms'                  => __DIR__ . '/../' . $config['path']['src'],
                'Phpmailer'            => __DIR__ . '/../' . $config['path']['library'] . '/mailer',
));
$loader->registerPrefixes(array(
                'Twig_Extensions_' => __DIR__ . '/../' . $config['path']['library'] . '/twig-extensions/lib',
                'Twig_'            => __DIR__ . '/../' . $config['path']['library'] . '/twig/lib',
));

$loader->register();
