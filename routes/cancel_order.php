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
        jsonEncodeResponse(['success'=>false,'message'=> "Unauthorized API access."], 401);
    }

    // Validate Input
    if (empty($order_id) || empty($discounted_price)) {
        logError("Invalid input: order_id: $order_id, discounted_price: $discounted_price, reason: $reason", "ERROR");
        jsonEncodeResponse(['success'=>false,'message'=> "Invalid input parameters."], 400);
    }

    writeLog("API called: cancel_order, order_id: $order_id, discounted_price: $discounted_price", "INFO");

    // Check if Order Exists
    $db->where('id', $order_id);
    $order = $db->getOne('orders', ['id', 'status', 'user_id']);

    if (!$order) {
        logError("Order not found: order_id $order_id", "ERROR");
        jsonEncodeResponse(['success'=>false,'message'=> "Order not found."], 400);

    }

    if ($order['status'] === 'canceled') {
        writeLog("Order already canceled: order_id $order_id", "INFO");
        jsonEncodeResponse(['success'=>false,'message'=> "Order is already canceled."], 400);
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
        jsonEncodeResponse(['success'=>false,'message'=> "Failed to update order status."], status_code: 422);
    }

    // Clear Cache (If Exists)
    $user_id = $order['user_id'];
    $cache_pattern = "canceled_orders:*";
    $keys = $redis->keys($cache_pattern);

    foreach ($keys as $key) {
        $redis->del($key);
        writeLog("Cache invalidated: $key", "INFO");
    }

    // Commit Transaction
    $db->commit();

    // Success Response
    writeLog("Order updated successfully: order_id $order_id, status: canceled", "INFO");
    jsonEncodeResponse(['success'=>true,'message'=> "Order marked as canceled."], status_code: 200);


} catch (Exception $e) {
    // Rollback Transaction in Case of Failure
    $db->rollback();

    // Log Error and Return JSON Response
    logError("Error: " . $e->getMessage(), "ERROR");
    jsonEncodeResponse(["success" => false, "message" => $e->getMessage()] , 500);
}
