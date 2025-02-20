<?php
require '../vendor/autoload.php'; // Firebase JWT
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Generate JWT Token
function generateJWT($userId, $email, $config) {
    $issuedAt = time();
    $expirationTime = $issuedAt + $config['token_config']['token_expiry'];

    $payload = [
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'sub' => $userId,
        'email' => $email
    ];

    return JWT::encode($payload, $config['token_config']['secret_key'], 'HS256');
}

// Validate JWT Token
function validateJWT($jwtToken, $config) {
    try {
        return (array) JWT::decode($jwtToken, new Key($config['token_config']['secret_key'], 'HS256'));
    } catch (Exception $e) {
        return false; // Invalid token
    }
}

// Generate and Store Refresh Token
function generateRefreshToken($user_id, $db, $config) {
    $refresh_token = bin2hex(random_bytes(32)); // Secure token
    $expires_at = time() + $config['token_config']['token_expiry'];

    // Store token in database
    $db->insert('refresh_tokens', ['user_id' => $user_id, 'token' => $refresh_token, 'expires_at' => date('Y-m-d H:i:s', $expires_at)]);

    return $refresh_token;
}

// Validate Refresh Token
function validateRefreshToken($refresh_token, $db) {
    $db->where('token', $refresh_token);
    $db->where('expires_at', date('Y-m-d'));
    $token = $db->getOne('refresh_tokens', ['user_id']);
    return $token;
}

// Remove Used Refresh Token
function deleteRefreshToken($refresh_token, $db) {
    $db->where('token', $refresh_token);
    $db->delete('refresh_tokens');
}


function verifyAuth($config)
{
    header('Content-Type: application/json');

    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';

    if ($authHeader) {
        list($tokenType, $jwtToken) = explode(' ', $authHeader, 2);

        if ($tokenType === 'Bearer' && $jwtToken) {
            $decodedData = validateJWT($jwtToken, $config);

            if ($decodedData) {
                return true;
            }
        }
    }

    logError("Unauthorized access: API cancel_order, WARNING");
    exit(json_encode(["error" => "Unauthorized API access"]));
}
