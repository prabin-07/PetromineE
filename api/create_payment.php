<?php
/**
 * Create Payment Endpoint
 * Supports: Google Pay (UPI Intent), Card Payments, Cash Payment (COD)
 */
require_once '../includes/session.php';
require_once '../config/database.php';
require_once '../includes/auth.php';

$paymentConfig = require '../config/payment.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Authentication required.']);
    exit();
}

if (hasRole('admin')) {
    echo json_encode(['success' => false, 'message' => 'Admin users cannot redeem purchases.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

try {
    $payload = json_decode(file_get_contents('php://input'), true);
    if (!$payload) {
        $payload = $_POST;
    }

    $purchaseId = isset($payload['purchase_id']) ? (int)$payload['purchase_id'] : 0;
    $paymentMethod = isset($payload['payment_method']) ? strtolower(trim($payload['payment_method'])) : '';

    // Only allow: gpay, card, cash
    $allowedMethods = ['gpay', 'card', 'cash'];
    if (!in_array($paymentMethod, $allowedMethods, true)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid payment method. Allowed: Google Pay, Card, or Cash.'
        ]);
        exit();
    }

    if ($purchaseId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid purchase selected.']);
        exit();
    }

    $user = getCurrentUser();
    $userId = $user['id'];

    // Fetch purchase information
    $purchaseQuery = "
        SELECT stb.*, fs.name AS station_name
        FROM save_to_buy stb
        JOIN fuel_stations fs ON stb.station_id = fs.id
        WHERE stb.id = :purchase_id AND stb.user_id = :user_id
        LIMIT 1
    ";

    $stmt = $db->prepare($purchaseQuery);
    $stmt->bindParam(':purchase_id', $purchaseId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $purchase = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$purchase) {
        echo json_encode(['success' => false, 'message' => 'Purchase not found.']);
        exit();
    }

    if ($purchase['status'] !== 'active') {
        echo json_encode(['success' => false, 'message' => 'Only active purchases can be redeemed.']);
        exit();
    }

    if (strtotime($purchase['expiry_date']) < time()) {
        echo json_encode(['success' => false, 'message' => 'This purchase has expired.']);
        exit();
    }

    $amount = (float)$purchase['total_amount'];
    if ($amount <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid amount for redemption.']);
        exit();
    }

    $orderId = sprintf('PMN-%s-%d', strtoupper(bin2hex(random_bytes(3))), $purchaseId);

    // Handle different payment methods
    if ($paymentMethod === 'cash') {
        // Cash Payment: Instant confirmation
        $paymentInsert = "
            INSERT INTO payments (user_id, order_id, gateway, payment_method, amount, status, save_to_buy_id, reference)
            VALUES (:user_id, :order_id, 'cash', 'cash', :amount, 'success', :purchase_id, :reference)
        ";

        $reference = 'CASH-' . time() . '-' . $purchaseId;
        $paymentStmt = $db->prepare($paymentInsert);
        $paymentStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $paymentStmt->bindParam(':order_id', $orderId);
        $paymentStmt->bindParam(':amount', $amount);
        $paymentStmt->bindParam(':purchase_id', $purchaseId, PDO::PARAM_INT);
        $paymentStmt->bindParam(':reference', $reference);
        $paymentStmt->execute();

        // Mark purchase as redeemed
        $redeemQuery = "
            UPDATE save_to_buy
            SET status = 'redeemed', redeemed_at = NOW()
            WHERE id = :purchase_id AND user_id = :user_id
        ";
        $redeemStmt = $db->prepare($redeemQuery);
        $redeemStmt->bindParam(':purchase_id', $purchaseId, PDO::PARAM_INT);
        $redeemStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $redeemStmt->execute();

        echo json_encode([
            'success' => true,
            'payment_method' => 'cash',
            'order_id' => $orderId,
            'status' => 'success',
            'message' => 'Cash payment confirmed. Your purchase will be ready for collection.',
            'purchase' => [
                'id' => $purchase['id'],
                'station_name' => $purchase['station_name'],
                'fuel_type' => $purchase['fuel_type'],
                'quantity' => $purchase['quantity'],
                'locked_price' => $purchase['locked_price'],
                'total_amount' => $purchase['total_amount']
            ]
        ]);
        exit();
    }

    // For Google Pay or Card: Create payment record
    $gateway = ($paymentMethod === 'card') ? 'cashfree' : 'gpay';
    
    $paymentInsert = "
        INSERT INTO payments (user_id, order_id, gateway, payment_method, amount, status, save_to_buy_id)
        VALUES (:user_id, :order_id, :gateway, :payment_method, :amount, 'pending', :purchase_id)
    ";

    $paymentStmt = $db->prepare($paymentInsert);
    $paymentStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $paymentStmt->bindParam(':order_id', $orderId);
    $paymentStmt->bindParam(':gateway', $gateway);
    $paymentStmt->bindParam(':payment_method', $paymentMethod);
    $paymentStmt->bindParam(':amount', $amount);
    $paymentStmt->bindParam(':purchase_id', $purchaseId, PDO::PARAM_INT);
    $paymentStmt->execute();

    if ($paymentMethod === 'gpay') {
        // Google Pay UPI Intent
        $upiId = $paymentConfig['gpay_upi_id'] ?? 'petromine@paytm'; // Default, should be configured
        $payeeName = $paymentConfig['gpay_payee_name'] ?? 'PetroMine';
        
        // Generate UPI Intent link
        $upiLink = generateUPIIntentLink($upiId, $payeeName, $amount, $orderId);
        
        echo json_encode([
            'success' => true,
            'payment_method' => 'gpay',
            'order_id' => $orderId,
            'upi_link' => $upiLink,
            'amount' => $amount,
            'message' => 'Opening Google Pay to complete payment...',
            'purchase' => [
                'id' => $purchase['id'],
                'station_name' => $purchase['station_name'],
                'fuel_type' => $purchase['fuel_type'],
                'quantity' => $purchase['quantity'],
                'locked_price' => $purchase['locked_price'],
                'total_amount' => $purchase['total_amount']
            ]
        ]);
        exit();
    }

    // Card Payment via Cashfree
    if ($paymentMethod === 'card') {
        $cashfreeConfig = $paymentConfig['cashfree'] ?? [];
        $placeholders = ['CF-APP-ID', 'YOUR_APP_ID_HERE', 'CF-SECRET-KEY', 'YOUR_SECRET_KEY_HERE'];
        if (empty($cashfreeConfig['app_id']) || in_array($cashfreeConfig['app_id'], $placeholders) ||
            empty($cashfreeConfig['secret_key']) || in_array($cashfreeConfig['secret_key'], $placeholders)) {
            echo json_encode([
                'success' => false,
                'message' => 'Cashfree credentials not configured for card payments. Please set your App ID and Secret Key in config/payment.php'
            ]);
            exit();
        }

        $cardResponse = createCashfreeCardOrder(
            $cashfreeConfig,
            $orderId,
            $amount,
            $user,
            $purchase
        );

        if (!$cardResponse['success']) {
            echo json_encode($cardResponse);
            exit();
        }

        // Update payments table with gateway reference
        $updatePayment = "
            UPDATE payments
            SET payment_reference = :payment_reference
            WHERE order_id = :order_id
        ";
        $updateStmt = $db->prepare($updatePayment);
        $updateStmt->bindParam(':payment_reference', $cardResponse['order_id']);
        $updateStmt->bindParam(':order_id', $orderId);
        $updateStmt->execute();

        echo json_encode([
            'success' => true,
            'payment_method' => 'card',
            'order_id' => $orderId,
            'payment_session_id' => $cardResponse['payment_session_id'],
            'cashfree_order_id' => $cardResponse['order_id'],
            'payment_link' => $cardResponse['payment_link'] ?? null,
            'message' => 'Card payment checkout initialized.',
            'purchase' => [
                'id' => $purchase['id'],
                'station_name' => $purchase['station_name'],
                'fuel_type' => $purchase['fuel_type'],
                'quantity' => $purchase['quantity'],
                'locked_price' => $purchase['locked_price'],
                'total_amount' => $purchase['total_amount']
            ]
        ]);
        exit();
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to initiate payment: ' . $e->getMessage()]);
}

/**
 * Generate Google Pay UPI Intent Link
 * Format: upi://pay?pa=<UPI_ID>&pn=<PAYEE_NAME>&am=<AMOUNT>&cu=INR&tn=<TRANSACTION_NOTE>
 */
function generateUPIIntentLink(string $upiId, string $payeeName, float $amount, string $orderId): string
{
    $params = [
        'pa' => urlencode($upiId),
        'pn' => urlencode($payeeName),
        'am' => number_format($amount, 2, '.', ''),
        'cu' => 'INR',
        'tn' => urlencode('PetroMine Order: ' . $orderId)
    ];
    
    return 'upi://pay?' . http_build_query($params);
}

/**
 * Create Cashfree order for Card Payments only
 */
function createCashfreeCardOrder(array $config, string $orderId, float $amount, array $user, array $purchase): array
{
    $baseUrl = $config['env'] === 'production'
        ? 'https://api.cashfree.com/pg/orders'
        : 'https://sandbox.cashfree.com/pg/orders';

    $returnUrl = $config['return_url'] ?: buildAbsoluteUrl('/api/payment_success.php?order_id={order_id}');

    $payload = [
        'order_id' => $orderId,
        'order_amount' => number_format($amount, 2, '.', ''),
        'order_currency' => $config['currency'] ?? 'INR',
        'order_meta' => [
            'return_url' => str_replace('{order_id}', $orderId, $returnUrl),
            'notify_url' => $config['notify_url'] ?: ''
        ],
        'customer_details' => [
            'customer_id' => 'user_' . $user['id'],
            'customer_email' => $user['email'] ?? 'customer@example.com',
            'customer_phone' => $_SESSION['phone'] ?? '9999999999'
        ],
        'order_tags' => [
            'purchase_id' => (string)$purchase['id'],
            'fuel_type' => $purchase['fuel_type'],
        ],
        // Only allow card payments
        'payment_methods' => ['card']
    ];

    $headers = [
        'Content-Type: application/json',
        'x-client-id: ' . $config['app_id'],
        'x-client-secret: ' . $config['secret_key'],
        'x-api-version: 2022-09-01'
    ];

    $ch = curl_init($baseUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($config['env'] === 'sandbox') {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['success' => false, 'message' => 'Cashfree API error: ' . $error];
    }

    $decoded = json_decode($response, true);

    if ($httpCode === 401 || $httpCode === 403) {
        $errorMsg = $decoded['message'] ?? $decoded['error'] ?? 'Authentication failed';
        return [
            'success' => false,
            'message' => 'Cashfree Authentication Failed: ' . $errorMsg
        ];
    }

    if (!isset($decoded['payment_session_id'])) {
        $errorMsg = $decoded['message'] ?? $decoded['error'] ?? 'Unable to create Cashfree order';
        if (isset($decoded['details']) && is_array($decoded['details'])) {
            $errorMsg .= ': ' . implode(', ', $decoded['details']);
        }
        return ['success' => false, 'message' => $errorMsg];
    }

    return [
        'success' => true,
        'order_id' => $decoded['order_id'],
        'payment_session_id' => $decoded['payment_session_id'],
        'payment_link' => $decoded['payment_link'] ?? null
    ];
}

function buildAbsoluteUrl(string $path): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return rtrim($scheme . '://' . $host, '/') . $path;
}
