<?php

namespace HB9HCR;

require_once __DIR__ . '/../../vendor/autoload.php';

$directory = __DIR__ . '/../../data/';

if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    move_uploaded_file($file['tmp_name'], $directory . str_replace(' ' , '-', $file['name']));
}

$log = (object)[
    'percent' => 0,
    'files' => Log::glob($directory . '*.adi'),
    'size' => 0,
    'max' => pow(1024, 2) * 10,
];

foreach ($log->files as $info) $log->size += $info->getSize();
$log->percent = ($log->size / $log->max) * 100;

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>HB9HCR</title>
        <link rel="stylesheet" href="/style/bootstrap.min.css">
        <link rel="stylesheet" href="/style/default.css?v=<?= time() ?>">
    </head>
    <body class="bg-transparent m-3">
        <form method="post" action="/frame/log.php" id="log-form" enctype="multipart/form-data"></form>
        <div class="row">
            <div class="col-8">
                <input type="file" name="file" class="form-control mb-3" form="log-form" accept=".adi">
                <p><?= count($log->files) ?> files, <?= $log->size ?> bytes, <?= sprintf('%.2f', $log->percent) ?>%</p>
            </div>
            <div class="col-4">
                <button class="btn btn-secondary w-100" type="submits" form="log-form">upload</button>
                <div class="progress mt-3">
                    <div class="progress-bar bg-info" style="width:<?= round($log->percent) ?>%"></div>
                </div>
            </div>
        </div>
        <table class="table my-3">
            <thead>
                <tr>
                    <th>File</th>
                    <th class="shrink">Size</th>
                    <th class="shrink">Modified</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($log->files as $file): ?>
                <tr>
                    <td><?= $file->getBasename() ?></td>
                    <td class="text-end"><?= sprintf('%.2f kB', $file->getSize() / 1024) ?></td>
                    <td class="text-end"><?= date('Y-m-d H:i:s', $file->getMTime()) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </body>
</html>