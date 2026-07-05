<?php
namespace HB9HCR;

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$mode = strtolower($_GET['mode'] ?? 'default');
$config = Util::mergeJson(__DIR__ . '/../config/default.json', __DIR__ . '/../config/local.json');
$config = $config->{$mode};

if ('admin' == $mode && (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] !== $config->username || $_SERVER['PHP_AUTH_PW'] !== $config->password)) {
    header('WWW-Authenticate: Basic realm="Restricted File"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Access Denied.';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <link rel="icon" href="/image/HB9HCR-Favicon.svg" type="image/svg+xml">
        <title>HB9HCR</title>
        <link rel="stylesheet" href="/style/bootstrap.min.css">
        <link rel="stylesheet" href="/style/default.css?v=<?= time() ?>">
    </head>
    <body>
        <div class="container">
        <?php foreach ($config->cards as $i => $card): ?>
            <div class="card" id="<?= $card ?>">
                <?php include __DIR__ . '/card/' . $card . '.php' ?>
                <div class="card-footer">
                    <span>Form 6.005 dfi / ALN 293-2257 / SAP 2526.0799 / <?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </body>
</html>