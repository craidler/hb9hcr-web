<?php

namespace HB9HCR;

use HB9HCR\Mvc;

$routes = [
    [
        'label' => 'Operator',
        'topic' => 'Profile',
        'title' => 'Welcome to HB9HCR',
        'route' => '/',
        'template' => 'operator',
    ],
    [
        'label' => 'Mission',
        'topic' => 'Profile',
        'title' => 'Bunkers On The Air',
        'route' => '/mission',
        'template' => 'mission',
    ],
    [
        'label' => 'Campaign',
        'topic' => 'Profile',
        'title' => 'Campaign',
        'route' => '/campaign',
        'template' => 'campaign',
    ],
    [
        'label' => 'Limmatstellung',
        'topic' => 'Intel',
        'title' => 'Limmatstellung',
        'route' => '/limmatstellung',
        'template' => 'limmatstellung',
    ],
    [
        'label' => 'urdorf',
        'topic' => 'Intel',
        'title' => 'Sperrstelle Urdorf',
        'route' => '/urdorf',
        'template' => 'urdorf',
    ],
    [
        'label' => 'Operations',
        'topic' => 'Intel',
        'title' => 'Operations',
        'route' => '/operations',
        'template' => 'operations',
    ],
    [
        'label' => 'Gear',
        'topic' => 'Communication',
        'title' => 'Gear',
        'route' => '/gear',
        'template' => 'gear',
    ],
    [
        'label' => 'Prop',
        'topic' => 'Intel',
        'title' => 'Propagation',
        'route' => '/propagation',
        'template' => 'propagation',
    ],
    [
        'topic' => 'Program',
        'title' => 'BOTA - Bunkers On The Air',
        'route' => '/hbbota',
        'template' => 'hbbota',
    ],
    [
        'topic' => 'Data',
        'title' => 'BOTA',
        'route' => '/bota',
        'template' => 'bota',
    ],
    [
        'topic' => 'Data',
        'title' => 'Log',
        'route' => '/log',
        'template' => 'log',
    ],
];

$operations = (include __DIR__ . '/bota.php')['operations'];

foreach ($operations as $operation) {
    $routes[] = [
        'topic' => 'Campaign',
        'title' => 'Operation ' . ucfirst(strtolower($operation['name'])),
        'route' => '/operation/' . strtolower($operation['name']),
        'active' => '/operations',
        'template' => 'operation',
    ];
}

return $routes;