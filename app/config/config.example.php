<?php

return new \Phalcon\Config([
    'cloud'       => [
        'host'     => 'localhost',
        'username' => 'root',
        'password' => '',
        'dbname'   => 'cloud',
        'charset'  => 'utf8-mb4',
    ],
    'redis'       => [
        'host' => '127.0.0.1',
        'port' => 6379,
    ],
    'application' => [
        'controllersDir' => APP_PATH . '/controllers',
        'modelsDir'      => APP_PATH . '/models',
        'pluginsDir'     => APP_PATH . '/plugins',
        'exceptionsDir'  => APP_PATH . '/exceptions',
        'baseUri'        => '/',
    ],
    'sphinx'      => [
        'host' => '127.0.0.1',
        'port' => 9306,
    ],
]);