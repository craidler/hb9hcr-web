<?php

namespace HB9HCR\Svc;

use HB9HCR\Model\Parameters;

class Session
{
    public function clear(string $name): self
    {
        unset($_SESSION[$name]);
        return $this;
    }

    public function get(string $name): mixed
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }

    public function set(string $name, mixed $value): self
    {
        $_SESSION[$name] = $value;
        return $this;
    }
}