<?php

Phalcon\Mvc\Model::setup([
    'notNullValidations' => false,
    'castOnHydrate'      => true,
]);

$config = include APP_PATH . '/config/config.php';

$loader = new Phalcon\Loader();

$loader->registerNamespaces([
    'App\Controllers' => $config->application->controllersDir,
    'App\Models'      => $config->application->modelsDir,
    'App\Plugins'     => $config->application->pluginsDir,
    'App\Exceptions'  => $config->application->exceptionsDir,
])->register();

$di->setShared('config', $config);

$di->setShared('url', function () {
    $config = $this->getConfig();
    $url = new Phalcon\Mvc\Url();
    $url->setBaseUri($config->application->baseUri);
    return $url;
});

$di->setShared('dbcloud', function () {
    $config = $this->getConfig();
    $params = [
        'host'     => $config->cloud->host,
        'username' => $config->cloud->username,
        'password' => $config->cloud->password,
        'dbname'   => $config->cloud->dbname,
        'charset'  => $config->cloud->charset,
        'options'  => [
            PDO::ATTR_STRINGIFY_FETCHES  => false,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
    ];
    return new Phalcon\Db\Adapter\Pdo\Mysql($params);
});

$di->set('dispatcher', function () use ($di) {
    $em = $di->getShared('eventsManager');
    $em->attach('dispatch', new App\Plugins\DispatcherListener(App\Plugins\DispatcherListener::Authorize));
    $dispatcher = new Phalcon\Mvc\Dispatcher();
    $dispatcher->setDefaultNamespace('App\Controllers');
    $dispatcher->setEventsManager($em);
    return $dispatcher;
});

$di->setShared('redis', function () {
    $config = $this->getConfig();
    $redis = new Redis();
    $redis->connect($config->redis->host, $config->redis->port);
    return $redis;
});

$di->setShared('session', function () {
    $session = new Phalcon\Session\Adapter\Files();
    $session->start();
    return $session;
});