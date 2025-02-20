<?php

try {
    // Read JSON Input
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
    $page = isset($data['page']) ? max(1, intval($data['page'])) : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    writeLog("API called: get_canceled_orders, user_id: $user_id, page: $page", "INFO");

    // Step 1: Check Authentication
    if (!verifyAuth($config)) {
        logError("Unauthorized access: API get_canceled_orders", "WARNING");
        jsonEncodeResponse(['success'=>false,'message'=> "Unauthorized API access."], 401);
    }

    // Step 2: Validate Input
    if (empty($user_id)) {
        logError("Invalid input: user_id: $user_id", "ERROR");
        jsonEncodeResponse(['success'=>false,'message'=> "Invalid input parameters."], 400);
    }

    // Step 3: Check Redis Cache First
    $cache_key = "canceled_orders:user_$user_id:page_$page";
    $canceled_orders = json_decode($redis->get($cache_key), true);

    if (empty($canceled_orders)) {
        writeLog("Cache miss: Fetching orders from DB for user $user_id", "INFO");

        // Step 4: Fetch User Location & Preference (Cached)
        $user_cache_key = "user_location_pref:$user_id";
        $user = json_decode($redis->get($user_cache_key), true);

        if (!$user) {
            $db->where('id', $user_id);
            $user = $db->getOne('users', ['latitude', 'longitude', 'preference']);

            if (!$user) {
                logError("User not found, user_id: $user_id", "ERROR");
                throw new Exception("User not found");
            }

            $redis->setex($user_cache_key, 600, json_encode($user)); // Cache for 10 min
        }

        $latitude = $user['latitude'];
        $longitude = $user['longitude'];
        $preference = $user['preference'];

        // Get min-max latitude and min-max longitude within 3 km
        $box = getBoundingBox($latitude, $longitude);

        // Step 5: Query to Find Canceled Orders Within 3KM
        writeLog("Fetching canceled orders for user $user_id", "INFO");

        // Tried with haversine formula but its take more time
            // $query = "
            //     SELECT o.id, o.original_price, o.discounted_price, o.latitude, o.longitude, r.name AS restaurant_name 
            //     FROM orders o
            //     INNER JOIN restaurants r ON o.restaurant_id = r.id
            //     WHERE o.status = 'canceled'
            //     AND (6371 * acos( cos(radians(?)) * cos(radians(o.latitude)) * cos(radians(o.longitude) - radians(?)) + sin(radians(?)) * sin(radians(o.latitude)) )) <= 3
            //     " . ($preference === "vegetarian" ? 
            //         "AND NOT EXISTS (SELECT 1 FROM order_items WHERE order_id = o.id AND category = 'non-veg')" 
            //         : "") . 
            //     " AND NOT EXISTS (SELECT 1 FROM order_items WHERE order_id = o.id AND category = 'sensitive') 
            //     ORDER BY o.id ASC
            //     LIMIT ?, ?";

        $query = "
            SELECT o.id AS order_id, o.original_price, o.discounted_price, 
                o.latitude AS order_lat, o.longitude AS order_lng, r.name AS restaurant_name
            FROM orders o
            INNER JOIN restaurants r ON o.restaurant_id = r.id
            WHERE o.status = 'canceled'
            AND o.latitude BETWEEN ? AND ?
            AND o.longitude BETWEEN ? AND ?";

        if ($preference === "vegetarian") {
            $query .= " AND NOT EXISTS (SELECT 1 FROM order_items WHERE order_id = o.id AND category = 'non-veg')";
        }
        $query .= " AND NOT EXISTS (SELECT 1 FROM order_items WHERE order_id = o.id AND category = 'sensitive')";
        $query .= " ORDER BY o.id ASC LIMIT ?, ?"; 

        $params = [$box['minLat'], $box['maxLat'], $box['minLng'], $box['maxLng'], $offset, $limit];

        // Start Database Transaction
        $db->startTransaction();
        $orders = $db->rawQuery($query, $params);

        // Step 6: Fetch Order Items in Batch (Avoid N+1 Queries)
        if (!empty($orders)) {
            $orderIds = array_column($orders, 'order_id');
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
                $order['items'] = $orderItems[$order['order_id']] ?? [];
            }
        }

        // Step 7: Get Total Count for Pagination
        $total_orders = $db->getValue("orders o", "count(*)");
        $total_pages = ceil($total_orders / $limit);

        $response = [
            "orders" => $orders, 
            "pagination" => [
                "current_page" => $page, 
                "total_pages" => $total_pages, 
                "total_orders" => $total_orders
            ]
        ];

        // Store in Redis for Caching (5 Min Expiry)
        $redis->setex($cache_key, 300, json_encode($response));
        writeLog("Cached orders for user $user_id, page $page", "INFO");

        // Commit Transaction
        $db->commit();

        $canceled_orders = $response;
    }

    if(!empty($canceled_orders)) { 
        $response_array = [
            'success' => true, 
            'message' => 'Cancel order list found.', 
            'data' => $canceled_orders
        ];
        $status_code = 200;

    } else {

        $response_array = [
            'success' => false, 
            'message' => 'Cancel order list not found.', 
            'data' => $canceled_orders
        ];
        $status_code = 404;
    }

    // Step 8: Return JSON Response
    writeLog("Returning canceled orders for user $user_id", "INFO");
    jsonEncodeResponse($response_array, $status_code);

} catch (Exception $e) {
    // Rollback Transaction on Error
    $db->rollback();

    // Log Error and Return JSON Response
    logError("Error: " . $e->getMessage(), "ERROR");
    jsonEncodeResponse(["success" => false, "message" => $e->getMessage()] , 500);
}
