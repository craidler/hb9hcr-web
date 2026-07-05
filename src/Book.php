<?php

namespace HB9HCR;

use ArrayObject;

class Book
{
    public static $config;

    public static function add(string $name): void
    {
        $collection = static::load();
        $collection[] = (object)[
            'name' => trim($name),
            'time' => time(),
        ];

        static::write($collection);
    }

    public static function read(): ArrayObject
    {
        $collection = static::load();

        usort($collection, function ($a, $b) {
            if ($a->time < $b->time) return +1;
            if ($a->time > $b->time) return -1;
            return 0;
        });

        return new ArrayObject($collection);
    }

    protected static function load(): array
    {
        $filename = __DIR__ . '/' . static::$config->filename;
        if (!file_exists($filename)) return [];

        return json_decode(file_get_contents($filename));
    }

    protected static function write(array $data): void
    {
        $filename = __DIR__ . '/' . static::$config->filename;
        file_put_contents($filename, json_encode($data));
    }
}