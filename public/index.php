<?php
require_once '../config/config.php';
require_once '../includes/db.php';
require_once '../includes/redis.php';
require_once '../includes/function.php';
require_once '../includes/jwt_functions.php';

rate_limit($redis);  // Prevent abuse

// Define the base path (adjust if needed)
$basePath = $config['app_config']['base_path'];

// Get request URI and remove query strings
$request = strtok($_SERVER['REQUEST_URI'], '?');

// Remove the base path from the request URI
if (strpos($request, $basePath) === 0) {
    $request = substr($request, strlen($basePath));
}

// Trim leading slash (optional)
$request = ltrim($request, '/');

switch ($request) {
    case 'register' :
        require '../routes/register.php';
        break;
    case 'oauth-token' :
        require '../routes/oauth_token.php';
        break;
    case 'cancel-order' :
        require '../routes/cancel_order.php';
        break;
    case 'claim-order' :
        require '../routes/claim_order.php';
        break;
    case 'get-canceled-orders' :
        require '../routes/get_canceled_orders.php';
        break;
    default:
        http_response_code(404);
        logError('404 Not Found, url:'.$request);
        echo "404 Not Found";
        break;
}