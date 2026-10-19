<?php

namespace HB9HCR\Svc;

use PDO, PDOStatement;

class Database 
{
    public object|null $config = null;
    protected object|null $adapter = null;

    public function __call(string $name, array $arguments): mixed
    {
        return call_user_func_array([$this->adapter(), $name], $arguments);
    }

    public function sql(string $filename): PDOStatement
    {
        return $this->adapter()->prepare(file_get_contents(__DIR__ . '/../../sql/' . $filename));
    }

    protected function adapter(): PDO
    {
        if (!$this->adapter) {
            $this->adapter = new PDO(
                $this->config->dsn, 
                $this->config->username,
                $this->config->password
            );
        }

        return $this->adapter;
    }
}