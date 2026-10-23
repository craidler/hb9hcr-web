<?php

namespace HB9HCR;

use GlobIterator;

return [
    'adab' => Svc\Adab::class,
    'adif' => Svc\Adif::class,
    'bota' => Svc\Bota::class,
    'router' => Mvc\Router::class,
    'database' => Svc\Database::class,
    'campaign' => Model\Campaign::class,
    'session' => Svc\Session::class,
    'request' => function () {
        return new Model\Parameters([
            'server' => new Model\Parameters($_SERVER),
            'path' => parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
            'post' => new Model\Parameters($_POST),
            'get' => new Model\Parameters($_GET),
        ]);
    },
    'routes' => function () {
        return json_decode(json_encode(include __DIR__ . '/router.php'));
    },
    'logs' => function (Mvc\Container $container) {
        return new GlobIterator($container->application->basedir . '/logs/*.log');
    },
];