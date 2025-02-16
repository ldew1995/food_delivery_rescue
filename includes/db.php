<?php
// Include the configuration file
require_once '../vendor/MysqliDb/MysqliDb.php';
$config = require '../config/config.php';

// Create a new database connection using MySQLi-Database-Class
$db = new MysqliDb([
    'host' => $config['database']['host'],
    'username' => $config['database']['username'],
    'password' => $config['database']['password'],
    'db' => $config['database']['database'],
    'charset' => $config['database']['charset']
]);

// Check if the connection is successful
if (!$db) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}