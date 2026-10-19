<?php
namespace HB9HCR;

require_once __DIR__ . '/../vendor/autoload.php';

use HB9HCR\Mvc;

$application = Mvc\Application::init(__DIR__ . '/../');
$container = $application->container;

if ($container->request->get->has('refresh')) {
    $container->bota->refresh($container->request->get->get('refresh'));
    header('Location: ' . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    exit;
}

if ($container->request->get->has('sitemap')) {
    $container->router->sitemap(__DIR__ . '/sitemap.xml');
    header('Location: ' . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    exit;
}

echo $application;