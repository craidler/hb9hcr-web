<?php

namespace HB9HCR\Svc;

use GlobIterator;

class Adab
{
    public object $container;

    public function import(): self
    {
        $upsert = $this->container->database->sql('adab/object.upsert.sql');
        $glob = new GlobIterator(__DIR__ . '/../data/*.csv');

        foreach ($glob as $file) {
            $content = file($file->getRealpath());

            foreach ($content as $line) {
                $fields = explode(';', trim($line));
                list($lat, $lng) = explode(', ', $fields[3]);

                $upsert->execute([
                    'reference' => $fields[0],
                    'name' => ucwords(strtolower($fields[1])),
                    'type' => $fields[2],
                    'lat' => $lat,
                    'lng' => $lng,
                ]);
            }
        }

        return $this;
    }

    public function upsert(object|array $data): bool
    {
        $upsert = $this->container->database->sql('adab/object.upsert.sql');
        $data = (object)$data;

        $upsert->execute([
            'reference' => strtoupper(trim($data->reference)),
            'name' => trim($data->name),
            'type' => trim($data->type),
            'lat' => $data->lat,
            'lng' => $data->lng,
        ]);

        return true;
    }

    public function collection(object|array $options = null): array
    {
        $collection = [];
        $options = (object)array_merge(['reference' => '%', 'lat' => 46.75, 'lng' => 8.2, 'distance' => 1000000], (array)$options);

        if (isset($options->references)) {
            $select = $this->container->database->sql('adab/object.item.sql');

            foreach ($options->references as $r) {
                $select->execute(['reference' => $r]);

                if ($item = $select->fetchObject()) {
                    $collection[] = $item;
                }
            }

            return $collection;
        }

        $select = $this->container->database->sql('adab/object.collection.sql');
        $select->execute((array)$options);
        while ($item = $select->fetchObject()) $collection[] = $item;
        return $collection;
    }
}