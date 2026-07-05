<?php

namespace HB9HCR;

use GlobIterator, ArrayObject, stdClass, traversable;

class Qsl
{
    public static function cards(string $haystack): ArrayObject
    {
        $cards = [];

        foreach (new GlobIterator($haystack) as $info) {
            $cards[] = (object)[
                'callsign' => $info->getBasename('.jpg'),
                'info' => $info, 
            ];
        }

        usort($cards, function ($a, $b) {
            if ($a->info->getMTime() < $b->info->getMTime()) return +1;
            if ($a->info->getMTime() > $b->info->getMTime()) return -1;
            return 0;
        });

        return new ArrayObject($cards);
    }

    public static function latest(traversable $cards): stdClass|null
    {
        $latest = null;

        foreach ($cards as $card) {
            if (!$latest) $latest = $card;
            if ($latest->info->getMTime() < $card->info->getMTime()) $latest = $card;
        }

        return $latest;
    }

    public static function render(Qso $qso, string $directory): void
    {
        $config = json_decode(file_get_contents(__DIR__ . '/../config/qsl.json'));
        $img = imagecreatefromjpeg(__DIR__ . '/../www/' . $config->background);
        imagealphablending($img, true);
        imagesavealpha($img, true);

        foreach ($config->elements as $element) static::{$element->type}($img, $element, $qso);
        imagejpeg($img, $directory . '/' . $qso->call . '.jpg');
    }

    protected static function image($img, stdClass $options): void
    {
        $transparency = 1 - ($options->opacity ?? 1);
        $resize = $options->resize ?? 1;
        $jitter = new stdClass();
        $jitter->x = random_int(($options->jitter ?? 0) * -1, $options->jitter ?? 0);
        $jitter->y = random_int(($options->jitter ?? 0) * -1, $options->jitter ?? 0);
        $layer = imagecreatefrompng(__DIR__ . '/../www/' . $options->filename);
        $alpha = ceil(127 * $transparency);
        $x = ($options->x ?? 0) + $jitter->x;
        $y = ($options->y ?? 0) + $jitter->y;

        imagealphablending($layer, false);
        imagesavealpha($layer, true);
        imagefilter($layer, IMG_FILTER_COLORIZE, 0, 0, 0, $alpha);

        imagecopyresampled(
            $img, $layer, 
            $x, $y,
            0, 0,
            imagesx($layer) * $resize, imagesy($layer) * $resize,
            imagesx($layer), imagesy($layer)
        );
    }

    protected static function line($img, stdClass $options): void
    {
        $color = $options->color ?? [0, 0, 0];
        $color = imagecolorallocate($img, $color[0], $color[1], $color[2]);
        $x = $options->x ?? [0, 0];
        $y = $options->y ?? [0, 0];
        imageline($img, $x[0], $y[0], $x[1], $y[1], $color);
    }

    protected static function rect($img, stdClass $options): void
    {
        $color = $options->color ?? [0, 0, 0];
        $color = imagecolorallocate($img, $color[0], $color[1], $color[2]);
        $x = $options->x ?? [0, 0];
        $y = $options->y ?? [0, 0];
        imagerectangle($img, $x[0], $y[0], $x[1], $y[1], $color);
    }

    protected static function text($img, stdClass $options, Qso $qso): void
    {
        $jitter = new stdClass();
        $jitter->x = random_int(($options->jitter ?? 0) * -1, $options->jitter ?? 0);
        $jitter->y = random_int(($options->jitter ?? 0) * -1, $options->jitter ?? 0);
        $color = $options->color ?? [0, 0, 0];
        $color = imagecolorallocate($img, $color[0], $color[1], $color[2]);
        $align = $options->align ?? 'left';
        $angle = $options->angle ?? 0;
        $font = __DIR__ . '/../font/' . ($options->font ?? 'NotoSansMono') . '.ttf';
        $text = $options->text ?? 'text';
        $text = preg_match('#{(.+?)}#', $text, $m) ? $qso->{$m[1]} : $text;
        $size = $options->size ?? 8;
        $box = imagettfbbox($size, $angle, $font, $text);
        $x = $options->x ?? 0;
        $y = $options->y ?? 0;
        $x -= 'center' == $align ? ($box[4] - $box[0]) / 2 : 0;
        $x -= 'right' == $align ? $box[4] - $box[0] : 0;

        imagettftext($img, $size, $angle ?? 0, ceil($x + $jitter->x), ceil($y + $jitter->y), $color, $font, $text);
    }
}