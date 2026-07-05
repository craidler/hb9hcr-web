<?php

namespace HB9HCR;

require_once __DIR__ . '/../../vendor/autoload.php';

$directory = __DIR__ . '/../image/qsl/';

if (isset($_POST['callsign'])) {
    $qso = Log::latest(Log::find($_POST['callsign'], __DIR__ . '/../../data/*.adi'));
    if ($qso) Qsl::render($qso, $directory);
}

$cards = Qsl::cards($directory . '/*.jpg');
$limit = 9;
$card = Qsl::latest(Qsl::cards($directory . '*.jpg'));

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>HB9HCR</title>
        <link rel="stylesheet" href="/style/bootstrap.min.css">
        <link rel="stylesheet" href="/style/default.css?v=<?= time() ?>">
    </head>
    <body class="bg-transparent mx-3 pt-1">
        <form method="post" action="/frame/qsl.php" id="qsl-form"></form>
        <div class="input-group mb-5">
            <span class="input-group-text">Callsign</span>
            <input type="text" name="callsign" class="form-control"form="qsl-form" required>
            <button type="submit" form="qsl-form" class="btn btn-secondary">generate</button>
        </div>
        <div class="card-body">
            <figure class="polaroid">
                <picture>
                    <img src="/image/qsl/<?= $card->callsign ?>.jpg?v=<?= time() ?>">
                </picture>
                <figcaption>
                    QSL card for <?= $card->callsign ?>
                </figcaption>
            </figure>
        </div>
        <div class="card-body pt-0">
            <h5 class="text-end mb-4">Recently generated</h5>
            <p>I digitalized my own handwriting, added some jitter to the positioning - so every cards composition is unique. Except from paper, this is as close as it gets to the real thing.</p>
            <div class="row">
            <?php foreach ($cards as $j => $card): ?>
                <?php if ($j >= $limit) break; ?>
                <div class="col-4">
                    <figure class="polaroid">
                        <picture>
                            <img src="/image/qsl/<?= $card->info->getBasename() ?>" alt="<?= $card->callsign ?>">
                        </picture>
                        <figcaption>
                            <?= $card->callsign ?>
                        </figcaption>
                    </figure>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </body>
</html>