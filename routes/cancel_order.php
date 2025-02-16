<?php

try {
    // Read JSON Input
    $data = json_decode(file_get_contents('php://input'), true);
    $order_id = isset($data['order_id']) ? intval($data['order_id']) : 0;
    $discounted_price = isset($data['discounted_price']) ? floatval($data['discounted_price']) : 0;
    $reason = isset($data['reason']) ? sanitizeInput($data['reason']) : '';

    // Authentication Check
    if (!verifyAuth($config)) {
        logError("Unauthorized access: API cancel_order", "WARNING");
        throw new Exception("Unauthorized API access");
    }

    // Validate Input
    if (empty($order_id) || empty($discounted_price)) {
        logError("Invalid input: order_id: $order_id, discounted_price: $discounted_price, reason: $reason", "ERROR");
        throw new Exception("Invalid input parameters");
    }

    writeLog("API called: cancel_order, order_id: $order_id, discounted_price: $discounted_price", "INFO");

    // Check if Order Exists
    $db->where('id', $order_id);
    $order = $db->getOne('orders', ['id', 'status', 'user_id']);

    if (!$order) {
        logError("Order not found: order_id $order_id", "ERROR");
        throw new Exception("Order not found");
    }

    if ($order['status'] === 'canceled') {
        writeLog("Order already canceled: order_id $order_id", "INFO");
        throw new Exception("Order is already canceled");
    }

    // Start Database Transaction
    $db->startTransaction();

    // Update Order Status
    $db->where('id', $order_id);
    $update = $db->update('orders', [
        'status' => 'canceled',
        'discounted_price' => $discounted_price,
        'reason' => $reason
    ]);

    if (!$update) {
        throw new Exception("Failed to update order status");
    }

    // Clear Cache (If Exists)
    $user_id = $order['user_id'];
    $cache_pattern = "canceled_orders:user_$user_id*";
    $keys = $redis->keys($cache_pattern);

    foreach ($keys as $key) {
        $redis->del($key);
        writeLog("Cache invalidated: $key", "INFO");
    }

    // Commit Transaction
    $db->commit();

    // Success Response
    writeLog("Order updated successfully: order_id $order_id, status: canceled", "INFO");
    echo json_encode(["success" => true, "message" => "Order marked as canceled"]);

} catch (Exception $e) {
    // Rollback Transaction in Case of Failure
    $db->rollback();

    // Log Error and Return JSON Response
    logError("Error: " . $e->getMessage(), "ERROR");
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
