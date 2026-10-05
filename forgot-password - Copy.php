<?php
require_once 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Please enter a valid email address."]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $update = $pdo->prepare("UPDATE users SET reset_token = :token, reset_expires_at = :expires WHERE id = :id");
        $update->execute([
            ':token' => $token,
            ':expires' => $expires,
            ':id' => $user['id']
        ]);

        // In production, trigger email delivery containing reset-password.php?token=$token
    }

    echo json_encode([
        "status" => "success", 
        "message" => "If an account exists with this email, password reset instructions have been sent."
    ]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Request failed: " . $e->getMessage()]);
}
?>