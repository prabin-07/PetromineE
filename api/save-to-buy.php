<?php
require_once '../includes/session.php';
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

try {
    $user_id = getCurrentUser()['id'];
    $station_id = (int)$_POST['station_id'];
    $fuel_type = $_POST['fuel_type'];
    $quantity = (float)$_POST['quantity'];
    $price = (float)$_POST['price'];
    
    if (!$station_id || !$fuel_type || !$quantity || !$price) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }
    
    if ($quantity <= 0 || $quantity > 100) {
        echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
        exit();
    }
    
    // Verify the price is current
    $priceQuery = "SELECT price FROM fuel_prices WHERE station_id = :station_id AND fuel_type = :fuel_type ORDER BY created_at DESC LIMIT 1";
    $priceStmt = $db->prepare($priceQuery);
    $priceStmt->bindParam(':station_id', $station_id);
    $priceStmt->bindParam(':fuel_type', $fuel_type);
    $priceStmt->execute();
    $currentPrice = $priceStmt->fetchColumn();
    
    if (!$currentPrice || abs($currentPrice - $price) > 0.01) {
        echo json_encode(['success' => false, 'message' => 'Price has changed. Please refresh and try again.']);
        exit();
    }
    
    $total_amount = $quantity * $price;
    $expiry_date = date('Y-m-d H:i:s', strtotime('+7 days'));
    
    $query = "
        INSERT INTO save_to_buy (user_id, station_id, fuel_type, locked_price, quantity, total_amount, expiry_date) 
        VALUES (:user_id, :station_id, :fuel_type, :locked_price, :quantity, :total_amount, :expiry_date)
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':station_id', $station_id);
    $stmt->bindParam(':fuel_type', $fuel_type);
    $stmt->bindParam(':locked_price', $price);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':total_amount', $total_amount);
    $stmt->bindParam(':expiry_date', $expiry_date);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Price locked successfully!',
            'purchase_id' => $db->lastInsertId()
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save purchase']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>