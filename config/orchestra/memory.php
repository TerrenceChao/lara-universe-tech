<?php

return [
    /*
    |----------------------------------------------------------------------
    | Default Driver
    |----------------------------------------------------------------------
    |
    | Set default driver for Orchestra\Memory.
    |
    */

    'driver' => env('MEMORY_DRIVER', 'fluent.default'),

    /*
    |----------------------------------------------------------------------
    | Cache configuration
    |----------------------------------------------------------------------
    */

    'cache' => [],

    /*
    |----------------------------------------------------------------------
    | Eloquent configuration
    |----------------------------------------------------------------------
    */

    'eloquent' => [
        'default' => [
            'model' => 'Orchestra\Memory\Model',
            'cache' => false,
        ],
    ],

    /*
    |----------------------------------------------------------------------
    | Fluent configuration
    |----------------------------------------------------------------------
    */

    'fluent' => [
        'default' => [
            'table' => 'orchestra_options',
            'cache' => false,
        ],
    ],

    /*
    |----------------------------------------------------------------------
    | Runtime configuration
    |----------------------------------------------------------------------
    */

    'runtime' => [],
];
