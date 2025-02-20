<?php

try {
    // Read JSON Input
    $data = json_decode(file_get_contents('php://input'), true);
    $email = sanitizeInput($data['email'] ?? '');
    $password = sanitizeInput($data['password'] ?? '');

    // Validate Input
    if (empty($email) || empty($password)) {
        logError("Invalid input: email: $email", "ERROR");
        jsonEncodeResponse(['success'=>false,'message'=> "Invalid input parameters."], 400);
    }

    // Verify user credentials
    $db->where('email', $email);
    $user = $db->getOne('users', ['id', 'password']);

    if (!$user) {
        jsonEncodeResponse(['success'=>false,'message'=> "Invalid credentials."], 400);
    }

    if (!password_verify($password, $user['password'])) {
        jsonEncodeResponse(['success'=>false,'message'=> "Invalid credentials."], 400);
    }

    // Generate Tokens
    $token = generateJWT($user['id'], $email, $config);
    $refreshToken = generateRefreshToken($user['id'], $db, $config);

    // Return JSON Response
    jsonEncodeResponse(['success'=>true,'message'=> "User logged in successfully.", 'data' => ['token' => $token, 'refresh_token' => $refreshToken]], 200);

} catch (Exception $e) {
    // Log Error and Return JSON Response
    logError("Login Error: " . $e->getMessage(), "ERROR");
    jsonEncodeResponse(["success" => false, "message" => $e->getMessage()] , 500);
}
