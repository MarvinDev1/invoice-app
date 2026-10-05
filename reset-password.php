<?php
require_once 'db.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if (empty($token)) {
    $error = "Invalid or missing password reset token.";
} else {
    // Check if token is valid and not expired
    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = :token AND reset_expires_at > NOW()");
    $stmt->execute([':token' => $token]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = "This password reset link is invalid or has expired.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $update = $pdo->prepare("UPDATE users SET password = :password, reset_token = NULL, reset_expires_at = NULL WHERE id = :id");
        $update->execute([
            ':password' => $hashed_password,
            ':id' => $user['id']
        ]);

        $success = "Your password has been successfully reset! You can now log in.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Invoice App</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="logo-area">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <span>Invoice App</span>
            </div>
            
            <div class="auth-form">
                <h3>Reset Password</h3>
                
                <?php if (!empty($error)): ?>
                    <div class="auth-message" style="color: var(--danger-color); margin-bottom: 16px;">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="auth-message" style="color: var(--success-color); margin-bottom: 16px;">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                    <div style="text-align: center; margin-top: 16px;">
                        <a href="index.php" class="btn btn-primary" style="text-decoration: none; display: inline-block;">Go to Login</a>
                    </div>
                <?php elseif (empty($error) || isset($user)): ?>
                    <form action="reset-password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" id="password" name="password" required placeholder="Enter new password">
                        </div>
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm new password">
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Update Password</button>
                    </form>
                <?php endif; ?>

                <?php if (empty($success)): ?>
                    <div class="auth-switch">
                        <a href="index.php"><i class="fa-solid fa-arrow-left"></i> Back to Login</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>