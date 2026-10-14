<?php
require_once 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

// 1. Handle Google OAuth Token Verification
if (isset($data['credential'])) {
    $id_token = $data['credential'];
    
    // Verify Google ID token using Google's secure tokeninfo endpoint
    $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($id_token);
    $response = @file_get_contents($url);
    
    if ($response === FALSE) {
        echo json_encode(["status" => "error", "message" => "Failed to verify Google token."]);
        exit;
    }
    
    $payload = json_decode($response, true);
    
    if (!isset($payload['email']) || empty($payload['email'])) {
        echo json_encode(["status" => "error", "message" => "Invalid Google account information."]);
        exit;
    }
    
    $email = filter_var($payload['email'], FILTER_SANITIZE_EMAIL);
    $google_id = $payload['sub'] ?? '';
    $full_name = $payload['name'] ?? 'Google User';
    
    try {
        // Check if user exists by email or google_id
        $stmt = $pdo->prepare("SELECT id, email, password_hash, google_id FROM users WHERE email = :email OR google_id = :google_id");
        $stmt->execute([':email' => $email, ':google_id' => $google_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Link google_id if missing on an existing email account
            if (empty($user['google_id']) && !empty($google_id)) {
                $updateStmt = $pdo->prepare("UPDATE users SET google_id = :google_id WHERE id = :id");
                $updateStmt->execute([':google_id' => $google_id, ':id' => $user['id']]);
            }
            $userId = $user['id'];
        } else {
            // Automatically register new user via Google (password_hash is NULL)
            $insertStmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, google_id) VALUES (:full_name, :email, NULL, :google_id)");
            $insertStmt->execute([
                ':full_name' => $full_name,
                ':email' => $email,
                ':google_id' => $google_id
            ]);
            $userId = $pdo->lastInsertId();
        }
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $userId;
        $_SESSION['email'] = $email;
        
        echo json_encode([
            "status" => "success",
            "message" => "Google login successful!"
        ]);
        exit;
        
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Google authentication database error: " . $e->getMessage()]);
        exit;
    }
}

// 2. Standard Email/Password Login Logic
$email = filter_var((isset($data['email']) ? $data['email'] : ''), FILTER_SANITIZE_EMAIL);
$password = isset($data['password']) ? $data['password'] : '';

// Server-side validation for login inputs
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Please enter a valid email address."]);
    exit;
}

if (empty($password)) {
    echo json_encode(["status" => "error", "message" => "Please enter your password."]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, email, password_hash FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(["status" => "error", "message" => "You must have an account to login."]);
        exit;
    }
    
    // Check if account was created via Google and has no password set
    if (empty($user['password_hash'])) {
        echo json_encode(["status" => "error", "message" => "This account uses Google Sign-In. Please sign in with Google."]);
        exit;
    }

    // Verify password using Sodium Argon2id
    if (sodium_crypto_pwhash_str_verify($user['password_hash'], $password)) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];

        echo json_encode([
            "status" => "success",
            "message" => "Login successful!"
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
    }

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Login failed: " . $e->getMessage()]);
}