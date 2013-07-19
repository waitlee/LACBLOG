<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', realpath(dirname(__FILE__)));
define('F_PATH', ROOT_PATH . DS . 'lacframework');
define("APP_PATH", ROOT_PATH . DS . 'demo');

require_once(F_PATH . DS ."lac.php");

$config = F_PATH . DS . 'config.php';

$app = \LAC\LAC::createWebApplication($config);
$app->run();

