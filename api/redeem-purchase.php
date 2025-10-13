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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $purchase_id = (int)$input['purchase_id'];
    $user_id = getCurrentUser()['id'];
    
    if (!$purchase_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid purchase ID']);
        exit();
    }
    
    // Verify the purchase belongs to the user and is active
    $verifyQuery = "
        SELECT id, status, expiry_date 
        FROM save_to_buy 
        WHERE id = :purchase_id AND user_id = :user_id
    ";
    
    $stmt = $db->prepare($verifyQuery);
    $stmt->bindParam(':purchase_id', $purchase_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $purchase = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$purchase) {
        echo json_encode(['success' => false, 'message' => 'Purchase not found']);
        exit();
    }
    
    if ($purchase['status'] !== 'active') {
        echo json_encode(['success' => false, 'message' => 'Purchase is not active']);
        exit();
    }
    
    if (strtotime($purchase['expiry_date']) < time()) {
        echo json_encode(['success' => false, 'message' => 'Purchase has expired']);
        exit();
    }
    
    // Update the purchase status to redeemed
    $redeemQuery = "
        UPDATE save_to_buy 
        SET status = 'redeemed', redeemed_at = NOW() 
        WHERE id = :purchase_id
    ";
    
    $stmt = $db->prepare($redeemQuery);
    $stmt->bindParam(':purchase_id', $purchase_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Purchase redeemed successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to redeem purchase']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>