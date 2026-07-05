<?php

namespace HB9HCR;

session_start();

require_once __DIR__ . '/../../vendor/autoload.php';

Book::$config = Util::mergeJson(__DIR__ . '/../../config/default.json', __DIR__ . '/../../config/local.json')->book;

if (isset($_POST['book-code']) && strtoupper($_POST['book-code']) == $_SESSION['captcha']) {
    Book::add($_POST['book-name']);
}

$collection = Book::read();

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>HB9HCR</title>
        <link rel="stylesheet" href="/style/bootstrap.min.css">
        <link rel="stylesheet" href="/style/default.css?v=<?= time() ?>">
    </head>
    <body class="bg-transparent mx-3 mt-1">
        <div class="card-body">
            <form method="post" action="/frame/book.php" id="book-form"></form>
            <div class="input-group mb-3">
                <span class="input-group-text">Callsign</span>
                <input type="text" name="book-name" class="form-control" form="book-form" required>
                <div class="input-group-text p-0"><img src="/image/captcha.php"></div>
                <input type="text" name="book-code" class="form-control" form="book-form" required>
                <button type="submit" class="btn btn-secondary" form="book-form">submit</button>
            </div>
       </div>
        <div class="card-body">
            <div class="row">
            <?php foreach ($collection as $j => $entry): ?>
                <div class="col-1"><?= sprintf('73 de %s', strtoupper($entry->name)) ?></div>
            <?php endforeach; ?>
            </div>
        </div>
    </body>
</html>