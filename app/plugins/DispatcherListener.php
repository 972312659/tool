<?php

namespace App\Plugins;

use App\Exceptions\LogicException;
use App\Exceptions\ParamException;
use Exception;
use Phalcon\Events\Event;
use Phalcon\Http\Response;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Dispatcher\Exception as DispatchException;
use Phalcon\Mvc\User\Plugin;

class DispatcherListener extends Plugin
{
    const Anonymous = 'Anonymous';

    const Authorize = 'Authorize';

    private $defaultAnnotation;

    public function __construct(string $defaultAnnotation)
    {
        if (!\in_array($defaultAnnotation, [self::Authorize, self::Anonymous], true)) {
            throw new \Phalcon\Config\Exception('错误的默认声明');
        }
        $this->defaultAnnotation = $defaultAnnotation;
    }

    public function beforeException(Event $event, Dispatcher $dispatcher, Exception $exception)
    {
        if ($exception instanceof DispatchException) {
            switch ($exception->getCode()) {
                case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                    $dispatcher->forward([
                        'controller' => 'index',
                        'action'     => 'notFound',
                    ]);
                    return false;
            }
        }
        if ($exception instanceof LogicException || $exception instanceof ParamException) {
            $resp = new Response();
            $resp->setStatusCode($exception->getCode());
            $resp->setJsonContent($exception);
            $resp->send();
            return false;
        }
    }

    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
        if ($dispatcher->getControllerClass() == "App\Controllers\SystemController") {
            return true;
        }
        /**
         * @var $ref \Phalcon\Annotations\Reflection
         */
        $ref = $this->annotations->get($dispatcher->getControllerClass());
        $classAnnotations = $ref->getClassAnnotations();
        $methodAnnotations = $this->annotations->getMethod($dispatcher->getControllerClass(), $dispatcher->getActiveMethod());
        $needAuthorize = $this->defaultAnnotation === self::Authorize;
        if ($classAnnotations && $classAnnotations->has(self::Anonymous)) {
            $needAuthorize = false;
        }
        if ($classAnnotations && $classAnnotations->has(self::Authorize)) {
            $needAuthorize = true;
        }
        if ($methodAnnotations->has(self::Anonymous)) {
            $needAuthorize = false;
        }
        if ($methodAnnotations->has(self::Authorize)) {
            $needAuthorize = true;
        }
        if ($needAuthorize && $this->session->get('Id', false) === false) {
            throw new LogicException('请登录再操作', 401);
        }
        return true;
    }
}