<?php

return new \Phalcon\Config([
    'cloud'       => [
        'host'     => '119.23.160.150',
        'username' => 'peach',
        'password' => 'cV0IxSOLyA0d2fxT',
        'dbname'   => 'cloud',
        'charset'  => 'utf8mb4',
    ],
    'redis'       => [
        'host'  => '172.17.0.1',
        'port'  => 6379,
        'index' => 1,
    ],
    'application' => [
        'controllersDir' => APP_PATH . '/controllers',
        'modelsDir'      => APP_PATH . '/models',
        'pluginsDir'     => APP_PATH . '/plugins',
        'exceptionsDir'  => APP_PATH . '/exceptions',
        'libsDir'        => APP_PATH . '/libs',
        'baseUri'        => '/',
    ],
    'sphinx'      => [
        'host' => '172.17.0.1',
        'port' => 9306,
    ],
]);