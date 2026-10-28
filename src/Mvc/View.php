<?php

namespace HB9HCR\Mvc;

use HB9HCR\Model\Parameters;

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

    public function number(float|int $value, object|array $options = []): string
    {
        $options = (object)array_merge(['decimal' => 0, 'point' => '.', 'thousand' => '\''], (array)$options);
        return number_format($value, $options->decimal, $options->point, $options->thousand);
    }

    public function partial(string $template, object|array $variables = []): string
    {
        return (string)static::init($this->container, ['template' => 'partial/' . $template], $variables);
    }

    public function plural(float|int $value, string $word): string
    {
        return sprintf(
            '%s %s%s',
            number_format($value, 0, '.', '\''),
            $word,
            1 < $value ? 's' : ''
        );
    }

    public function truncate(string $value, object|array $options = []): string
    {
        $truncated = '';
        $options = (object)array_merge(['length' => 30, 'token' => ' '], (array)$options);
        $token = strtok($value, $options->token);

        while (strlen($truncated) < $options->length && $token) {
            $truncated .= $options->token . $token;
            $token = strtok($options->token);
        }

        return trim($truncated, ' ,');
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