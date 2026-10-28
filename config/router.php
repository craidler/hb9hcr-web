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
        'label' => 'Operations',
        'topic' => 'Campaign',
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
        'template' => 'mission',
    ],
    [
        'topic' => 'Data',
        'title' => 'BOTA',
        'route' => '/bota',
        'template' => 'bota',
    ],
    [
        'topic' => 'Data',
        'title' => 'Planner',
        'route' => '/planner',
        'template' => 'planner',
    ],
    [
        'topic' => 'Data',
        'title' => 'Render',
        'route' => '/render',
        'template' => 'render',
    ],
];

$operations = (include __DIR__ . '/bota.php')['operations'];

foreach ($operations as $operation) {
    if (array_key_exists('route', $operation)) {
        $routes[] = [
            'topic' => 'Operation',
            'title' => $operation['name'],
            'route' => $operation['route'],
            'active' => '/operations',
            'template' => 'operation',
        ];
    }

}

return $routes;