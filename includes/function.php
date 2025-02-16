<?php
//satize input values
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

//logs error
function logError($message) {
    $file = '../storage/logs/error.log';
    $logMessage = "[" . date("Y-m-d H:i:s") . "] $message\n";
    file_put_contents($file, $logMessage, FILE_APPEND);
}

//logs infos
function writeLog($message, $type = "INFO") {
    $file = '../storage/logs/api.log';
    $logMessage = "[" . date("Y-m-d H:i:s") . "] [$type] $message\n";
    file_put_contents($file, $logMessage, FILE_APPEND);
}

// Secure API key authentication
function authenticate_request($db, $api_key) {
    $query = "SELECT id FROM api_keys WHERE api_key = ? AND status = 'active' AND (expires_at IS NULL OR expires_at > NOW())";
    $result = $db->rawQueryOne($query, [$api_key]);

    return !empty($result);
}

// Rate limiting using Redis
function rate_limit($redis) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $count = $redis->incr($ip);
    $redis->expire($ip, 60);

    if ($count > 20) { // Max 20 requests per minute
        http_response_code(429);
        exit(json_encode(["error" => "Too many requests. Try again later."]));
    }
}