-- Additional Sample Data for Petromine Application
USE petromine;

-- Insert sample users (customers and pump owners)
INSERT INTO users (username, email, password, role) VALUES 
-- Customers
('john_doe', 'john.doe@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
('sarah_wilson', 'sarah.wilson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
('mike_johnson', 'mike.johnson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
('emma_davis', 'emma.davis@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
('alex_brown', 'alex.brown@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),

-- Pump Owners
('shell_owner', 'owner@shell-stations.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pump_owner'),
('bp_owner', 'owner@bp-express.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pump_owner'),
('exxon_owner', 'owner@exxon-mobile.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pump_owner'),
('chevron_owner', 'owner@chevron-stations.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pump_owner'),
('texaco_owner', 'owner@texaco-fuel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pump_owner');

-- Update existing stations with proper owners
UPDATE fuel_stations SET owner_id = 7 WHERE id = 1; -- Shell owner
UPDATE fuel_stations SET owner_id = 8 WHERE id = 2; -- BP owner  
UPDATE fuel_stations SET owner_id = 9 WHERE id = 3; -- Exxon owner

-- Insert additional fuel stations
INSERT INTO fuel_stations (owner_id, name, address, latitude, longitude, phone) VALUES
(7, 'Shell Station Uptown', '321 Uptown Boulevard, North District', 40.7505, -73.9934, '+1-555-0104'),
(7, 'Shell Express Mall Road', '654 Mall Road, Shopping District', 40.7282, -74.0776, '+1-555-0105'),
(8, 'BP Station Riverside', '987 Riverside Drive, Riverside', 40.8176, -73.9482, '+1-555-0106'),
(8, 'BP Quick Stop Airport', '147 Airport Road, Terminal Area', 40.6892, -74.1745, '+1-555-0107'),
(9, 'Exxon Station Westside', '258 West Avenue, Westside', 40.7614, -73.9776, '+1-555-0108'),
(10, 'Chevron Downtown Plaza', '369 Plaza Street, Downtown', 40.7484, -73.9857, '+1-555-0109'),
(10, 'Chevron Highway Junction', '741 Highway Junction, Suburb East', 40.7282, -73.7949, '+1-555-0110'),
(11, 'Texaco City Center', '852 City Center Ave, Midtown', 40.7549, -73.9840, '+1-555-0111'),
(11, 'Texaco Express Lane', '963 Express Lane, Industrial Area', 40.6782, -73.9442, '+1-555-0112'),
(7, 'Shell 24/7 Station', '159 Night Owl Street, Late District', 40.7128, -74.0060, '+1-555-0113');

-- Insert current fuel prices for all stations (in Indian Rupees per liter)
INSERT INTO fuel_prices (station_id, fuel_type, price, effective_date) VALUES
-- Station 4 (Shell Uptown)
(4, 'petrol', 102.85, NOW() - INTERVAL 2 HOUR),
(4, 'diesel', 90.15, NOW() - INTERVAL 2 HOUR),
-- Station 5 (Shell Mall Road)
(5, 'petrol', 102.20, NOW() - INTERVAL 1 HOUR),
(5, 'diesel', 89.60, NOW() - INTERVAL 1 HOUR),
-- Station 6 (BP Riverside)
(6, 'petrol', 103.45, NOW() - INTERVAL 3 HOUR),
(6, 'diesel', 90.85, NOW() - INTERVAL 3 HOUR),
-- Station 7 (BP Airport)
(7, 'petrol', 104.20, NOW() - INTERVAL 30 MINUTE),
(7, 'diesel', 91.40, NOW() - INTERVAL 30 MINUTE),
-- Station 8 (Exxon Westside)
(8, 'petrol', 102.65, NOW() - INTERVAL 4 HOUR),
(8, 'diesel', 89.95, NOW() - INTERVAL 4 HOUR),
-- Station 9 (Chevron Plaza)
(9, 'petrol', 101.85, NOW() - INTERVAL 5 HOUR),
(9, 'diesel', 89.25, NOW() - INTERVAL 5 HOUR),
-- Station 10 (Chevron Highway)
(10, 'petrol', 101.45, NOW() - INTERVAL 6 HOUR),
(10, 'diesel', 88.70, NOW() - INTERVAL 6 HOUR),
-- Station 11 (Texaco City)
(11, 'petrol', 103.75, NOW() - INTERVAL 1 HOUR),
(11, 'diesel', 91.05, NOW() - INTERVAL 1 HOUR),
-- Station 12 (Texaco Express)
(12, 'petrol', 102.35, NOW() - INTERVAL 2 HOUR),
(12, 'diesel', 89.45, NOW() - INTERVAL 2 HOUR),
-- Station 13 (Shell 24/7)
(13, 'petrol', 103.10, NOW() - INTERVAL 30 MINUTE),
(13, 'diesel', 90.25, NOW() - INTERVAL 30 MINUTE);

-- Insert historical price data (price changes over time) - in Indian Rupees
INSERT INTO fuel_prices (station_id, fuel_type, price, effective_date) VALUES
-- Yesterday's prices (slightly different)
(1, 'petrol', 102.15, NOW() - INTERVAL 1 DAY),
(1, 'diesel', 89.35, NOW() - INTERVAL 1 DAY),
(2, 'petrol', 101.50, NOW() - INTERVAL 1 DAY),
(2, 'diesel', 88.55, NOW() - INTERVAL 1 DAY),
(3, 'petrol', 102.85, NOW() - INTERVAL 1 DAY),
(3, 'diesel', 89.95, NOW() - INTERVAL 1 DAY),

-- Last week's prices
(1, 'petrol', 100.80, NOW() - INTERVAL 7 DAY),
(1, 'diesel', 88.20, NOW() - INTERVAL 7 DAY),
(2, 'petrol', 100.25, NOW() - INTERVAL 7 DAY),
(2, 'diesel', 87.80, NOW() - INTERVAL 7 DAY),
(3, 'petrol', 101.45, NOW() - INTERVAL 7 DAY),
(3, 'diesel', 88.75, NOW() - INTERVAL 7 DAY);

-- Insert comprehensive station services (prices in Indian Rupees)
INSERT INTO station_services (station_id, service_name, is_available, price, description) VALUES
-- Shell Station Downtown (ID: 1)
(1, 'Engine Oil Change', TRUE, 2499.00, 'Full synthetic oil change service'),
(1, 'Nitrogen Air Fill', TRUE, 50.00, 'Nitrogen tire inflation service'),
(1, 'Car Wash', TRUE, 150.00, 'Basic car wash service'),
(1, 'Tire Pressure Check', TRUE, 0.00, 'Free tire pressure check'),
(1, 'Windshield Cleaning', TRUE, 0.00, 'Complimentary windshield cleaning'),
(1, 'ATM Service', TRUE, 0.00, 'On-site ATM available'),

-- BP Express Highway (ID: 2)
(2, 'Engine Oil Change', TRUE, 2299.00, 'Conventional oil change service'),
(2, 'Nitrogen Air Fill', TRUE, 45.00, 'Nitrogen tire inflation service'),
(2, 'Convenience Store', TRUE, 0.00, '24/7 convenience store'),
(2, 'Coffee & Snacks', TRUE, 0.00, 'Fresh coffee and snacks available'),
(2, 'Restrooms', TRUE, 0.00, 'Clean restroom facilities'),
(2, 'Phone Charging', TRUE, 0.00, 'Free phone charging station'),

-- Exxon City Center (ID: 3)
(3, 'Engine Oil Change', TRUE, 2799.00, 'Premium oil change service'),
(3, 'Nitrogen Air Fill', TRUE, 55.00, 'Nitrogen tire inflation service'),
(3, 'Tire Pressure Check', TRUE, 0.00, 'Free tire pressure check'),
(3, 'Car Vacuum', TRUE, 20.00, 'Self-service car vacuum'),
(3, 'Air Fresheners', TRUE, 99.00, 'Various car air fresheners'),
(3, 'Emergency Kit', TRUE, 1999.00, 'Basic car emergency kit'),

-- Shell Uptown (ID: 4)
(4, 'Premium Car Wash', TRUE, 250.00, 'Premium car wash with wax'),
(4, 'Oil Change Express', TRUE, 2199.00, 'Quick 15-minute oil change'),
(4, 'Tire Rotation', TRUE, 999.00, 'Professional tire rotation service'),
(4, 'Battery Check', TRUE, 0.00, 'Free battery health check'),
(4, 'Brake Fluid Check', TRUE, 0.00, 'Complimentary brake fluid check'),
(4, 'WiFi Access', TRUE, 0.00, 'Free WiFi for customers'),

-- Shell Mall Road (ID: 5)
(5, 'Engine Oil Change', TRUE, 2399.00, 'Standard oil change service'),
(5, 'Nitrogen Air Fill', TRUE, 49.00, 'Nitrogen tire inflation'),
(5, 'Shopping Center Access', TRUE, 0.00, 'Direct access to shopping mall'),
(5, 'Food Court Nearby', TRUE, 0.00, 'Food court within walking distance'),
(5, 'Parking Validation', TRUE, 0.00, 'Free parking validation'),

-- BP Riverside (ID: 6)
(6, 'Scenic View', TRUE, 0.00, 'Beautiful riverside location'),
(6, 'Picnic Area', TRUE, 0.00, 'Small picnic area available'),
(6, 'Engine Oil Change', TRUE, 2599.00, 'Full service oil change'),
(6, 'Fishing Supplies', TRUE, 0.00, 'Basic fishing supplies available'),
(6, 'Ice & Beverages', TRUE, 0.00, 'Cold beverages and ice'),

-- BP Airport (ID: 7)
(7, 'Express Service', TRUE, 0.00, 'Quick fuel service for travelers'),
(7, 'Travel Snacks', TRUE, 0.00, 'Travel-sized snacks and drinks'),
(7, 'Engine Oil Change', TRUE, 2999.00, 'Premium express oil change'),
(7, 'Car Rental Info', TRUE, 0.00, 'Car rental information desk'),
(7, 'Airport Shuttle', TRUE, 50.00, 'Shuttle service to airport terminal'),

-- Exxon Westside (ID: 8)
(8, 'Engine Oil Change', TRUE, 2699.00, 'High-quality oil change service'),
(8, 'Nitrogen Air Fill', TRUE, 52.00, 'Professional nitrogen service'),
(8, 'Mechanic Services', TRUE, 0.00, 'Basic mechanical services available'),
(8, 'Towing Service', TRUE, 0.00, 'Emergency towing service contact'),
(8, 'Jumper Cables', TRUE, 399.00, 'Jumper cables for sale'),

-- Chevron Plaza (ID: 9)
(9, 'Premium Services', TRUE, 0.00, 'Full-service fuel station'),
(9, 'Engine Oil Change', TRUE, 2899.00, 'Premium oil change with inspection'),
(9, 'Car Detailing', TRUE, 7999.00, 'Professional car detailing service'),
(9, 'Tire Sales', TRUE, 0.00, 'New tire sales and installation'),
(9, 'Credit Card Only', TRUE, 0.00, 'Accepts all major credit cards'),

-- Chevron Highway (ID: 10)
(10, 'Truck Services', TRUE, 0.00, 'Services for large vehicles'),
(10, 'Engine Oil Change', TRUE, 1999.00, 'Budget-friendly oil change'),
(10, 'Diesel Exhaust Fluid', TRUE, 299.00, 'DEF for diesel vehicles'),
(10, 'Truck Parking', TRUE, 0.00, 'Large vehicle parking available'),
(10, 'Weigh Station Info', TRUE, 0.00, 'Nearby weigh station information'),

-- Texaco City (ID: 11)
(11, 'City Convenience', TRUE, 0.00, 'Urban convenience services'),
(11, 'Engine Oil Change', TRUE, 2449.00, 'Standard oil change service'),
(11, 'Metro Card Sales', TRUE, 0.00, 'Public transit cards available'),
(11, 'Bike Pump', TRUE, 0.00, 'Free bicycle tire pump'),
(11, 'Electric Car Charging', TRUE, 150.00, 'Level 2 EV charging station'),

-- Texaco Express (ID: 12)
(12, 'Industrial Services', TRUE, 0.00, 'Services for commercial vehicles'),
(12, 'Bulk Fuel Sales', TRUE, 0.00, 'Bulk fuel for businesses'),
(12, 'Engine Oil Change', TRUE, 2349.00, 'Commercial-grade oil change'),
(12, 'Fleet Discounts', TRUE, 0.00, 'Discounts for fleet customers'),
(12, 'Invoice Billing', TRUE, 0.00, 'Business invoice billing available'),

-- Shell 24/7 (ID: 13)
(13, '24/7 Service', TRUE, 0.00, 'Round-the-clock fuel service'),
(13, 'Night Security', TRUE, 0.00, 'Well-lit and secure location'),
(13, 'Engine Oil Change', TRUE, 2549.00, '24-hour oil change service'),
(13, 'Emergency Services', TRUE, 0.00, 'Emergency fuel delivery contact'),
(13, 'Late Night Snacks', TRUE, 0.00, 'Snacks and beverages available 24/7');

-- Insert sample "Save to Buy" transactions (amounts in Indian Rupees)
INSERT INTO save_to_buy (user_id, station_id, fuel_type, locked_price, quantity, total_amount, expiry_date, status, created_at) VALUES
-- Active purchases
(2, 1, 'petrol', 101.85, 20.0, 2037.00, DATE_ADD(NOW(), INTERVAL 5 DAY), 'active', NOW() - INTERVAL 2 DAY),
(3, 2, 'diesel', 88.55, 15.5, 1372.53, DATE_ADD(NOW(), INTERVAL 3 DAY), 'active', NOW() - INTERVAL 4 DAY),
(4, 3, 'petrol', 102.85, 25.0, 2571.25, DATE_ADD(NOW(), INTERVAL 6 DAY), 'active', NOW() - INTERVAL 1 DAY),
(5, 4, 'diesel', 90.15, 18.0, 1622.70, DATE_ADD(NOW(), INTERVAL 4 DAY), 'active', NOW() - INTERVAL 3 DAY),
(6, 5, 'petrol', 102.20, 22.5, 2299.50, DATE_ADD(NOW(), INTERVAL 2 DAY), 'active', NOW() - INTERVAL 5 DAY),

-- Redeemed purchases (with savings)
(2, 1, 'petrol', 100.80, 30.0, 3024.00, NOW() - INTERVAL 2 DAY, 'redeemed', NOW() - INTERVAL 9 DAY),
(3, 2, 'diesel', 87.80, 20.0, 1756.00, NOW() - INTERVAL 1 DAY, 'redeemed', NOW() - INTERVAL 8 DAY),
(4, 3, 'petrol', 101.45, 25.0, 2536.25, NOW() - INTERVAL 3 DAY, 'redeemed', NOW() - INTERVAL 10 DAY),
(5, 6, 'diesel', 89.25, 16.5, 1472.63, NOW() - INTERVAL 4 DAY, 'redeemed', NOW() - INTERVAL 11 DAY),
(6, 7, 'petrol', 102.65, 28.0, 2874.20, NOW() - INTERVAL 1 DAY, 'redeemed', NOW() - INTERVAL 6 DAY),

-- Expired purchases
(2, 8, 'diesel', 88.20, 12.0, 1058.40, NOW() - INTERVAL 2 DAY, 'expired', NOW() - INTERVAL 15 DAY),
(3, 9, 'petrol', 100.25, 18.5, 1854.63, NOW() - INTERVAL 5 DAY, 'expired', NOW() - INTERVAL 12 DAY),
(4, 10, 'diesel', 87.80, 21.0, 1843.80, NOW() - INTERVAL 1 DAY, 'expired', NOW() - INTERVAL 14 DAY);

-- Update redeemed_at for redeemed purchases
UPDATE save_to_buy SET redeemed_at = DATE_SUB(expiry_date, INTERVAL 1 DAY) WHERE status = 'redeemed';