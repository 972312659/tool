<?php

error_reporting(E_ALL &~E_NOTICE);
defined('BASE_PATH') || define('BASE_PATH', dirname(__DIR__));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');
try {
    $di = new \Phalcon\Di\FactoryDefault();
    include '../app/config/router.php';
    include '../app/config/services.php';
    $application = new \Phalcon\Mvc\Application($di);
    $application->useImplicitView(false);
    $application->handle()->send();
} catch (\Exception $e) {
    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}