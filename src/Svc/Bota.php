<?php

namespace HB9HCR\Svc;

use Exception, CurlHandle, DateTime, DateInterval, ReflectionClass;

class Bota
{
    public object $container;
    public object $config;
    protected $connection;

    public function activations(): array
    {
        $s = $this->container->database->sql('wwbota/activation.collection.sql');
        $s->execute();
        $c = [];

        while ($r = $s->fetchObject()) {
            $c[] = $r;
        }

        return array_slice($c, 0, 10);
    }

    public function awards(): array
    {
        $collection = [];
        $statement = $this->container->database->sql('wwbota/award.collection.sql');
        $statement->execute();

        while ($item = $statement->fetchObject()) {
            $k = strtolower(sprintf('%s.%s.%s', $item->scheme, $item->type, $item->award));
            $item->levels = json_decode($item->levels);

            $collection[] = (object)[
                'image' => sprintf('%s.%s.%s', strtolower($item->scheme), $item->type, $item->award),
                'score' => $item->score,
                'scheme' => $item->scheme,
                'levels' => $item->levels,
                'level' => count($item->levels) ? strtolower($item->levels[count($item->levels) - 1]) : null,
                'description' => $this->config->awards->{$k} ?? explode('.', $k),
            ];
        }

        return $collection;
    }

    public function b2bs(): array
    {
        $s = $this->container->database->sql('wwbota/b2b.collection.sql');
        $s->execute();
        $c = [];

        while ($a = $s->fetchObject()) {
            foreach (json_decode($a->reference) as $r) {
                $c[$r->reference->reference] = (object)[
                    'date' => $a->date,
                    'reference' => $r->reference->reference,
                ];
            }
        }

        return array_slice($c, 0, 10);
    }

    public function operations(): array
    {
        $qsos = $this->container->database->sql('wwbota/log.collection.sql');
        $refs = $this->container->database->sql('wwbota/reference.collection.sql');
        $ops = (include $this->container->application->basedir . '/config/bota.php')['operations'];
        
        foreach ($ops as $i => $o) {
            $ops[$i] = $o = (object)array_merge($o, ['call' => [], 'ref' => 0, 'loc' => [], 'id' => sha1($o['name']), 'state' => 0]);

            foreach ($o->set as $j => $s) {
                $o->loc[] = $s[0];
                $o->set[$j] = $s = (object)['last' => null, 'lat' => explode('/', $s[0])[0], 'lng' => explode('/', $s[0])[1], 'ref' => $s[1], 'state' => 0, 'also' => $s[2] ?? []];

                foreach ($s->ref as $y => $r) {
                    $qsos->execute(['reference' => $r, 'id' => '%']);
                    $refs->execute(['reference' => $r]);

                    $o->ref++;
                    $s->ref[$y] = $r = (object)array_merge($refs->fetch(), ['cw' => 0, 'ssb' => 0, 'ft8' => 0, 'fm' => 0, 'state' => 0]);

                    while ($q = $qsos->fetchObject()) {
                        foreach (json_decode($q->modes) as $m) $r->{strtolower($m)}++;
                        $o->call[] = $q->hunter_call;
                        $s->last = !$s->last || $s->last < $q->time ? $q->time : $s->last;
                    }

                    $o->state += $r->state = match (true) {
                        25 <= $r->cw => 2,
                        0 < $r->cw + $r->ssb + $r->ft8 => 1,
                        default => 0
                    };
                }
            }

            $o->state = match(true) {
                $o->ref * 2 == $o->state => 2,
                0 < $o->state => 1,
                default => 0
            };

            $o->call = array_unique($o->call);
            $o->loc = array_unique($o->loc);
            foreach ($o->loc as $i => $l) $o->loc[$i] = (object)['lat' => explode('/', $l)[0], 'lng' => explode('/', $l)[1]];
            sort($o->call);
        }

        usort($ops, function ($a, $b) {
            return strcmp($a->name, $b->name);
        });

        return $ops;
    }

    public function statistics(object|array $options = []): array
    {
        $collection = [];
        $options = (object)array_merge(['role' => 'activator'], (array)$options);
        $select = $this->container->database->sql('wwbota/statistic.collection.sql');
        $select->execute(['role' => strtoupper($options->role[0])]);

        while ($r = $select->fetchObject()) $collection[] = $r;
        return $collection;
    }

    public function status(): object
    {
        $spot = $this->read([
            'endpoint' => 'spots/',
            'refresh' => true,
            'query' => ['age' => 24],
            'ttl' => 300,
            'pipeline' => [
                function ($data) {
                    foreach ($data as $item) {
                        if (preg_match('#hb9hcr#i', $item->call) && 'live' == strtolower($item->type)) {
                            return $item;
                        }
                    }

                    return null;
                },
            ],
        ]);

        return (object)array_merge([
            'airing' => (bool)$spot
        ], (array)$spot);
    }

    public function refreshActivations(object $options): void
    {
        $upsert = $this->container->database->sql('wwbota/activation.upsert.sql');
        $query = [];

        if (isset($options->start)) {
            $start = DateTime::createFromFormat('Ymd', date('Ymd'), null);
            $start->sub(new DateInterval($options->start));
            $query = ['start' => $start->format('Y-m-d')];
        }

        foreach ($this->read(['endpoint' => 'logs/activations', 'limit' => 100, 'query' => $query]) as $a) {
            $a->bunker_to_bunkers = json_encode($a->bunker_to_bunkers);
            $a->reference = $a->reference->reference;
            $a->modes = json_encode($a->modes);
            $a->bands = json_encode($a->bands);
            $upsert->execute((array)$a);
        }
    }

    public function refreshAwards(): void
    {
        $upsert = $this->container->database->sql('wwbota/award.upsert.sql');

        foreach ($this->read(['endpoint' => 'awards']) as $item) {
            $upsert->execute([
                'award' => $item->award,
                'details' => json_encode($item->details),
                'levels' => json_encode($item->levels),
                'name' => $item->name,
                'scheme' => $item->scheme,
                'score' => $item->score,
                'type' => $item->type,
            ]);
        }       
    }

    public function pushLogs(array $collection): void
    {
        foreach ($collection as $item) {
            $logs = [];
            $time = DateTime::createFromFormat('Ymd His', $item->qso_date . ' ' . $item->time_on, null);
            preg_match_all('#(B/HB-\d{4})#', $item->qslmsg, $m);
            
            // my own
            foreach ($m[1] as $r) {
                $logs[] = [
                    'activator_call' => $item->station_callsign,
                    'hunter_call' => $item->call,
                    'reference' => $r,
                    'time' => $time->format('Y-m-d\TH:i:s\Z'),
                    'band' => $item->band,
                    'mode' => $item->mode,
                    'rst_sent' => $item->rst_sent,
                    'rst_rcvd' => $item->rst_rcvd,
                ];
            }

            // b2b
            if (isset($item->sig_info)) {
                foreach (explode(',', $item->sig_info) as $r) {
                    $logs[] = [
                        'activator_call' => $item->call,
                        'hunter_call' => $item->station_callsign,
                        'reference' => $r,
                        'time' => $time->format('Y-m-d\TH:i:s\Z'),
                        'band' => $item->band,
                        'mode' => $item->mode,
                        'rst_sent' => $item->rst_rcvd,
                        'rst_rcvd' => $item->rst_sent,
                    ];
                }
            }

            $this->post(['endpoint' => 'logs', 'data' => $logs]);
        }
    }

    public function refreshLogs(object $options): void
    {
        $upsert = $this->container->database->sql('wwbota/log.upsert.sql');
        $query = [];

        if (isset($options->start)) {
            $start = DateTime::createFromFormat('Ymd', date('Ymd'), null);
            $start->sub(new DateInterval($options->start));
            $query = ['start' => $start->format('Y-m-d')];
        }

        foreach ($this->read(['endpoint' => 'logs/activator', 'limit' => 1000, 'query' => $query]) as $qso) {
            $upsert->execute([
                'id' => $qso->id,
                'activator_call' => $qso->activator_call,
                'activator_logged' => (int)$qso->activator_logged,
                'band' => $qso->band,
                'hunter_call' => $qso->hunter_call,
                'hunter_logged' => (int)$qso->hunter_logged,
                'modes' => json_encode($qso->modes),
                'reference' => $qso->reference->reference,
                'detail' => json_encode($qso->reference),
                'rejected' => (int)$qso->rejected,
                'rst_rcvd' => $qso->rst_rcvd,
                'rst_sent' => $qso->rst_sent,
                'time' => str_replace(['T', 'Z'], [' ', ''], $qso->time),
            ]);
        }
    }
    
    public function refreshReferences(object $options): void
    {
        $uri = 'https://api.wwbota.org/bunkers/?format=GEOJSON&scheme=' . $options->scheme;
        $data = json_decode(file_get_contents($uri));
        $upsert = $this->container->database->sql('wwbota/reference.upsert.sql');

        foreach ($data->features as $f) {
            $upsert->execute([
                'reference' => $f->properties->reference,
                'scheme' => $options->scheme,
                'name' => $f->properties->name,
                'type' => $f->properties->type,
                'lat' => $f->geometry->coordinates[1],
                'lng' => $f->geometry->coordinates[0],
                'loc' => $f->properties->locator,
            ]);
        }
    }

    public function refreshStatistics(object $options): void
    {
        $upsert = $this->container->database->sql('wwbota/statistic.upsert.sql');

        foreach (isset($options->role) ? [$options->role] : ['activator', 'hunter'] as $role) {
            $data = $this->read(['endpoint' => 'stats/activations/' . $role]);

            foreach ($data->monthly_activations as $year => $monthly) {
                foreach ($monthly as $month => $stats) {
                    $upsert->execute([
                        'activations' => $stats->activations,
                        'activators' => $stats->activators,
                        'bunkers' => $stats->bunkers,
                        'hunters' => $stats->hunters,
                        'month' => $month,
                        'year' => $year,
                        'role' => $role,
                    ]);
                }
            }
        }
    }

    protected function cache(object|array $options, object|array|null $data = null): object|array|null
    {
        $options = (object)array_merge(['ttl' => 3600], (array)$options);
        $filename = sprintf('%s/%s', $this->container->application->basedir . '/cache', sha1($options->endpoint));

        if ($data) {
            file_put_contents($filename, json_encode($data));
            return $data;
        }

        if (file_exists($filename) && filemtime($filename) > time() - $options->ttl) {
            return json_decode(file_get_contents($filename));
        }

        return null;
    }

    protected function connect(): CurlHandle
    {
        if (!$this->connection) {
            $endpoint = 'https://wwbota.net/wp-json/wwbota/v1/token';
            $header = [
                'Content-Type: application/json',
                'Accept: application/json',
            ];

            $data = $this->cache(['endpoint' => $endpoint, 'refresh' => true]);
            $c = curl_init();
            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

            if (!$data) {
                curl_setopt($c, CURLOPT_HTTPHEADER, $header);
                curl_setopt($c, CURLOPT_URL, $endpoint);
                curl_setopt($c, CURLOPT_POST, true);
                curl_setopt($c, CURLOPT_POSTFIELDS, json_encode([
                    'username' => $this->config->api->username,
                    'password' => $this->config->api->password,
                ]));

                $response = curl_exec($c);
                $info = (object)curl_getinfo($c);

                if (200 != $info->http_code) throw new Exception($response);

                $data = $this->cache(['endpoint' => $endpoint], json_decode($response));

                curl_setopt($c, CURLOPT_POST, false);
            }

            $header[] = 'Authorization: Bearer ' . $data->token;                
            curl_setopt($c, CURLOPT_HTTPHEADER, $header);
            $this->connection = $c;
        }

        return $this->connection;
    }

    protected function post(object|array $options): mixed
    {
        $connection = $this->connect();
        $options = (object)array_merge(['data' => []], (array)$options);

        curl_setopt($connection, CURLOPT_URL, $this->config->api->base . $options->endpoint);
        curl_setopt($connection, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($connection, CURLOPT_POST, true);
        curl_setopt($connection, CURLOPT_POSTFIELDS, json_encode($options->data));
                
        $response = curl_exec($connection);
        $info = (object)curl_getinfo($connection);

        curl_setopt($connection, CURLOPT_POST, false);

        if (201 != $info->http_code) throw new Exception($response);

        return $response;
    }

    protected function read(object|array $options): mixed
    {   
        $complete = false;            
        $options = (object)array_merge(['pipeline' => [], 'query' => [], 'limit' => 100, 'debug' => false], (array)$options);
        $offset = 0;
        $limit = $options->limit;
        $data = [];

        do {
            $query = http_build_query(array_merge($options->query, ['offset' => $offset, 'limit' => $limit]));
            $url = $this->config->api->base . $options->endpoint . ($query ? '?' . $query : '');
            $response = $this->cache(['endpoint' => $url, 'ttl' => $options->ttl ?? 3600]);

            if (!$response) {
                $connection = $this->connect();
                curl_setopt($connection, CURLOPT_URL, $url);
                $response = curl_exec($connection);
                $info = (object)curl_getinfo($connection);

                if (200 != $info->http_code) throw new Exception($response);

                $response = json_decode($response);
                $this->cache(['endpoint' => $url], $response);
            }

            if (is_object($response)) {
                $data = $response;
                $complete = true;
            }

            if (is_array($response)) {
                $data = array_merge($data, $response);
                $complete = count($response) < $limit;
                $offset += $limit;
            }
        }
        while (!$complete);

        foreach ($options->pipeline as $step) {
            $data = call_user_func_array($step, [$data]);
        }

        return $data;
    }
}