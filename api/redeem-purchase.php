<?php
require_once '../includes/session.php';
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

// Check if user is admin - admins cannot redeem fuel purchases
if (hasRole('admin')) {
    echo json_encode(['success' => false, 'message' => 'Admin users are not allowed to redeem fuel purchases']);
    exit();
}

http_response_code(410);
echo json_encode([
    'success' => false,
    'message' => 'Direct redemption is disabled. Use the Redeem Now payment flow to complete your purchase.'
]);
?>