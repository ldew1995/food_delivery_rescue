<?php

// Read JSON Input
$data = json_decode(file_get_contents('php://input'), true);
$api_key = sanitizeInput($data['api_key'] ?? '');
$order_id = isset($data['order_id']) ? intval($data['order_id']) : 0;
$discounted_price = isset($data['discounted_price']) ? floatval($data['discounted_price']) : 0;
$reason = isset($data['reason']) ? sanitizeInput($data['reason']) : 0;

// Step 1: Authentication Check
if (!authenticate_request($db, $api_key)) {
    logError("Unauthorized access: API cancel_order, api_key: $api_key", "WARNING");
    exit(json_encode(["error" => "Unauthorized API access"]));
}

// Step 2: Validate Input
if (!$order_id || !$reason || $discounted_price < 0) {
    logError("Invalid input: order_id: $order_id, discounted_price: $discounted_price, reason: $reason", "ERROR");
    exit(json_encode(["error" => "Invalid input parameters"]));
}

writeLog("API called: cancel_order, order_id: $order_id, discounted_price: $discounted_price", "INFO");

// Step 3: Check if Order Exists
$db->where('id', $order_id);
$order = $db->getOne('orders', ['id', 'status', 'user_id']);

if (!$order) {
    logError("Order not found: order_id $order_id", "ERROR");
    exit(json_encode(["success" => false, "message" => "Order not found"]));
}

if ($order['status'] === 'canceled') {
    writeLog("Order already canceled: order_id $order_id", "INFO");
    exit(json_encode(["success" => false, "message" => "Order is already canceled"]));
}

// Step 4: Update Order Status
$db->where('id', $order_id);
$update = $db->update('orders', [
    'status' => 'canceled',
    'discounted_price' => $discounted_price,
    'reason' => $reason
]);

// Step 5: Check Update Result
if ($update) {
    writeLog("Order updated successfully: order_id $order_id, status: canceled", "INFO");

    // Step 6: Clear Cache (If Exists)
    $user_id = $order['user_id'];
    $cache_pattern = "canceled_orders:user_$user_id*";
    $keys = $redis->keys($cache_pattern);
    
    foreach ($keys as $key) {
        $redis->del($key);
        writeLog("Cache invalidated: $key", "INFO");
    }

    echo json_encode(["success" => true, "message" => "Order marked as canceled"]);
} else {
    logError("Failed to update order: order_id $order_id", "ERROR");
    echo json_encode(["success" => false, "message" => "Error updating order"]);
}

