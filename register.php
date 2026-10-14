<?php
require_once 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

// 1. Handle Google OAuth Registration/Signup
if (isset($data['credential'])) {
    $id_token = $data['credential'];
    
    // Verify Google ID token securely via Google's endpoint
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
        // Check if account already exists
        $stmt = $pdo->prepare("SELECT id, email, google_id FROM users WHERE email = :email OR google_id = :google_id");
        $stmt->execute([':email' => $email, ':google_id' => $google_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Link google_id if missing on existing email account
            if (empty($user['google_id']) && !empty($google_id)) {
                $updateStmt = $pdo->prepare("UPDATE users SET google_id = :google_id WHERE id = :id");
                $updateStmt->execute([':google_id' => $google_id, ':id' => $user['id']]);
            }
            $userId = $user['id'];
        } else {
            // Register new user via Google with a null password
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
            "message" => "Google account registered successfully!"
        ]);
        exit;
        
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Google registration failed: " . $e->getMessage()]);
        exit;
    }
}

// 2. Standard Email/Password Registration Logic
$name = trim($data['name'] ?? $data['full_name'] ?? '');
$email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
$password = $data['password'] ?? '';

if (empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Please fill in all fields with a valid email and password."]);
    exit;
}

if (!preg_match('/^[A-Za-z\s\'-]+$/u', $name)) {
    echo json_encode(["status" => "error", "message" => "Name can only contain letters and spaces."]);
    exit;
}

// Server-side password validation using your exact regex pattern
$password_pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
if (!preg_match($password_pattern, $password)) {
    echo json_encode([
        "status" => "error", 
        "message" => "Password must be at least 8 characters and include an uppercase letter, a lowercase letter, a number, and a special character."
    ]);
    exit;
}

try {
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        echo json_encode(["status" => "error", "message" => "An account with this email already exists."]);
        exit;
    }

    // Hash password using PHP's native sodium argon2id algorithm
    $passwordHash = sodium_crypto_pwhash_str(
        $password,
        SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
        SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE ?? SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
    );

    // Insert user into database using the actual schema column name
    $insert = $pdo->prepare("INSERT INTO users (full_name, email, password_hash) VALUES (:full_name, :email, :password_hash)");
    $result = $insert->execute([
        ':full_name' => $name,
        ':email' => $email,
        ':password_hash' => $passwordHash
    ]);

    if (!$result || $insert->rowCount() !== 1) {
        echo json_encode(["status" => "error", "message" => "Registration failed: user could not be saved to the database."]);
        exit;
    }

    echo json_encode([
        "status" => "success",
        "message" => "Account registered successfully! Please sign in."
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Registration failed: " . $e->getMessage()]);
}