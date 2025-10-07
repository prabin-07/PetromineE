<?php
require_once '../includes/session.php';
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

try {
    $user_id = getCurrentUser()['id'];
    
    $query = "
        SELECT 
            stb.id,
            stb.fuel_type,
            stb.locked_price,
            stb.quantity,
            stb.total_amount,
            stb.expiry_date,
            stb.status,
            stb.created_at,
            stb.redeemed_at,
            fs.name as station_name,
            fs.address as station_address
        FROM save_to_buy stb
        JOIN fuel_stations fs ON stb.station_id = fs.id
        WHERE stb.user_id = :user_id
        ORDER BY stb.created_at DESC
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Update expired purchases
    $updateQuery = "
        UPDATE save_to_buy 
        SET status = 'expired' 
        WHERE user_id = :user_id 
        AND status = 'active' 
        AND expiry_date < NOW()
    ";
    
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bindParam(':user_id', $user_id);
    $updateStmt->execute();
    
    // Update status for expired purchases in the result
    foreach ($purchases as &$purchase) {
        if ($purchase['status'] === 'active' && strtotime($purchase['expiry_date']) < time()) {
            $purchase['status'] = 'expired';
        }
    }
    
    echo json_encode($purchases);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to fetch purchases']);
}
?>