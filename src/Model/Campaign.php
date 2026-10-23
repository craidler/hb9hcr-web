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

    public function init(): void
    {
        $select = $this->database->sql('campaign/reference.collection.sql');
        $select->execute(['adab' => '^[AF]', 'bota' => '.*']);

        $this->planned = new Collection();
        $this->operations = new Collection();
        $this->references = Collection::createWithStatement($select, ['key' => function ($item) {
            return $item->adab;
        }]);

        $select->execute(['adab' => '.*', 'bota' => '^B/HB-']);
        $references = Collection::createWithStatement($select, ['key' => function ($item) {
            $item->qso_hunters = json_decode($item->qso_hunters);
            return $item->bota;
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
                    $reference[] = $r = $references->offsetGet($r);
                    $this->planned->offsetSet($r->adab, $r);
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
        $reference->planned = $this->planned->offsetExists($reference->adab);

        $c = match(true) {
            'F' == $reference->adab[0] => '#feee00',
            'T' == $reference->adab[0] => 'green',
            default => '#ff6052',
        };

        $c = $reference->bota ? '#b14137ff' : $c;
        $c = $reference->planned ? '#1a5c95ff' : $c;
        $c = 0 < $reference->qso_qty ? '#2a81cbff' : $c;
        $c = 25 <= $reference->qso_cw ? '#35bf6aff' : $c;

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
            foreach ($object->reference as $i) $c = array_merge($c, $i->qso_hunters); 
        }

        $c = array_unique($c);
        sort($c);

        return $c;
    }

    public function operation(string $route): object
    {
        foreach ($this->container->campaign->operations as $o) {
            if ($route == $o->route) return $o;
        }

        throw new Exception();
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
}