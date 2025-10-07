-- Demo Users with Easy-to-Remember Credentials
-- All passwords are: "password123"
USE petromine;

-- Clear existing demo data first (optional)
-- DELETE FROM save_to_buy WHERE user_id > 1;
-- DELETE FROM users WHERE id > 1;

-- Insert demo users with simple credentials
INSERT INTO users (username, email, password, role) VALUES 
-- Demo Customers (password: password123)
('demo_customer', 'customer@demo.com', '$2y$10$8K1p/a0dChZnNPfOwt/My.Uy8lrjKBH1fRlmSxqrO8L.KlXg2Nu7W', 'customer'),
('alice_smith', 'alice@demo.com', '$2y$10$8K1p/a0dChZnNPfOwt/My.Uy8lrjKBH1fRlmSxqrO8L.KlXg2Nu7W', 'customer'),
('bob_jones', 'bob@demo.com', '$2y$10$8K1p/a0dChZnNPfOwt/My.Uy8lrjKBH1fRlmSxqrO8L.KlXg2Nu7W', 'customer'),
('carol_white', 'carol@demo.com', '$2y$10$8K1p/a0dChZnNPfOwt/My.Uy8lrjKBH1fRlmSxqrO8L.KlXg2Nu7W', 'customer'),

-- Demo Pump Owners (password: password123)
('demo_owner', 'owner@demo.com', '$2y$10$8K1p/a0dChZnNPfOwt/My.Uy8lrjKBH1fRlmSxqrO8L.KlXg2Nu7W', 'pump_owner'),
('station_manager', 'manager@demo.com', '$2y$10$8K1p/a0dChZnNPfOwt/My.Uy8lrjKBH1fRlmSxqrO8L.KlXg2Nu7W', 'pump_owner'),

-- Demo Admin (password: password123)
('demo_admin', 'admin@demo.com', '$2y$10$8K1p/a0dChZnNPfOwt/My.Uy8lrjKBH1fRlmSxqrO8L.KlXg2Nu7W', 'admin');

-- Demo Save-to-Buy transactions for demo users (amounts in Indian Rupees)
INSERT INTO save_to_buy (user_id, station_id, fuel_type, locked_price, quantity, total_amount, expiry_date, status, created_at) VALUES
-- Active demo purchases
((SELECT id FROM users WHERE username = 'demo_customer'), 1, 'petrol', 101.50, 25.0, 2537.50, DATE_ADD(NOW(), INTERVAL 5 DAY), 'active', NOW() - INTERVAL 2 DAY),
((SELECT id FROM users WHERE username = 'alice_smith'), 2, 'diesel', 88.00, 20.0, 1760.00, DATE_ADD(NOW(), INTERVAL 4 DAY), 'active', NOW() - INTERVAL 1 DAY),
((SELECT id FROM users WHERE username = 'bob_jones'), 3, 'petrol', 102.65, 30.0, 3079.50, DATE_ADD(NOW(), INTERVAL 6 DAY), 'active', NOW() - INTERVAL 3 DAY),

-- Redeemed demo purchases (showing savings)
((SELECT id FROM users WHERE username = 'demo_customer'), 1, 'diesel', 87.50, 18.0, 1575.00, NOW() - INTERVAL 1 DAY, 'redeemed', NOW() - INTERVAL 8 DAY),
((SELECT id FROM users WHERE username = 'alice_smith'), 2, 'petrol', 100.25, 22.0, 2205.50, NOW() - INTERVAL 2 DAY, 'redeemed', NOW() - INTERVAL 9 DAY),
((SELECT id FROM users WHERE username = 'carol_white'), 3, 'diesel', 87.80, 15.5, 1360.90, NOW() - INTERVAL 3 DAY, 'redeemed', NOW() - INTERVAL 10 DAY);

-- Update redeemed_at timestamps
UPDATE save_to_buy SET redeemed_at = DATE_SUB(expiry_date, INTERVAL 2 DAY) WHERE status = 'redeemed' AND redeemed_at IS NULL;