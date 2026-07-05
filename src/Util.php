<?php

namespace HB9HCR;

use stdClass;

class Util
{
    public static function mergeArray(array $a, array $b): array
    {
        foreach ($b as $k => $v) {
            if (is_int($k)) {
                $a[] = $v;
            } elseif (isset($a[$k]) && is_array($a[$k]) && is_array($v)) {
                $a[$k] = static::mergeArray($a[$k], $v);
            } else {
                $a[$k] = $v;
            }
        }

        return $a;
    }

    public static function mergeJson(string $a, string $b = null): stdClass
    {
        $a = file_exists($a) ? json_decode(file_get_contents($a), true) : [];
        $b = file_exists($b) ? json_decode(file_get_contents($b), true) : [];
        $m = static::mergeArray($a, $b);
        return json_decode(json_encode($m));
    }
}