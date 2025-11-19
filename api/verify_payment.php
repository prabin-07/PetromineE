<?php
/**
 * Verify Payment Endpoint
 * Only verifies Card payments (Cashfree)
 * Google Pay and Cash payments are handled differently
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

try {
    $payload = json_decode(file_get_contents('php://input'), true);
    if (!$payload) {
        $payload = $_POST;
    }

    $orderId = isset($payload['order_id']) ? trim($payload['order_id']) : '';
    $purchaseId = isset($payload['purchase_id']) ? (int)$payload['purchase_id'] : 0;

    if ($orderId === '' || $purchaseId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid verification request.']);
        exit();
    }

    $userId = getCurrentUser()['id'];

    $paymentQuery = "
        SELECT * FROM payments
        WHERE order_id = :order_id AND user_id = :user_id
        LIMIT 1
    ";
    $paymentStmt = $db->prepare($paymentQuery);
    $paymentStmt->bindParam(':order_id', $orderId);
    $paymentStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $paymentStmt->execute();
    $payment = $paymentStmt->fetch(PDO::FETCH_ASSOC);

    if (!$payment) {
        echo json_encode(['success' => false, 'message' => 'Payment record not found.']);
        exit();
    }

    // Check both column names for compatibility
    $relatedPurchaseId = (int)($payment['save_to_buy_id'] ?? $payment['related_purchase_id'] ?? 0);
    if ($relatedPurchaseId !== $purchaseId) {
        echo json_encode(['success' => false, 'message' => 'Payment does not match the selected purchase.']);
        exit();
    }

    // Cash payments are already confirmed, no verification needed
    if ($payment['payment_method'] === 'cash') {
        echo json_encode([
            'success' => true,
            'status' => $payment['status'],
            'message' => 'Cash payment does not require verification.'
        ]);
        exit();
    }

    // Google Pay UPI: User confirms manually, we don't auto-verify
    if ($payment['payment_method'] === 'gpay') {
        echo json_encode([
            'success' => false,
            'status' => 'pending',
            'message' => 'Google Pay payments require manual confirmation. Please confirm payment completion.'
        ]);
        exit();
    }

    // Card payments: Verify with gateway
    if ($payment['status'] === 'success') {
        echo json_encode(['success' => true, 'status' => 'success', 'message' => 'Payment already verified.']);
        exit();
    }

    if ($payment['payment_method'] !== 'card' || $payment['gateway'] !== 'cashfree') {
        echo json_encode(['success' => false, 'message' => 'Only card payments can be auto-verified.']);
        exit();
    }

    $cashfreeStatus = fetchCashfreeOrderStatus($paymentConfig['cashfree'], $orderId);

    if (!$cashfreeStatus['success']) {
        echo json_encode($cashfreeStatus);
        exit();
    }

    $orderStatus = strtoupper($cashfreeStatus['order_status']);
    $referenceId = $cashfreeStatus['reference_id'] ?? null;
    $paymentMethod = $cashfreeStatus['payment_method'] ?? $payment['payment_method'];

    if ($orderStatus === 'PAID' || $orderStatus === 'COMPLETED') {
        // Update payment record
        $updatePayment = "
            UPDATE payments
            SET status = 'success', payment_reference = :reference_id, payment_method = :payment_method
            WHERE order_id = :order_id
        ";
        $updateStmt = $db->prepare($updatePayment);
        $updateStmt->bindParam(':reference_id', $referenceId);
        $updateStmt->bindParam(':payment_method', $paymentMethod);
        $updateStmt->bindParam(':order_id', $orderId);
        $updateStmt->execute();

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
            'status' => 'success',
            'message' => 'Payment verified successfully.',
            'reference_id' => $referenceId
        ]);
        exit();
    }

    if ($orderStatus === 'FAILED' || $orderStatus === 'CANCELLED') {
        $updatePayment = "
            UPDATE payments
            SET status = 'failed'
            WHERE order_id = :order_id
        ";
        $failStmt = $db->prepare($updatePayment);
        $failStmt->bindParam(':order_id', $orderId);
        $failStmt->execute();

        echo json_encode([
            'success' => false,
            'status' => 'failed',
            'message' => 'Payment failed. Please try again.'
        ]);
        exit();
    }

    echo json_encode([
        'success' => false,
        'status' => 'pending',
        'message' => 'Payment is still pending. Please wait and retry verification.'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to verify payment: ' . $e->getMessage()]);
}

function fetchCashfreeOrderStatus(array $config, string $orderId): array
{
    $baseUrl = $config['env'] === 'production'
        ? 'https://api.cashfree.com/pg/orders/'
        : 'https://sandbox.cashfree.com/pg/orders/';

    $url = $baseUrl . urlencode($orderId);

    $headers = [
        'x-client-id: ' . $config['app_id'],
        'x-client-secret: ' . $config['secret_key'],
        'x-api-version: 2022-09-01'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    // SSL certificate handling for local development
    // TODO: In production, remove these lines or set to true for security
    if ($config['env'] === 'sandbox') {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['success' => false, 'message' => 'Cashfree API error: ' . $error];
    }

    $decoded = json_decode($response, true);

    if (!isset($decoded['order_status'])) {
        return ['success' => false, 'message' => 'Unable to fetch order status.'];
    }

    $referenceId = null;
    $paymentMethod = null;
    if (!empty($decoded['payments']) && is_array($decoded['payments'])) {
        $latestPayment = end($decoded['payments']);
        $referenceId = $latestPayment['cf_payment_id'] ?? ($latestPayment['payment_arn'] ?? null);
        $paymentMethod = $latestPayment['payment_method'] ?? null;
    }

    return [
        'success' => true,
        'order_status' => $decoded['order_status'],
        'reference_id' => $referenceId,
        'payment_method' => $paymentMethod
    ];
}

