<?php
require_once '../includes/session.php';
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

// Check if user is admin - admins cannot access fuel purchase stats
if (hasRole('admin')) {
    echo json_encode(['error' => 'Admin users are not allowed to access fuel purchase statistics']);
    exit();
}

try {
    $user_id = getCurrentUser()['id'];
    $user = getCurrentUser();

    // Pump owner stats
    if ($user['role'] === 'pump_owner') {
        $totalStationsStmt = $db->prepare("SELECT COUNT(*) FROM fuel_stations WHERE owner_id = :uid");
        $totalStationsStmt->execute([':uid' => $user_id]);
        $total_stations = $totalStationsStmt->fetchColumn();

        $todaysSalesStmt = $db->prepare("
            SELECT COALESCE(SUM(stb.total_amount), 0)
            FROM save_to_buy stb
            JOIN fuel_stations fs ON stb.station_id = fs.id
            WHERE fs.owner_id = :uid AND DATE(stb.created_at) = CURDATE()
        ");
        $todaysSalesStmt->execute([':uid' => $user_id]);
        $todays_sales = $todaysSalesStmt->fetchColumn();

        $activeLocksStmt = $db->prepare("
            SELECT COUNT(*) FROM save_to_buy stb
            JOIN fuel_stations fs ON stb.station_id = fs.id
            WHERE fs.owner_id = :uid AND stb.status = 'active' AND stb.expiry_date > NOW()
        ");
        $activeLocksStmt->execute([':uid' => $user_id]);
        $active_locks = $activeLocksStmt->fetchColumn();

        echo json_encode([
            'total_stations' => (int)$total_stations,
            'todays_sales' => number_format($todays_sales, 2),
            'active_locks' => (int)$active_locks
        ]);
        exit();
    }

try {
    $user_id = getCurrentUser()['id'];
    
    // Get total savings (difference between locked price and current price for redeemed purchases)
    $savingsQuery = "
        SELECT COALESCE(SUM(
            (fp.price - stb.locked_price) * stb.quantity
        ), 0) as total_savings
        FROM save_to_buy stb
        JOIN fuel_prices fp ON stb.station_id = fp.station_id AND stb.fuel_type = fp.fuel_type
        WHERE stb.user_id = :user_id 
        AND stb.status = 'redeemed'
        AND fp.created_at >= stb.redeemed_at
        ORDER BY fp.created_at DESC
    ";
    
    $stmt = $db->prepare($savingsQuery);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $total_savings = $stmt->fetchColumn() ?: 0;
    
    // Get active locks count
    $activeQuery = "
        SELECT COUNT(*) as active_locks
        FROM save_to_buy 
        WHERE user_id = :user_id 
        AND status = 'active' 
        AND expiry_date > NOW()
    ";
    
    $stmt = $db->prepare($activeQuery);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $active_locks = $stmt->fetchColumn() ?: 0;
    
    // Get total purchases count
    $purchasesQuery = "
        SELECT COUNT(*) as total_purchases
        FROM save_to_buy 
        WHERE user_id = :user_id
    ";
    
    $stmt = $db->prepare($purchasesQuery);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $total_purchases = $stmt->fetchColumn() ?: 0;
    
    echo json_encode([
        'total_savings' => number_format($total_savings, 2),
        'active_locks' => $active_locks,
        'total_purchases' => $total_purchases
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to fetch stats']);
}
?>