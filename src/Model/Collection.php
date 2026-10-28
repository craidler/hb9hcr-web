<?php

namespace HB9HCR\Model;

use ArrayObject, PDOStatement;

class Collection extends ArrayObject
{
    public static function createWithStatement(PDOStatement $statement, object|array $options = []): static
    {
        $instance = new static();
        $options = new Parameters((array)$options);

        while ($item = $statement->fetchObject()) {
            $item = $options->get('callback') ? $options->get('callback')($item) : $item;
            $options->get('key') ? $instance->offsetSet($options->get('key')($item), $item) : $instance->append($item);
        }

        return $instance;
    }

    public function append(mixed $value): void
    {
        if ($value instanceof static) {
            foreach ($value as $v) $this->append($v);
            return;
        }

        parent::append($value);
    }

    public function find(callable $callback): object|null
    {
        foreach ($this as $item) {
            if ($callback($item)) return $item;
        }

        return null;
    }

    public function has(callable $callback): bool
    {
        foreach ($this as $item) {
            if ($callback($item)) return true;
        }

        return false;
    }
}