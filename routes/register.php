<?php

try {

    // Read JSON Input
    $data = json_decode(file_get_contents('php://input'), true);
    $name = sanitizeInput($data['name'] ?? '');
    $email = sanitizeInput($data['email'] ?? '');
    $password = sanitizeInput($data['password'] ?? '');
    
    // Hash the password
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    } else {
        throw new Exception("Invalid input: Password cannot be empty");
    }

    // Validate Input
    if (empty($name) || empty($email) || empty($hashedPassword)) {
        logError("Invalid input: name: $name, email: $email", "ERROR");
        throw new Exception("Invalid input parameters");
    }

    // Verify existing user
    $db->where('email', $email);
    $user = $db->getOne('users', ['id', 'email']);
    if ($user) {
        logError("User already exists: email $email", "ERROR");
        throw new Exception("User already exists.");
    }

    // Start Transaction
    $db->startTransaction();

    // Insert new user
    $resp = $db->insert('users', ['name' => $name, 'email' => $email, 'password' => $hashedPassword]);

    if ($resp) {
        // Commit transaction
        $db->commit();
        echo json_encode(['success' => true, 'message' => 'User registered successfully']);
    } else {
        throw new Exception("User registration failed");
    }

} catch (Exception $e) {
    // Rollback Transaction on Error
    $db->rollback();

    // Log Error and Return JSON Response
    logError("Error: " . $e->getMessage(), "ERROR");
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
