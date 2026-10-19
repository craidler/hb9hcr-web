<?php

namespace HB9HCR\Mvc;

use DateTime;

class Router
{
    public object $container;

    public function redirect(string $location = null): void
    {
        header('Location: ' . ($location ? $location : $this->container->request->path));
        exit;
    }

    public function route(): object
    {
        $routes = $this->container->routes; 
        $path = $this->container->request->path;

        usort($routes, function ($a, $b) {
            return strlen($a->route) - strlen($b->route);
        });

        foreach ($routes as $route) {
            if ($route->route != $path) continue;
            return $route;
        }
    }

    public function sitemap(string $filename): void
    {
        $date = new DateTime('now', null);
        $urls = [];

        foreach (array_keys($this->container->routes) as $route) {
            $urls[] = (object)[
                'loc' => 'https://hb9hcr.ch' . $route,
                'lastmod' => $date,
            ];
        }

        file_put_contents($filename, '<?xml version="1.0" encoding="UTF-8"?>' . View::init($this->container, ['template' => 'partial/sitemap.phtml'], ['urls' => $urls]));
    }
}