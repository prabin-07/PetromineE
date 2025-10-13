-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 13, 2025 at 12:24 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `petromine`
--

-- --------------------------------------------------------

--
-- Table structure for table `fuel_prices`
--

DROP TABLE IF EXISTS `fuel_prices`;
CREATE TABLE IF NOT EXISTS `fuel_prices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `station_id` int DEFAULT NULL,
  `fuel_type` enum('petrol','diesel') COLLATE utf8mb4_general_ci NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `effective_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `station_id` (`station_id`)
) ENGINE=InnoDB AUTO_INCREMENT=165 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fuel_prices`
--

INSERT INTO `fuel_prices` (`id`, `station_id`, `fuel_type`, `price`, `effective_date`, `created_at`) VALUES
(145, 14, 'petrol', 54.00, '2025-09-29 10:05:55', '2025-09-29 10:05:55'),
(146, 14, 'diesel', 50.00, '2025-09-29 10:06:05', '2025-09-29 10:06:05'),
(147, 33, 'petrol', 102.50, '2025-10-08 06:46:14', '2025-10-08 06:46:14'),
(148, 33, 'diesel', 89.75, '2025-10-08 06:46:14', '2025-10-08 06:46:14'),
(159, 39, 'petrol', 101.70, '2025-10-08 06:46:14', '2025-10-08 06:46:14'),
(160, 39, 'diesel', 88.85, '2025-10-08 06:46:14', '2025-10-08 06:46:14'),
(161, 40, 'petrol', 102.80, '2025-10-08 06:46:14', '2025-10-08 06:46:14'),
(162, 40, 'diesel', 89.95, '2025-10-08 06:46:14', '2025-10-08 06:46:14'),
(163, 14, 'petrol', 150.00, '2025-10-11 09:35:19', '2025-10-11 09:35:19'),
(164, 14, 'diesel', 89.00, '2025-10-11 09:35:36', '2025-10-11 09:35:36');

-- --------------------------------------------------------

--
-- Table structure for table `fuel_stations`
--

DROP TABLE IF EXISTS `fuel_stations`;
CREATE TABLE IF NOT EXISTS `fuel_stations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `address` text COLLATE utf8mb4_general_ci NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fuel_stations`
--

INSERT INTO `fuel_stations` (`id`, `owner_id`, `name`, `address`, `latitude`, `longitude`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 7, 'PetroMine Station - Kottayam Central', 'Kanjikuzhy, Kottayam, Kerala 686004', 9.59160000, 76.52220000, '+91-9745123456', 1, '2025-09-10 03:30:00', '2025-10-12 09:50:00'),
(2, 20, 'Indian Oil - Nagampadam', 'Nagampadam, Kottayam, Kerala 686001', 9.59840000, 76.52280000, '+91-9846009876', 1, '2025-08-18 04:45:00', '2025-10-11 13:10:00'),
(3, 7, 'HP Fuel Center - Ettumanoor', 'Main Road, Ettumanoor, Kottayam, Kerala 686631', 9.66800000, 76.56470000, '+91-9961554321', 1, '2025-09-25 06:15:00', '2025-10-12 03:40:00'),
(4, 20, 'BPCL Fuel Point - Pala', 'Opp. St. Thomas College, Pala, Kottayam, Kerala 686574', 9.71330000, 76.68530000, '+91-9895022334', 1, '2025-08-05 03:00:00', '2025-10-10 11:55:00'),
(5, 7, 'Shell Express - Changanassery', 'MC Road, Changanassery, Kottayam, Kerala 686101', 9.44220000, 76.54240000, '+91-9747002211', 0, '2025-07-22 07:30:00', '2025-09-29 11:15:00'),
(6, 20, 'Reliance Fuel Hub - Ponkunnam', 'NH 183, Ponkunnam, Kottayam, Kerala 686506', 9.60320000, 76.71140000, '+91-9809554433', 1, '2025-09-08 03:50:00', '2025-10-12 03:25:00'),
(7, 7, 'Essar Energy - Vaikom', 'Vaikom Town, Kottayam, Kerala 686141', 9.74850000, 76.39650000, '+91-9947221100', 1, '2025-09-15 02:15:00', '2025-10-11 06:45:00'),
(8, 20, 'Nayara Fuel Stop - Manarcad', 'Ettumanoor Road, Manarcad, Kottayam, Kerala 686019', 9.57290000, 76.56720000, '+91-9876045678', 1, '2025-09-12 05:20:00', '2025-10-13 03:30:00'),
(9, 7, 'PetroMine Smart Station - Pampady', 'Pampady Junction, Kottayam, Kerala 686502', 9.58510000, 76.63720000, '+91-9745342211', 1, '2025-08-30 08:35:00', '2025-10-12 06:10:00'),
(10, 20, 'HPCL Service - Kurichy', 'Kottayam-Changanassery Road, Kurichy, Kerala 686532', 9.49320000, 76.53810000, '+91-9895098765', 0, '2025-07-10 03:10:00', '2025-09-28 08:00:00'),
(14, 20, 'BP', 'KOTTAYAM BAKER JUNCTION', 99.99999999, 432.43400000, '-1899', 1, '2025-09-29 10:05:41', '2025-10-08 06:16:26'),
(33, 1, 'IndianOil Kochi City Center', '42 MG Road, Ernakulam, Kochi, Kerala', 9.98163500, 76.28077600, '+91-484-555-0101', 1, '2025-10-08 06:09:16', '2025-10-08 06:09:16'),
(39, 7, 'Bharat Petroleum Palakkad Junction', 'Coimbatore Road, Palakkad, Kerala', 10.77648000, 76.65358000, '+91-491-555-0107', 1, '2025-10-08 06:09:16', '2025-10-08 06:09:16'),
(40, 8, 'HP Fuel Station Alleppey Highway', 'NH 66, Kommady, Alappuzha, Kerala', 9.49174000, 76.32699000, '+91-477-555-0108', 1, '2025-10-08 06:09:16', '2025-10-08 06:09:16');

-- --------------------------------------------------------

--
-- Table structure for table `save_to_buy`
--

DROP TABLE IF EXISTS `save_to_buy`;
CREATE TABLE IF NOT EXISTS `save_to_buy` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `station_id` int DEFAULT NULL,
  `fuel_type` enum('petrol','diesel') COLLATE utf8mb4_general_ci NOT NULL,
  `locked_price` decimal(8,2) NOT NULL,
  `quantity` decimal(8,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `expiry_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','redeemed','expired') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `redeemed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `station_id` (`station_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `save_to_buy`
--

INSERT INTO `save_to_buy` (`id`, `user_id`, `station_id`, `fuel_type`, `locked_price`, `quantity`, `total_amount`, `expiry_date`, `status`, `created_at`, `redeemed_at`) VALUES
(22, 19, 14, 'petrol', 54.00, 23.00, 1242.00, '2025-09-29 10:08:39', 'redeemed', '2025-09-29 10:07:28', '2025-09-29 10:08:39'),
(25, 19, 39, 'diesel', 88.85, 50.00, 4442.50, '2025-10-15 02:44:00', 'active', '2025-10-08 08:14:00', NULL),
(26, 20, 14, 'petrol', 150.00, 10.00, 1500.00, '2025-10-20 05:46:40', 'active', '2025-10-13 11:16:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `station_services`
--

DROP TABLE IF EXISTS `station_services`;
CREATE TABLE IF NOT EXISTS `station_services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `station_id` int DEFAULT NULL,
  `service_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `is_available` tinyint(1) DEFAULT '1',
  `price` decimal(8,2) DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `station_id` (`station_id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('customer','pump_owner','admin') COLLATE utf8mb4_general_ci DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@petromine.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-09-29 05:02:50', '2025-10-13 10:40:48'),
(7, 'Rahul', 'rahul@gmail.com', '$2y$10$8K1p/a0dChZnNPfOwt/My.Uy8lrjKBH1fRlmSxqrO8L.KlXg2Nu7W', 'pump_owner', '2025-09-29 05:07:24', '2025-10-13 11:32:23'),
(8, 'demo_admin', 'admin@demo.com', '$2y$10$8K1p/a0dChZnNPfOwt/My.Uy8lrjKBH1fRlmSxqrO8L.KlXg2Nu7W', 'admin', '2025-09-29 05:07:24', '2025-09-29 05:07:24'),
(19, 'ajin', 'chandyajin@gmail.com', '$2y$10$C3gAyfCsB63IP6zarVqOUuGLQ9JgelZKPYwLYO58VAcmQCoudiEA2', 'customer', '2025-09-29 05:10:50', '2025-10-11 09:29:05'),
(20, 'jithu', 'jithu@gmail.com', '$2y$10$xd6LebzNegnFTWiK1h1kNuDXb42PLN3xMIdyfwOQjrqtzIeTNIDoC', 'pump_owner', '2025-09-29 10:04:43', '2025-10-13 12:23:19'),
(21, 'prabin', 'prabin@gmail.com', '$2y$10$5wuHNtNwn.zw2je/LDM6zeXQc5oJtumURWaG2nCk0G.NGpMF4xy7K', 'customer', '2025-10-08 06:38:47', '2025-10-13 10:42:31');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `fuel_prices`
--
ALTER TABLE `fuel_prices`
  ADD CONSTRAINT `fuel_prices_ibfk_1` FOREIGN KEY (`station_id`) REFERENCES `fuel_stations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fuel_stations`
--
ALTER TABLE `fuel_stations`
  ADD CONSTRAINT `fuel_stations_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `save_to_buy`
--
ALTER TABLE `save_to_buy`
  ADD CONSTRAINT `save_to_buy_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `save_to_buy_ibfk_2` FOREIGN KEY (`station_id`) REFERENCES `fuel_stations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `station_services`
--
ALTER TABLE `station_services`
  ADD CONSTRAINT `station_services_ibfk_1` FOREIGN KEY (`station_id`) REFERENCES `fuel_stations` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
