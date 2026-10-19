<?php

namespace HB9HCR\Mvc;

class View
{
    public object $container;
    protected object $variables;
    protected string $template;
    protected object $config;

    public static function init(Container $container, object|array $options = [], object|array $variables = []): static
    {
        $options = (object)array_merge(['template' => null], $options);

        $instance = new static();
        $instance->variables = $variables = new Parameters($variables);
        $instance->container = $container;
        $instance->template = sprintf('%s/%s', $container->application->basedir, $options->template);

        $variables->set('post', $container->request->post);
        $variables->set('get', $container->request->get);

        return $instance;
    }

    public function partial(string $template, object|array $variables = []): string
    {
        return (string)static::init($this->container, ['template' => 'partial/' . $template], $variables);
    }

    public function __get(string $name): mixed
    {
        return $this->variables->get($name);
    }

    public function __toString(): string
    {
        ob_start();
        include $this->template;
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }
}