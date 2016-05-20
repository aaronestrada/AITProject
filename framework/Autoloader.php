<?php
include_once dirname(__FILE__) . '/../config/db.php';

function frameworkAutoloader($classname) {
    $classFile = str_replace('\\', '/', $classname) . '.php';
    if (is_file(realpath(__DIR__ . '/../' . $classFile)))
        require_once('../' . $classFile);
}

spl_autoload_register('frameworkAutoloader');