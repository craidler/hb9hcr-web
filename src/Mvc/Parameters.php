<?php

namespace HB9HCR\Mvc;

use ArrayObject;

class Parameters extends ArrayObject
{
    public function __get(string $name): mixed
    {
        return $this->get($name);
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return $this->offsetExists($name) ? $this->offsetGet($name) : $default;
    }

    public function has(string $name): bool
    {
        return $this->offsetExists($name);
    }

    public function set(string $name, mixed $value): self
    {
        $this->offsetSet($name, $value);
        return $this;
    }
}