<?php

try {
    // Read JSON Input
    $data = json_decode(file_get_contents('php://input'), true);
    $email = sanitizeInput($data['email'] ?? '');
    $password = sanitizeInput($data['password'] ?? '');

    // Validate Input
    if (empty($email) || empty($password)) {
        logError("Invalid input: email: $email", "ERROR");
        throw new Exception("Invalid input parameters");
    }

    // Verify user credentials
    $db->where('email', $email);
    $user = $db->getOne('users', ['id', 'password']);

    if (!$user) {
        throw new Exception("Invalid credentials");
    }

    if (!password_verify($password, $user['password'])) {
        throw new Exception("Invalid credentials");
    }

    // Generate Tokens
    $token = generateJWT($user['id'], $email, $config);
    $refreshToken = generateRefreshToken($user['id'], $db, $config);

    // Return JSON Response
    echo json_encode(['success' => true, 'token' => $token, 'refresh_token' => $refreshToken]);

} catch (Exception $e) {
    // Log Error and Return JSON Response
    logError("Login Error: " . $e->getMessage(), "ERROR");
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
