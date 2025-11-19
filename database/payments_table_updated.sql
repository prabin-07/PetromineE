-- Updated Payments Table Schema
-- Supports: Google Pay (UPI), Card Payments, Cash Payments
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    save_to_buy_id INT NULL,
    order_id VARCHAR(255) NOT NULL,
    gateway VARCHAR(100) NOT NULL,
    payment_method VARCHAR(100) DEFAULT 'gpay',
    payment_reference VARCHAR(255) NULL,
    reference VARCHAR(255) NULL,
    amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'created',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (save_to_buy_id) REFERENCES save_to_buy(id) ON DELETE SET NULL,
    UNIQUE KEY idx_payments_order (order_id)
);

-- If table already exists, add missing columns
ALTER TABLE payments 
ADD COLUMN IF NOT EXISTS reference VARCHAR(255) NULL AFTER payment_reference,
ADD COLUMN IF NOT EXISTS save_to_buy_id INT NULL AFTER user_id;

