<?php

try {
    // Read JSON Input
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
    $order_id = isset($data['order_id']) ? intval($data['order_id']) : 0;

    // API Authentication
    if (!verifyAuth($config)) {
        logError("Unauthorized access: API claim_order", "WARNING");
        jsonEncodeResponse(['success'=>false,'message'=> "Unauthorized API access."], 401);
    }

    // Validate Input
    if (empty($user_id) || empty($order_id)) {
        logError("Invalid input: user_id: $user_id, order_id: $order_id", "ERROR");
        jsonEncodeResponse(['success'=>false,'message'=> "Invalid input parameters."], 400);
    }

    writeLog("API called: claim_order, user_id: $user_id, order_id: $order_id", "INFO");

    // Start Transaction
    $db->startTransaction();

    // Check If Order Exists & Is Available
    $db->where('id', $order_id);
    $db->where('status', 'canceled');
    $order = $db->getOne('orders', ['id', 'restaurant_id', 'status', 'original_price', 'discounted_price', 'latitude', 'longitude']);

    if (!$order) {
        logError("Order not available, order_id: $order_id");
        jsonEncodeResponse(['success'=>false,'message'=> "Order not available."], 400);
    }

    // Secure Update Query Using Bind Parameters
    $db->where('id', $order_id);
    $db->where('status', 'canceled');
    $update = $db->update('orders', ['status' => 'claimed']);

    if (!$update) {
        logError("Order update failed.");
        jsonEncodeResponse(['success'=>false,'message'=> "Order update failed."], 400);
    }

    // Insert new order as a claimed order
    $arr_ins = [
        'parent_id' => $order_id,
        'restaurant_id' => $order['restaurant_id'],
        'user_id' => $user_id,
        'status' => 'claimed',
        'original_price' => $order['original_price'],
        'discounted_price' => $order['discounted_price'],
        'latitude' => $order['latitude'],
        'longitude' => $order['longitude'],
    ];

    $new_order_id = $db->insert('orders', $arr_ins);
    if (!$new_order_id) {
        logError("Order insertion failed.");
        jsonEncodeResponse(['success'=>false,'message'=> "Order insertion failed."], 400);
    }

    // Fetch user preference
    $db->where('id', $user_id);
    $user = $db->getOne('users', ['id', 'preference']);

    // Fetch & Insert Order Items (Filtering based on user preference)
    $db->where('order_id', $order_id);
    $db->where('category', ['sensitive'], 'NOT IN');
    if ($user['preference'] == 'vegetarian') {
        $db->where('category', ['sensitive'], 'non-veg');
    }
    $order_items = $db->get('order_items', null, ['item_name', 'category']);
    if (!$order_items) {
        logError("Order items does not exist.");
        jsonEncodeResponse(['success'=>false,'message'=> "Order items does not exist."], 404);
    }
    foreach ($order_items as $item) {
        $item_ins = [
            'order_id' => $new_order_id,
            'item_name' => $item['item_name'],
            'category' => $item['category']
        ];
        if (!$db->insert('order_items', $item_ins)) {
            logError("Order item insertion failed.");
            jsonEncodeResponse(['success'=>false,'message'=> "Order item insertion failed."], 400);
        }
    }

    // Fetch & Insert Delivery Partner Details
    $db->where('order_id', $order_id);
    $delivery_partner = $db->getOne('order_delivery', ['delivery_partner_id', 'status']);

    if ($delivery_partner) {
        $partner_ins = [
            'order_id' => $new_order_id,
            'delivery_partner_id' => $delivery_partner['delivery_partner_id'],
        ];
        if (!$db->insert('order_delivery', $partner_ins)) {
            logError("Order Delivery insertion failed.");
            jsonEncodeResponse(['success'=>false,'message'=> "Order Delivery insertion failed."], 400);
        }
    }

    // Commit transaction after all successful operations
    $db->commit();
    writeLog("Order claimed successfully: user_id: $user_id, order_id: $order_id", "INFO");

    // Invalidate Redis Cache (Remove old cached order)
    $cache_pattern = "canceled_orders:*";
    $keys = $redis->keys($cache_pattern);
    foreach ($keys as $key) {
        $redis->del($key);
        writeLog("Cache invalidated: $key", "INFO");
    }

    jsonEncodeResponse(['success'=>true,'message'=> "Order claimed successfully.", 'data' => ["new_order_id" => $new_order_id]], 200);

} catch (Exception $e) {
    // Rollback transaction if any error occurs
    $db->rollback();
    logError("Transaction failed: " . $e->getMessage(), "ERROR");
    jsonEncodeResponse(["success" => false, "message" => $e->getMessage()] , 500);
}

