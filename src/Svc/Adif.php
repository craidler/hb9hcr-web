<?php

namespace HB9HCR\Svc;

use PDO, DateTime;
use HB9HCR\Ui\Pagination;

class Adif
{
    public object $container;

    public function collection(object|array $options = []): array|Pagination
    {
        $collection = [];
        $statement = $this->container->database->sql('adif/log.collection.sql');
        $options = (object)array_merge(['callsign' => '%'], (array)$options);
        $i = 0;

        $statement->execute([
            'callsign' => $options->callsign,
        ]);

        while ($item = $statement->fetchObject()) {
            $chunks = explode('/', $item->callsign);

            usort($chunks, function($a, $b) {
                return strlen($b) <=> strlen($a);
            });

            $item->at = DateTime::createFromFormat('Y-m-d H:i:s', $item->qso_at, null);
            $item->rst = sprintf('%d/%d', $item->rst_sent, $item->rst_rcvd);
            $item->qrz = sprintf('https://qrz.com/db/%s', $chunks[0]);
            $collection[] = $item;
            $i++;

            if (isset($options->limit) && $i >= $options->limit) break;
        }

        if (isset($options->pagination)) {
            return Pagination::init($collection, $options->pagination);
        }

        var_dump($collection);

        return $collection;
    }

    public function insert(array $collection): self
    {
        $stmt = $this->container->database->sql('adif/log.insert.sql');

        foreach ($collection as $item) {
            $stmt->execute([
                'band' => $item->band,
                'callsign' => $item->call,
                'cqz' => $item->cqz,
                'dxcc' => $item->dxcc,
                'freq' => $item->freq,
                'info' => $item->my_sig_info,
                'ituz' => $item->ituz,
                'mode' => $item->mode,
                'pwr' => $item->tx_pwr ?? 100,
                'qsl' => $item->qslmsg,
                'qso_at' => (DateTime::createFromFormat('Ymd His', $item->qso_date . ' ' . $item->time_on, null))->format('Y-m-d H:i:s'),
                'rst_rcvd' => $item->rst_rcvd,
                'rst_sent' => $item->rst_sent,
                'sig' => $item->my_sig,
                'station' => $item->station_callsign,
            ]);
        }

        return $this;
    }

    public function parse(string $filename): array
    {
        $collection = [];

        $handle = fopen($filename, 'r');

        while (!feof($handle)) {
            $line = fgets($handle);
            if (!preg_match('#<EOR>$#', trim($line), $m)) continue;
            preg_match_all('/<([^:]+):[^>]+>([^<]*?)(?=\s*<|$)/', $line, $matches);
            $collection[] = (object)array_change_key_case(array_map('trim', array_combine($matches[1], $matches[2])), CASE_LOWER);
        }

        fclose($handle);

        return $collection;
    }

    public function upload(string $filename): self
    {
        $this->insert($this->parse($filename));
        unlink($filename);
        return $this;
    }
}