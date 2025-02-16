<?php

// Read JSON Input
$data = json_decode(file_get_contents('php://input'), true);
$api_key = sanitizeInput($data['api_key'] ?? '');
$user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
$page = isset($data['page']) ? max(1, intval($data['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

writeLog("API called: get_canceled_orders, user_id: $user_id, page: $page", "INFO");

// Step 1: Check Authentication
if (!authenticate_request($db, $api_key)) {
    logError("Unauthorized access: API get_canceled_orders, api_key: $api_key", "WARNING");
    exit(json_encode(["error" => "Unauthorized API access"]));
}

// Step 2: Check Redis Cache First
$cache_key = "canceled_orders:user_$user_id:page_$page";
$canceled_orders = $redis->get($cache_key);

if (!$canceled_orders) {
    writeLog("Cache miss: Fetching orders from DB for user $user_id", "INFO");

    // Step 3: Fetch User Location & Preference (Cached)
    $user_cache_key = "user_location_pref:$user_id";
    $user = json_decode($redis->get($user_cache_key), true);

    if (!$user) {
        $db->where('id', $user_id);
        $user = $db->getOne('users', ['latitude', 'longitude', 'preference']);

        if (!$user) {
            logError("User not found, user_id: $user_id", "ERROR");
            exit(json_encode(['success' => false, 'message' => 'User not found']));
        }
        
        $redis->setex($user_cache_key, 600, json_encode($user)); // Cache for 10 min
    }

    $latitude = $user['latitude'];
    $longitude = $user['longitude'];
    $preference = $user['preference'];

    // Step 4: Optimized Query to Find Canceled Orders Within 3KM
    writeLog("Fetching canceled orders for user $user_id", "INFO");

    $query = "
        SELECT o.id, o.original_price, o.discounted_price, o.latitude, o.longitude, r.name AS restaurant_name 
        FROM orders o
        INNER JOIN restaurants r ON o.restaurant_id = r.id
        WHERE o.status = 'canceled'
        AND (6371 * acos( cos(radians(?)) * cos(radians(o.latitude)) * cos(radians(o.longitude) - radians(?)) + sin(radians(?)) * sin(radians(o.latitude)) )) <= 3
        " . ($preference === "vegetarian" ? 
            "AND NOT EXISTS (SELECT 1 FROM order_items WHERE order_id = o.id AND category = 'non-veg')" 
            : "") . 
        " AND NOT EXISTS (SELECT 1 FROM order_items WHERE order_id = o.id AND category = 'sensitive') 
        ORDER BY o.id ASC
        LIMIT ?, ?";

    $params = [$latitude, $longitude, $latitude, $offset, $limit];

    $orders = $db->rawQuery($query, $params);

    // Step 5: Fetch Order Items in Batch (Avoid N+1 Queries)
    if (!empty($orders)) {
        $orderIds = array_column($orders, 'id');
        $db->where('order_id', $orderIds, 'IN');
        $items = $db->get('order_items', null, ['order_id', 'item_name', 'category']);

        // Map items to orders
        $orderItems = [];
        foreach ($items as $item) {
            $orderItems[$item['order_id']][] = [
                'item_name' => $item['item_name'],
                'category' => $item['category']
            ];
        }

        foreach ($orders as &$order) {
            $order['items'] = $orderItems[$order['id']] ?? [];
        }
    }

    // Step 6: Get Total Count for Pagination
    $total_orders = $db->getValue("orders o", "count(*)");
    $total_pages = ceil($total_orders / $limit);

    $response = json_encode(["orders" => $orders, "pagination" => ["current_page" => $page, "total_pages" => $total_pages, "total_orders" => $total_orders]]);

    // Step 7 Store in Redis for Caching (5 Min Expiry)
    $redis->setex($cache_key, 300, $response);
    writeLog("Cached orders for user $user_id, page $page", "INFO");

    $canceled_orders = $response;
}

// Step 8: Return JSON Response
writeLog("Returning canceled orders for user $user_id", "INFO");
echo $canceled_orders;
