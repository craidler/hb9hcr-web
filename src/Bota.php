<?php

namespace HB9HCR;

use Exception, CurlHandle, ArrayObject, DateTime, DateTimeZone, stdClass;

class Bota
{
    protected static $curl;
    public static $config;

    public static function read(array $options): ArrayObject|stdClass
    {
        $options = (object)array_merge([], $options);
        $options->endpoint = static::$config->base . $options->endpoint;
        $data = static::cache(['key' => $options->endpoint, 'ttl' => static::$config->ttl ?? 3600]);

        if (!$data) {
            $curl = static::connect();
            curl_setopt($curl, CURLOPT_URL, $options->endpoint);

            $data = curl_exec($curl);
            $info = curl_getinfo($curl);

            if (200 != $info['http_code']) {
                throw new Exception(sprintf('code %d on reading %s', $info['http_code'], $options->endpoint));
            }

            $data = static::cache(['key' => $options->endpoint], $data);
        }

        $data = json_decode($data);

        return is_array($data) ? new ArrayObject($data) : $data;
    }

    protected static function cache(array $options, string $data = null): string|false
    {
        $filename = __DIR__ . '/../cache/' . base64_encode($options['key']);

        if ($data) {
            file_put_contents($filename, $data);
            return $data;
        }

        if (!file_exists($filename) || filemtime($filename) < time() - $options['ttl']) {
            return false;
        }
        
        return file_get_contents($filename);
    }

    protected static function connect(): CurlHandle
    {
        if (!static::$curl) {
            static::$curl = $curl = curl_init();

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . static::token(['endpoint' => static::$config->token]),
                'Accept: application/json',
                'Content-Type: application/json',
            ]);
        }

        return static::$curl;
    }

    protected static function token(array $options): string
    {
        $options = (object)array_merge([], $options);
        $data = static::cache(['key' => $options->endpoint, 'ttl' => static::$config->ttl ?? 3600]);

        if (!$data) {
            $curl = static::$curl;

            curl_setopt($curl, CURLOPT_URL, $options->endpoint);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
            ]);

            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
                'username' => static::$config->username,
                'password' => static::$config->password,
            ]));

            $data = curl_exec($curl);
            $info = curl_getinfo($curl);

            if (200 != $info['http_code']) {
                throw new Exception(sprintf('code %d on reading %s', $info['http_code'], $options->endpoint));
            }

            curl_setopt($curl, CURLOPT_POST, false);
            
            $data = static::cache(['key' => $options->endpoint], $data);
        }

        return (json_decode($data))->token;
    }
}