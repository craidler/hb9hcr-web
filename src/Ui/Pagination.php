<?php

namespace HB9HCR\UI;

use ArrayAccess, ArrayObject;

class Pagination extends ArrayObject
{
    public $collection;
    public $options;

    public static function init(array|ArrayAccess $collection, object|array $options = []): static
    {
        $instance = new static();
        $instance->collection = $collection;
        $instance->options = (object)array_merge(['pagesize' => 10], (array)$options);

        for ($i = 0; $i < count($collection); $i++) {
            $j = floor($i / $instance->options->pagesize);
            $page = $instance->offsetExists($j) ? $instance->offsetGet($j) : [];
            $page[] = $collection[$i];
            $instance->offsetSet($j, $page);
        }

        return $instance;
    }
}