<?php
require_once 'db.php';
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access. Please log in."]);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!$payload) {
    echo json_encode(["status" => "error", "message" => "Invalid payload received."]);
    exit;
}

try {
    $invoiceNumber = trim((string)($payload['invoice']['number'] ?? ''));
    if ($invoiceNumber === '' || !preg_match('/^INV-/i', $invoiceNumber)) {
        $invoiceNumber = 'INV-' . date('Ymd') . '-' . substr(str_replace(['-', '+', '/'], '', base64_encode(random_bytes(6))), 0, 6);
    }
    $totalAmount = $payload['invoice']['total'] ?? 0;
    $status = $payload['meta']['status'] ?? 'Draft';
    $payloadJson = json_encode($payload);

    // Check if invoice number already exists for this user
    $check = $pdo->prepare("SELECT id FROM invoices WHERE user_id = :uid AND invoice_number = :inv_num");
    $check->execute([':uid' => $user_id, ':inv_num' => $invoiceNumber]);
    $existing = $check->fetch();

    if ($existing) {
        $stmt = $pdo->prepare("UPDATE invoices SET total_amount = :total, status = :status, payload_data = :payload, updated_at = NOW() WHERE id = :id");
        $stmt->execute([
            ':total' => $totalAmount,
            ':status' => $status,
            ':payload' => $payloadJson,
            ':id' => $existing['id']
        ]);
        echo json_encode(["status" => "success", "message" => "Invoice updated successfully!"]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO invoices (user_id, invoice_number, total_amount, status, payload_data, created_at) VALUES (:uid, :inv_num, :total, :status, :payload, NOW())");
        $stmt->execute([
            ':uid' => $user_id,
            ':inv_num' => $invoiceNumber,
            ':total' => $totalAmount,
            ':status' => $status,
            ':payload' => $payloadJson
        ]);
        echo json_encode(["status" => "success", "message" => "Invoice saved successfully!"]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Failed to save invoice: " . $e->getMessage()]);
}
?>