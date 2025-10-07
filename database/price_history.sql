-- Historical Price Data for Analytics and Trends
USE petromine;

-- Insert price history for the last 30 days to show price trends
-- This creates realistic price fluctuations over time

-- Shell Station Downtown (ID: 1) - Price History (in Indian Rupees)
INSERT INTO fuel_prices (station_id, fuel_type, price, effective_date) VALUES
-- 30 days ago
(1, 'petrol', 97.25, NOW() - INTERVAL 30 DAY),
(1, 'diesel', 84.65, NOW() - INTERVAL 30 DAY),
-- 25 days ago
(1, 'petrol', 98.20, NOW() - INTERVAL 25 DAY),
(1, 'diesel', 85.40, NOW() - INTERVAL 25 DAY),
-- 20 days ago
(1, 'petrol', 99.15, NOW() - INTERVAL 20 DAY),
(1, 'diesel', 86.20, NOW() - INTERVAL 20 DAY),
-- 15 days ago
(1, 'petrol', 100.05, NOW() - INTERVAL 15 DAY),
(1, 'diesel', 87.15, NOW() - INTERVAL 15 DAY),
-- 10 days ago
(1, 'petrol', 100.80, NOW() - INTERVAL 10 DAY),
(1, 'diesel', 88.20, NOW() - INTERVAL 10 DAY),
-- 5 days ago
(1, 'petrol', 101.70, NOW() - INTERVAL 5 DAY),
(1, 'diesel', 89.05, NOW() - INTERVAL 5 DAY),
-- 2 days ago
(1, 'petrol', 102.15, NOW() - INTERVAL 2 DAY),
(1, 'diesel', 89.35, NOW() - INTERVAL 2 DAY);

-- BP Express Highway (ID: 2) - Price History (in Indian Rupees)
INSERT INTO fuel_prices (station_id, fuel_type, price, effective_date) VALUES
-- 30 days ago
(2, 'petrol', 96.50, NOW() - INTERVAL 30 DAY),
(2, 'diesel', 84.20, NOW() - INTERVAL 30 DAY),
-- 25 days ago
(2, 'petrol', 97.25, NOW() - INTERVAL 25 DAY),
(2, 'diesel', 84.95, NOW() - INTERVAL 25 DAY),
-- 20 days ago
(2, 'petrol', 98.45, NOW() - INTERVAL 20 DAY),
(2, 'diesel', 85.80, NOW() - INTERVAL 20 DAY),
-- 15 days ago
(2, 'petrol', 99.20, NOW() - INTERVAL 15 DAY),
(2, 'diesel', 86.70, NOW() - INTERVAL 15 DAY),
-- 10 days ago
(2, 'petrol', 100.25, NOW() - INTERVAL 10 DAY),
(2, 'diesel', 87.80, NOW() - INTERVAL 10 DAY),
-- 5 days ago
(2, 'petrol', 101.15, NOW() - INTERVAL 5 DAY),
(2, 'diesel', 88.45, NOW() - INTERVAL 5 DAY),
-- 2 days ago
(2, 'petrol', 101.50, NOW() - INTERVAL 2 DAY),
(2, 'diesel', 88.55, NOW() - INTERVAL 2 DAY);

-- Exxon City Center (ID: 3) - Price History (in Indian Rupees)
INSERT INTO fuel_prices (station_id, fuel_type, price, effective_date) VALUES
-- 30 days ago
(3, 'petrol', 98.20, NOW() - INTERVAL 30 DAY),
(3, 'diesel', 85.40, NOW() - INTERVAL 30 DAY),
-- 25 days ago
(3, 'petrol', 99.15, NOW() - INTERVAL 25 DAY),
(3, 'diesel', 86.25, NOW() - INTERVAL 25 DAY),
-- 20 days ago
(3, 'petrol', 100.05, NOW() - INTERVAL 20 DAY),
(3, 'diesel', 87.15, NOW() - INTERVAL 20 DAY),
-- 15 days ago
(3, 'petrol', 100.80, NOW() - INTERVAL 15 DAY),
(3, 'diesel', 88.20, NOW() - INTERVAL 15 DAY),
-- 10 days ago
(3, 'petrol', 101.45, NOW() - INTERVAL 10 DAY),
(3, 'diesel', 88.75, NOW() - INTERVAL 10 DAY),
-- 5 days ago
(3, 'petrol', 102.65, NOW() - INTERVAL 5 DAY),
(3, 'diesel', 89.95, NOW() - INTERVAL 5 DAY),
-- 2 days ago
(3, 'petrol', 102.85, NOW() - INTERVAL 2 DAY),
(3, 'diesel', 89.95, NOW() - INTERVAL 2 DAY);

-- Add some recent price updates (within last few hours) to show real-time changes (in Indian Rupees)
INSERT INTO fuel_prices (station_id, fuel_type, price, effective_date) VALUES
-- Recent updates for demonstration
(1, 'petrol', 102.50, NOW() - INTERVAL 3 HOUR),
(2, 'diesel', 88.90, NOW() - INTERVAL 2 HOUR),
(3, 'petrol', 103.20, NOW() - INTERVAL 1 HOUR),
(4, 'diesel', 90.15, NOW() - INTERVAL 4 HOUR),
(5, 'petrol', 102.20, NOW() - INTERVAL 5 HOUR);

-- Weekend price variations (typically higher on weekends) - in Indian Rupees
INSERT INTO fuel_prices (station_id, fuel_type, price, effective_date) VALUES
-- Last weekend (Saturday)
(1, 'petrol', 103.45, DATE_SUB(NOW(), INTERVAL WEEKDAY(NOW()) + 1 DAY)),
(1, 'diesel', 90.25, DATE_SUB(NOW(), INTERVAL WEEKDAY(NOW()) + 1 DAY)),
(2, 'petrol', 102.85, DATE_SUB(NOW(), INTERVAL WEEKDAY(NOW()) + 1 DAY)),
(2, 'diesel', 89.60, DATE_SUB(NOW(), INTERVAL WEEKDAY(NOW()) + 1 DAY)),
(3, 'petrol', 104.20, DATE_SUB(NOW(), INTERVAL WEEKDAY(NOW()) + 1 DAY)),
(3, 'diesel', 91.05, DATE_SUB(NOW(), INTERVAL WEEKDAY(NOW()) + 1 DAY));

-- Holiday price spikes (simulate holiday weekend) - in Indian Rupees
INSERT INTO fuel_prices (station_id, fuel_type, price, effective_date) VALUES
-- Festival season prices (higher during Diwali/Dussehra)
(1, 'petrol', 105.25, NOW() - INTERVAL 21 DAY),
(1, 'diesel', 92.15, NOW() - INTERVAL 21 DAY),
(2, 'petrol', 104.80, NOW() - INTERVAL 21 DAY),
(2, 'diesel', 91.70, NOW() - INTERVAL 21 DAY),
(3, 'petrol', 106.45, NOW() - INTERVAL 21 DAY),
(3, 'diesel', 93.25, NOW() - INTERVAL 21 DAY);