<?php 

namespace HB9HCR\Mvc;

use ArrayObject, ReflectionClass;
use HB9HCR\Model\Parameters;

class Container extends ArrayObject
{
    protected object $data;

    public static function init(Application $application): static
    {
        $instance = new static();
        $instance->offsetSet('application', $application);
        $instance->offsetSet('config', (object)[
            'container' => (object)include $application->basedir . '/config/container.php',
            'router' => (object)include $application->basedir . '/config/router.php',
        ]);

        return $instance;
    }

    public function __construct()
    {
        $this->data = new Parameters();
    }

    public function __get(string $name): mixed
    {
        return $this->get($name);
    }

    public function data(string $name): object
    {
        if (!$this->data->has($name)) {
            $this->data->set($name, (object)include $this->application->basedir . '/config/' . $name . '.php');
        }

        return $this->data->get($name);
    }

    public function get(string $name): mixed
    {
        if (!$this->offsetExists($name)) {
            $factory = $this->config->container->{$name};

            if (is_string($factory)) $instance = new $factory;
            if (is_callable($factory)) $instance = call_user_func_array($this->config->container->{$name}, [$this]);
            $this->offsetSet($name, $instance);

            if (is_object($instance)) {
                $reflection = new ReflectionClass($instance);
                $config = $this->application->basedir . '/config/' . strtolower($reflection->getShortName()) . '.php';
                if ($reflection->hasProperty('config') && is_file($config)) $instance->config = (object)include $config;
                if ($reflection->hasProperty('container')) $instance->container = $this;
                if ($reflection->hasProperty('database')) $instance->database = $this->get('database');
                if ($reflection->hasMethod('init')) $instance->init();
            }
        }

        return $this->offsetGet($name);
    }
}