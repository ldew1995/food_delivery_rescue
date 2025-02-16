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
    ],

    'token_config' => [
        'secret_key' => '3f5e2c3b48a1d74a90e67f8c2d5b9a6f4e1c8d2b37a9b0d6c4e3f1a5b7d8c9e0',
        'token_expiry' => 3600,          //1 hour
        'refresh_token_expire' => 604800  // Refresh Token Expiry (7 days)
    ]

];
