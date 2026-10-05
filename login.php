<?php
require_once 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
$password = $data['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Please enter a valid email and password."]);
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

    // Verify password using Sodium Argon2id
    if (sodium_crypto_pwhash_str_verify($user['password_hash'], $password)) {
        // Start session or return success
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