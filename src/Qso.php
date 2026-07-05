<?php

namespace HB9HCR;

use stdClass, DateTime, DateTimeZone;

class Qso extends stdClass
{
    public static function parse($data): Qso
    {
        $qso = new static();

        if (preg_match_all('/<([^:]+):\d+>([^<]*)/', $data, $ms)) {
            $zone = new DateTimeZone('utc');

            foreach ($ms[1] as $i => $k) {
                $qso->{strtolower($k)} = trim($ms[2][$i]);
            }

            if (property_exists($qso, 'freq')) {
                $qso->freq = sprintf('%.3f', $qso->freq);
            }
            
            if (property_exists($qso, 'qso_date') && property_exists($qso, 'time_on')) {
                $qso->datetime = DateTime::createFromFormat('Ymd\THis', $qso->qso_date . 'T' . $qso->time_on, $zone);
            }
        }

        return $qso;
    }
}