<?php
/**
 * Payment Gateway Configuration
 * 
 * Supported Payment Methods:
 * 1. Google Pay (UPI Intent)
 * 2. Card Payments (via Cashfree)
 * 3. Cash Payment (COD)
 * 
 * To configure:
 * - Google Pay: Set your UPI ID (e.g., yourmerchant@paytm)
 * - Cashfree (Cards): Set App ID and Secret Key
 */
return [
    'default_gateway' => 'cashfree',
    
    // Google Pay UPI Configuration
    'gpay_upi_id' => getenv('GPAY_UPI_ID') ?: 'petromine@paytm', // Your business UPI ID
    'gpay_payee_name' => getenv('GPAY_PAYEE_NAME') ?: 'PetroMine', // Business name
    
    // Cashfree Configuration (for Card Payments only)
    'cashfree' => [
        // Get these from https://www.cashfree.com/developers/dashboard
        'app_id' => getenv('CASHFREE_APP_ID') ?: 'YOUR_APP_ID_HERE',
        'secret_key' => getenv('CASHFREE_SECRET_KEY') ?: 'YOUR_SECRET_KEY_HERE',
        'env' => getenv('CASHFREE_ENV') ?: 'sandbox', // sandbox or production
        'currency' => 'INR',
        'notify_url' => getenv('CASHFREE_NOTIFY_URL') ?: '',
        'return_url' => getenv('CASHFREE_RETURN_URL') ?: '',
    ],
];

