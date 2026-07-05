<?php

namespace HB9HCR;

use GlobIterator, SplFileInfo, ArrayObject, stdClass, traversable;

class Log
{
    public static function find(string $needle, string $haystack, array $options = []): ArrayObject
    {
        $options = (object)array_merge(['property' => 'call'], $options);
        $matches = new ArrayObject();
        $needle = strtoupper(trim($needle));

        foreach (static::glob($haystack) as $info) {
            foreach (static::parse($info)->qsos as $qso) {
                if ($qso->{$options->property} == $needle) {
                    $matches->append($qso);
                }
            }
        }

        return $matches;
    }

    public static function glob(string $pattern): GlobIterator
    {
        return new GlobIterator($pattern);
    }

    public static function latest(traversable $qsos): Qso|null
    {
        $latest = null;

        foreach ($qsos as $qso) {
            if (!$latest) $latest = $qso;
            if ($latest->datetime < $qso->datetime) $latest = $qso;
        }

        return $latest;
    }

    public static function parse(SplFileInfo|string $info): stdClass
    {
        $instance = new stdClass();
        $instance->info = $info instanceof SplFileInfo ? $info : new SplFileInfo($info);
        $instance->qsos = new ArrayObject();

        foreach (file($instance->info->getPathname()) as $line) {
            if (preg_match('#<EOR>#', $line)) {
                $qso = Qso::parse($line);
                $instance->qsos->append($qso);
            }
        }

        return $instance;
    }
}