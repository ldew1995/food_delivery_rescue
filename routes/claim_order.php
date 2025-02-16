<?php

// Read JSON Input
$data = json_decode(file_get_contents('php://input'), true);
$api_key = sanitizeInput($data['api_key'] ?? '');
$user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
$order_id = isset($data['order_id']) ? intval($data['order_id']) : 0;

// Step 1: API Authentication
if (!authenticate_request($db, $api_key)) {
    logError("Unauthorized access: API claim_order, api_key: $api_key", "WARNING");
    exit(json_encode(["error" => "Unauthorized API access"]));
}

// Step 2: Validate Input
if ($user_id <= 0 || $order_id <= 0) {
    logError("Invalid input: user_id: $user_id, order_id: $order_id", "ERROR");
    exit(json_encode(["error" => "Invalid input parameters"]));
}

writeLog("API called: claim_order, user_id: $user_id, order_id: $order_id", "INFO");

// Step 3: Check If Order Exists & Is Available
$db->where('id', $order_id);
$db->where('status', 'canceled');
$order = $db->getOne('orders', ['id', 'status', 'user_id']);

if (!$order) {
    logError("Order not available: user_id: $user_id, order_id: $order_id", "ERROR");
    exit(json_encode(['success' => false, 'message' => 'Order not available']));
}

// Step 4: Secure Update Query Using Bind Parameters
$db->where('id', $order_id);
$db->where('status', 'canceled');
$update = $db->update('orders', ['user_id' => $user_id, 'status' => 'claimed']);


// Step 5: Check Update Result
if ($update) {
    writeLog("Order claimed successfully: user_id: $user_id, order_id: $order_id", "INFO");

    // Step 6: Invalidate Redis Cache (Remove old cached order)
    $cache_pattern = "canceled_orders:*";
    $keys = $redis->keys($cache_pattern);
    foreach ($keys as $key) {
        $redis->del($key);
        writeLog("Cache invalidated: $key", "INFO");
    }

    echo json_encode(["success" => true, "message" => "Order claimed successfully"]);
} else {
    logError("Error claiming order: user_id: $user_id, order_id: $order_id", "ERROR");
    echo json_encode(["success" => false, "message" => "Error claiming order"]);
}
