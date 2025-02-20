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
        jsonEncodeResponse(['success'=>false,'message'=> "Invalid input: Password cannot be empty."], 400);

    }

    // Validate Input
    if (empty($name) || empty($email) || empty($hashedPassword)) {
        logError("Invalid input: name: $name, email: $email", "ERROR");
        jsonEncodeResponse(['success'=>false,'message'=> "Invalid input parameters."], 400);
    }

    // Verify existing user
    $db->where('email', $email);
    $user = $db->getOne('users', ['id', 'email']);
    if ($user) {
        logError("User already exists: email $email", "ERROR");
        jsonEncodeResponse(['success'=>false,'message'=> "User already exists."], 400);
    }

    // Start Transaction
    $db->startTransaction();

    // Insert new user
    $resp = $db->insert('users', ['name' => $name, 'email' => $email, 'password' => $hashedPassword]);

    if ($resp) {
        // Commit transaction
        $db->commit();
        jsonEncodeResponse(['success'=>true,'message'=> "User registered successfully."], 200);
    } else {
        jsonEncodeResponse(['success'=>false,'message'=> "User registration failed."], 400);
    }

} catch (Exception $e) {
    // Rollback Transaction on Error
    $db->rollback();

    // Log Error and Return JSON Response
    logError("Error: " . $e->getMessage(), "ERROR");
    jsonEncodeResponse(["success" => false, "message" => $e->getMessage()] , 500);
}
