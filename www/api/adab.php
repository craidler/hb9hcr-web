<?php

namespace HB9HCR;

require_once __DIR__ . '/../../vendor/autoload.php';

header("Content-Type: text/json");

$application = Mvc\Application::init(__DIR__ . '/../../');
$adab = $application->container->adab;
// $adab->import();

echo json_encode($adab->collection($_GET));