<?php
/**
 * Payment Success Callback
 * Handles return from payment gateway (Card payments)
 */
require_once '../includes/session.php';
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: text/html; charset=UTF-8');

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$orderId = isset($_GET['order_id']) ? trim($_GET['order_id']) : '';
$status = isset($_GET['status']) ? strtolower(trim($_GET['status'])) : '';

if (empty($orderId)) {
    header('Location: ../dashboard.php?error=invalid_order');
    exit();
}

try {
    $userId = getCurrentUser()['id'];
    
    // Get payment record
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
        header('Location: ../dashboard.php?error=payment_not_found');
        exit();
    }

    $purchaseId = (int)($payment['save_to_buy_id'] ?? 0);

    // If already successful, redirect
    if ($payment['status'] === 'success') {
        header('Location: ../dashboard.php?payment=success&order_id=' . urlencode($orderId));
        exit();
    }

    // For card payments, verify with gateway
    if ($payment['payment_method'] === 'card' && $payment['gateway'] === 'cashfree') {
        $paymentConfig = require '../config/payment.php';
        $cashfreeStatus = fetchCashfreeOrderStatus($paymentConfig['cashfree'], $orderId);

        if ($cashfreeStatus['success']) {
            $orderStatus = strtoupper($cashfreeStatus['order_status']);
            
            if ($orderStatus === 'PAID' || $orderStatus === 'COMPLETED') {
                // Update payment record
                $updatePayment = "
                    UPDATE payments
                    SET status = 'success', payment_reference = :reference_id
                    WHERE order_id = :order_id
                ";
                $updateStmt = $db->prepare($updatePayment);
                $referenceId = $cashfreeStatus['reference_id'] ?? null;
                $updateStmt->bindParam(':reference_id', $referenceId);
                $updateStmt->bindParam(':order_id', $orderId);
                $updateStmt->execute();

                // Mark purchase as redeemed
                if ($purchaseId > 0) {
                    $redeemQuery = "
                        UPDATE save_to_buy
                        SET status = 'redeemed', redeemed_at = NOW()
                        WHERE id = :purchase_id AND user_id = :user_id
                    ";
                    $redeemStmt = $db->prepare($redeemQuery);
                    $redeemStmt->bindParam(':purchase_id', $purchaseId, PDO::PARAM_INT);
                    $redeemStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                    $redeemStmt->execute();
                }

                header('Location: ../dashboard.php?payment=success&order_id=' . urlencode($orderId));
                exit();
            } elseif ($orderStatus === 'FAILED' || $orderStatus === 'CANCELLED') {
                $updatePayment = "UPDATE payments SET status = 'failed' WHERE order_id = :order_id";
                $failStmt = $db->prepare($updatePayment);
                $failStmt->bindParam(':order_id', $orderId);
                $failStmt->execute();

                header('Location: ../dashboard.php?payment=failed&order_id=' . urlencode($orderId));
                exit();
            }
        }
    }

    // Pending or unknown status
    header('Location: ../dashboard.php?payment=pending&order_id=' . urlencode($orderId));
    exit();

} catch (Exception $e) {
    header('Location: ../dashboard.php?error=payment_error');
    exit();
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
    if (!empty($decoded['payments']) && is_array($decoded['payments'])) {
        $latestPayment = end($decoded['payments']);
        $referenceId = $latestPayment['cf_payment_id'] ?? ($latestPayment['payment_arn'] ?? null);
    }

    return [
        'success' => true,
        'order_status' => $decoded['order_status'],
        'reference_id' => $referenceId
    ];
}

