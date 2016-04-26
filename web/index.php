<?php
include_once('../framework/Autoloader.php');
include_once('../config/const.php');

$application = new \framework\BaseApplication('dev');
$application->run();
?>