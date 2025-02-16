<?php
require_once '../vendor/predis/autoload.php';
$config = require '../config/config.php';


// Connect to Redis
$redis = new Predis\Client([
    'host' => $config['redis']['host'],
    'port' => $config['redis']['port'],
    'database' => $config['redis']['database'],
    'username' => $config['redis']['username'],
    'password'=> $config['redis']['password'],
]);

// Check if the connection is successful
if (!$redis) {
    die(json_encode(['success' => false, 'message' => 'Redis connection failed']));
}

