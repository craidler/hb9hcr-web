<?php
namespace HB9HCR;

use DateTime, DateTimeZone, DateInterval;

$filename = __DIR__ . '/../cam/snapshot.jpg';
$airing = file_exists($filename) && filemtime($filename) > time() - 15;

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta 
        <meta charset="UTF-8">
        <meta http-equiv="Refresh" content="15"/>
        <title>HB9HCR - <?= $airing ? 'On' : 'Off The' ?> Air</title>
        <link rel="stylesheet" href="/style/bootstrap.min.css">
        <link rel="stylesheet" href="/style/default.css?v=<?= time() ?>">
    </head>
    <body style="background:transparent;padding:1rem">
        <figure class="polaroid">
            <picture>
                <img src="<?= $airing ? '/cam/snapshot.jpg?v=' . time() : '/image/HB9HCR-Herman.gif' ?>" alt="HB9HCR - <?= $airing ? 'On' : 'Off The' ?> Air" width="100%">
            </picture>
            <figcaption<?= $airing ? ' class="text-danger text-airing fw-bold text-uppercase"' : '' ?>>
                HB9HCR is <?= $airing ? 'on' : 'off the' ?> air
            </figcaption>
        </figure>
    </body>
</html>