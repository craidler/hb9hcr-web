<?php

namespace HB9HCR;

use GlobIterator;

return [
    'adab' => Svc\Adab::class,
    'adif' => Svc\Adif::class,
    'bota' => Svc\Bota::class,
    'router' => Mvc\Router::class,
    'database' => Svc\Database::class,
    'operations' => function (Mvc\Container $container) {
        return $container->bota->operations();
    },
    'session' => Svc\Session::class,
    'request' => function () {
        return new Mvc\Parameters([
            'server' => new Mvc\Parameters($_SERVER),
            'path' => parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
            'post' => new Mvc\Parameters($_POST),
            'get' => new Mvc\Parameters($_GET),
        ]);
    },
    'routes' => function () {
        return json_decode(json_encode(include __DIR__ . '/router.php'));
    },
    'logs' => function (Mvc\Container $container) {
        return new GlobIterator($container->application->basedir . '/logs/*.log');
    },
];