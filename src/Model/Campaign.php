<?php

namespace HB9HCR\Model;

use Exception;

class Campaign
{
    public object $container;
    public object $database;
    public Collection|null $references = null;
    public Collection|null $operations = null;
    public Collection|null $planned = null;

    protected array $types = [
        '#geschützstand#i' => 'Emplacement Art',
        '#unterstand#i' => 'Shelter',
        '#telefon#i' => 'Signal',
        '#infanteriebunker#i' => 'Bunker',
        '#infanteriekanonenschild#i' => 'Emplacement Inf',
        '#ik-stand#i' => 'Emplacement Inf',
        '#schild#i' => 'Shield',
        '#mg-werk#i' => 'Bunker MG',
        '#mg-stand#i' => 'Emplacement MG',
        '#kp anlage#i' => 'Command',
        '#beobachter#i' => 'Observation',
        '#mun-magazin#i' => 'Ammunition',
        '#schützenstellung#i' => 'Entrenchment',
    ];

    public function init(): void
    {
        $select = $this->database->sql('campaign/reference.collection.sql');
        $select->execute(['adab' => '^[AF]', 'bota' => '.*']);

        $this->planned = new Collection();
        $this->operations = new Collection();
        $this->references = Collection::createWithStatement($select, ['callback' => function ($item) {
            $item->qso_hunters = json_decode($item->qso_hunters);
            return $item;
        }]);

        $ops = json_decode(json_encode((include $this->container->application->basedir . '/config/bota.php')['operations']));

        usort($ops, function ($a, $b) {
            return strcmp($a->name, $b->name);
        });

        foreach ($ops as $o) {
            $o->id = strtoupper(trim(base64_encode($o->name), '='));

            foreach ($o->location as $i => $l) {
                $reference = [];

                foreach ($l[2] as $r) {
                    $reference[] = $r = $this->references->find(function ($item) use($r) {
                        return $r == $item->bota;
                    });

                    $this->planned->append($r);
                }

                $o->location[$i] = (object)[
                    'name' => $l[0],
                    'lat' => explode('/', $l[1])[0],
                    'lng' => explode('/', $l[1])[1],
                    'reference' => $reference,
                    'also' => $l[3] ?? null,
                ];
            }

            $this->operations->append($o);
        }
    }

    public function colorize(object $reference): object
    {
        $reference->planned = (bool)$this->planned->find(function ($item) use ($reference) {
            return $item->adab == $item->adab && $item->bota == $reference->bota;
        });

        $c = null;

        if ($reference->adab) {
            $c = match(true) {
                'F' == $reference->adab[0] => '#feee00',
                'T' == $reference->adab[0] => 'green',
                default => '#ff6052',
            };
        }

        if ($reference->bota) {
            $c = 'A' == $reference->name[0] ? '#b14137ff' : $c;
            $c = 'F' == $reference->name[0] ? '#fedc00' : $c;
            $c = $reference->planned ? '#1a5c95ff' : $c;
            $c = 0 < $reference->qso_qty ? '#2a81cbff' : $c;
            $c = 25 <= $reference->qso_cw ? '#35bf6aff' : $c;
        }

        $reference->color = $c;

        return $reference;
    }

    public function complete(object $object): bool
    {
        if (isset($object->location)) {
            foreach ($object->location as $l) {
                if (!$this->complete($l)) return false;
            }

            return true;
        }

        if (isset($object->reference)) {
            foreach ($object->reference as $r) {
                if (!$this->complete($r)) return false;
            }

            return true;
        }

        if (25 > $object->qso_cw ?? 0) {
            return false;
        }

        return true;
    }

    public function distance(object|array $a, object|array $b): float
    {
        $earthRadius = 6371; // Radius of the earth in km
        list($lat1, $lon1) = (array)$a;
        list($lat2, $lon2) = (array)$b;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance;
    }

    public function hunters(?object $object = null): array
    {
        $c = [];

        if (!$object) {
            foreach ($this->operations as $i) $c = array_merge($c, $this->hunters($i));
        }

        if ($object && isset($object->location)) {
            foreach ($object->location as $i) $c = array_merge($c, $this->hunters($i));
        }

        if ($object && isset($object->reference)) {
            foreach ($object->reference as $i) {
                if (isset($i->qso_hunters)) {
                    foreach ($i->qso_hunters as $h) if ($h) $c[] = $h;
                }
            }
        }

        $c = array_unique($c);
        sort($c);

        return $c;
    }

    public function last(object $object): string|null
    {
        $last = null;

        foreach ($object->reference as $r) {
            $last = $r->qso_last > $last ? substr($r->qso_last, 0, 10) : $last;
        }

        return $last;
    }

    public function locations(?object $object = null, bool $unique = false): Collection
    {
        $c = new Collection();

        if (!$object) {
            foreach ($this->operations as $o) $c->append($this->locations($o, $unique));
            return $c;
        }

        if (isset($object->location)) {
            foreach ($object->location as $l) {
                if ($unique) {
                    $c->offsetSet(sprintf('%.f6/%.f6', $l->lat, $l->lng), $l);
                    continue;
                }

                $c->append($l);
            }
        }

        return $c;
    }

    public function operation(string $route): object
    {
        foreach ($this->operations as $o) {
            if ($route == $o->route) return $o;
        }

        throw new Exception();
    }

    public function qsos(object $object): int
    {
        foreach (['location', 'reference'] as $k) {
            if (isset($object->{$k})) {
                $c = 0;
                foreach ($object->{$k} as $i) $c += $this->qsos($i);
                return $c;
            }
        }

        return $object->qso_cw + $object->qso_ssb + $object->qso_ft8;
    }

    public function references(object $object): int
    {
        $r = 0;

        if (isset($object->location)) foreach ($object->location as $l) $r += count($l->reference);
        if (isset($object->reference)) $r += count($object->reference);

        return $r;
    }

    public function summary(): object
    {
        $o = $this->operations;
        $l = 0;

        foreach ($o as $i) $l += count($i->location);

        return (object)[
            'reference' => count($this->references),
            'operation' => count($o),
            'location' => $l,
            'progress' => floor((count($this->planned) / count($this->references)) * 100),
            'planned' => count($this->planned),
            'hunter' => count($this->hunters()),
        ];
    }

    public function type(object $reference): string
    {
        foreach ($this->types as $p => $t) {
            if (preg_match($p, $reference->type)) return $t;
        }

        return $reference->type;
    }
}