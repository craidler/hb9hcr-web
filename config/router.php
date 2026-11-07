<?php

namespace HB9HCR;

use HB9HCR\Mvc;

$routes = [
    [
        'label' => 'OPR',
        'topic' => 'Profile',
        'title' => 'Welcome to HB9HCR',
        'route' => '/',
        'template' => 'operator',
    ],
    [
        'label' => 'MSN',
        'topic' => 'Profile',
        'title' => 'Bunkers On The Air',
        'route' => '/mission',
        'template' => 'mission',
    ],
    [
        'label' => 'CPG',
        'topic' => 'Profile',
        'title' => 'Campaign',
        'route' => '/campaign',
        'template' => 'campaign',
    ],
    [
        'label' => 'OPS',
        'topic' => 'Campaign',
        'title' => 'Operations',
        'route' => '/operations',
        'template' => 'operations',
    ],
    [
        'label' => str_contains($_SERVER['SERVER_NAME'], 'localhost') ? 'EQP' : null,
        'topic' => 'Communication',
        'title' => 'Signal Equipment',
        'route' => '/equipment',
        'template' => 'equipment',
    ],
    [
        'label' => 'PRP',
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
        'label' => str_contains($_SERVER['SERVER_NAME'], 'localhost') ? 'PLN' : null,
        'topic' => 'Data',
        'title' => 'Planner',
        'route' => '/planner',
        'template' => 'planner',
    ],
    [
        'label' => str_contains($_SERVER['SERVER_NAME'], 'localhost') ? 'DATA' : null,
        'topic' => 'Data',
        'title' => 'DATA',
        'route' => '/data',
        'template' => 'data',
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