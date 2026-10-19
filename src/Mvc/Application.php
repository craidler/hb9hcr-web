<?php

namespace HB9HCR\Mvc;

use GlobIterator;

class Application
{
    public object $container;
    public string $basedir;

    public static function init(string $directory, object|array $options = []): static
    {
        session_start();
        $instance = new static();
        $instance->basedir = realpath($directory);
        $instance->container = Container::init($instance);
        return $instance;
    }

    public function __toString(): string
    {
        $page = $this->container->router->route();
        ob_start();

        echo View::init(
            $this->container,
            ['template' => 'partial/layout.phtml'],
            ['page' => $page, 'content' => (string)View::init(
                $this->container,
                ['template' => 'view/' . $page->template . '.phtml'],
            )],
        );

        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }
}