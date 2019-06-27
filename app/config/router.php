<?php

$router = $di->getRouter();
$router->setDefaultController('index');
$router->setDefaultAction('index');
$router->removeExtraSlashes(true);
$router->setUriSource(Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);

$router->add('/', 'index::index');
$router->add('/:controller', [
    'controller' => 1,
]);
$router->add('/:controller/:action', [
    'controller' => 1,
    'action'     => 2,
]);
$router->add('/:controller/:action/:params', [
    'controller' => 1,
    'action'     => 2,
    'params'     => 3,
]);
$router->handle();
