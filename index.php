<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', realpath(dirname(__FILE__)));
define('F_PATH', ROOT_PATH . DS . 'lacframework');

require(F_PATH . DS ."lac.php");

$l = new LAC();
$l->run();

