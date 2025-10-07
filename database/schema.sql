CREATE DATABASE IF NOT EXISTS petromine;
USE petromine;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'pump_owner', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Fuel stations table
CREATE TABLE fuel_stations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT,
    name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    phone VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Fuel prices table
CREATE TABLE fuel_prices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    station_id INT,
    fuel_type ENUM('petrol', 'diesel') NOT NULL,
    price DECIMAL(8, 2) NOT NULL,
    effective_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (station_id) REFERENCES fuel_stations(id) ON DELETE CASCADE
);

-- Station services table
CREATE TABLE station_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    station_id INT,
    service_name VARCHAR(100) NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    price DECIMAL(8, 2) DEFAULT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (station_id) REFERENCES fuel_stations(id) ON DELETE CASCADE
);

-- Save to buy table
CREATE TABLE save_to_buy (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    station_id INT,
    fuel_type ENUM('petrol', 'diesel') NOT NULL,
    locked_price DECIMAL(8, 2) NOT NULL,
    quantity DECIMAL(8, 2) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    expiry_date TIMESTAMP NOT NULL,
    status ENUM('active', 'redeemed', 'expired') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    redeemed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (station_id) REFERENCES fuel_stations(id) ON DELETE CASCADE
);

-- Insert default admin user
INSERT INTO users (username, email, password, role) VALUES 
('admin', 'admin@petromine.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample fuel stations
INSERT INTO fuel_stations (owner_id, name, address, latitude, longitude, phone) VALUES
(1, 'IndianOil - MG Road Kochi', 'MG Road, Kochi, Kerala', 9.9312, 76.2673, '+91-484-000-0101'),
(1, 'BPCL - Vyttila Mobility Hub', 'Vyttila, Kochi, Kerala', 9.9676, 76.3181, '+91-484-000-0102'),
(1, 'HP - Technopark Trivandrum', 'Technopark, Thiruvananthapuram, Kerala', 8.5596, 76.8798, '+91-471-000-0103');

-- Insert sample fuel prices (in Indian Rupees per liter)
INSERT INTO fuel_prices (station_id, fuel_type, price) VALUES
(1, 'petrol', 102.50),
(1, 'diesel', 89.75),
(2, 'petrol', 101.80),
(2, 'diesel', 88.90),
(3, 'petrol', 103.20),
(3, 'diesel', 90.45);

-- Insert sample services (prices in Indian Rupees)
INSERT INTO station_services (station_id, service_name, is_available, price, description) VALUES
(1, 'Engine Oil Change', TRUE, 2499.00, 'Full synthetic oil change service'),
(1, 'Nitrogen Air Fill', TRUE, 50.00, 'Nitrogen tire inflation service'),
(1, 'Car Wash', TRUE, 150.00, 'Basic car wash service'),
(2, 'Engine Oil Change', TRUE, 2299.00, 'Conventional oil change service'),
(2, 'Nitrogen Air Fill', TRUE, 45.00, 'Nitrogen tire inflation service'),
(3, 'Engine Oil Change', TRUE, 2799.00, 'Premium oil change service'),
(3, 'Nitrogen Air Fill', TRUE, 55.00, 'Nitrogen tire inflation service'),
(3, 'Tire Pressure Check', TRUE, 0.00, 'Free tire pressure check');