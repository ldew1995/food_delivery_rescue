<?php
// Configuration file (config.php)

return [
    'database' => [
        'host'     => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'food_delivery_rescue',
        'charset'  => 'utf8',
    ],
    
    'redis' => [
        'host'     => '',                       //'127.0.0.1',
        'port'     => '',                       //6379,
        'database' => 0,
        'username' => '',
        'password'=> '',    
    ],

    'app_config' => [
        'base_path' => '/food_delivery_rescue'
    ]
];
