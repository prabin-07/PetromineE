-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2025 at 11:53 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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

CREATE TABLE `fuel_prices` (
  `id` int(11) NOT NULL,
  `station_id` int(11) DEFAULT NULL,
  `fuel_type` enum('petrol','diesel') NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `effective_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fuel_prices`
--

INSERT INTO `fuel_prices` (`id`, `station_id`, `fuel_type`, `price`, `effective_date`, `created_at`) VALUES
(1, 1, 'petrol', 3.45, '2025-09-29 05:02:50', '2025-09-29 05:02:50'),
(2, 1, 'diesel', 3.89, '2025-09-29 05:02:50', '2025-09-29 05:02:50'),
(3, 2, 'petrol', 3.42, '2025-09-29 05:02:50', '2025-09-29 05:02:50'),
(4, 2, 'diesel', 3.85, '2025-09-29 05:02:50', '2025-09-29 05:02:50'),
(5, 3, 'petrol', 3.48, '2025-09-29 05:02:50', '2025-09-29 05:02:50'),
(6, 3, 'diesel', 3.92, '2025-09-29 05:02:50', '2025-09-29 05:02:50'),
(7, 1, 'petrol', 3.25, '2025-08-30 05:07:42', '2025-09-29 05:07:42'),
(8, 1, 'diesel', 3.65, '2025-08-30 05:07:42', '2025-09-29 05:07:42'),
(9, 1, 'petrol', 3.28, '2025-09-04 05:07:42', '2025-09-29 05:07:42'),
(10, 1, 'diesel', 3.68, '2025-09-04 05:07:42', '2025-09-29 05:07:42'),
(11, 1, 'petrol', 3.32, '2025-09-09 05:07:42', '2025-09-29 05:07:42'),
(12, 1, 'diesel', 3.72, '2025-09-09 05:07:42', '2025-09-29 05:07:42'),
(13, 1, 'petrol', 3.35, '2025-09-14 05:07:42', '2025-09-29 05:07:42'),
(14, 1, 'diesel', 3.75, '2025-09-14 05:07:42', '2025-09-29 05:07:42'),
(15, 1, 'petrol', 3.38, '2025-09-19 05:07:42', '2025-09-29 05:07:42'),
(16, 1, 'diesel', 3.78, '2025-09-19 05:07:42', '2025-09-29 05:07:42'),
(17, 1, 'petrol', 3.42, '2025-09-24 05:07:42', '2025-09-29 05:07:42'),
(18, 1, 'diesel', 3.82, '2025-09-24 05:07:42', '2025-09-29 05:07:42'),
(19, 1, 'petrol', 3.44, '2025-09-27 05:07:42', '2025-09-29 05:07:42'),
(20, 1, 'diesel', 3.86, '2025-09-27 05:07:42', '2025-09-29 05:07:42'),
(21, 2, 'petrol', 3.22, '2025-08-30 05:07:42', '2025-09-29 05:07:42'),
(22, 2, 'diesel', 3.62, '2025-08-30 05:07:42', '2025-09-29 05:07:42'),
(23, 2, 'petrol', 3.25, '2025-09-04 05:07:42', '2025-09-29 05:07:42'),
(24, 2, 'diesel', 3.65, '2025-09-04 05:07:42', '2025-09-29 05:07:42'),
(25, 2, 'petrol', 3.29, '2025-09-09 05:07:42', '2025-09-29 05:07:42'),
(26, 2, 'diesel', 3.69, '2025-09-09 05:07:42', '2025-09-29 05:07:42'),
(27, 2, 'petrol', 3.32, '2025-09-14 05:07:42', '2025-09-29 05:07:42'),
(28, 2, 'diesel', 3.72, '2025-09-14 05:07:42', '2025-09-29 05:07:42'),
(29, 2, 'petrol', 3.35, '2025-09-19 05:07:42', '2025-09-29 05:07:42'),
(30, 2, 'diesel', 3.75, '2025-09-19 05:07:42', '2025-09-29 05:07:42'),
(31, 2, 'petrol', 3.39, '2025-09-24 05:07:42', '2025-09-29 05:07:42'),
(32, 2, 'diesel', 3.79, '2025-09-24 05:07:42', '2025-09-29 05:07:42'),
(33, 2, 'petrol', 3.41, '2025-09-27 05:07:42', '2025-09-29 05:07:42'),
(34, 2, 'diesel', 3.83, '2025-09-27 05:07:42', '2025-09-29 05:07:42'),
(35, 3, 'petrol', 3.28, '2025-08-30 05:07:42', '2025-09-29 05:07:42'),
(36, 3, 'diesel', 3.68, '2025-08-30 05:07:42', '2025-09-29 05:07:42'),
(37, 3, 'petrol', 3.31, '2025-09-04 05:07:42', '2025-09-29 05:07:42'),
(38, 3, 'diesel', 3.71, '2025-09-04 05:07:42', '2025-09-29 05:07:42'),
(39, 3, 'petrol', 3.35, '2025-09-09 05:07:42', '2025-09-29 05:07:42'),
(40, 3, 'diesel', 3.75, '2025-09-09 05:07:42', '2025-09-29 05:07:42'),
(41, 3, 'petrol', 3.38, '2025-09-14 05:07:42', '2025-09-29 05:07:42'),
(42, 3, 'diesel', 3.78, '2025-09-14 05:07:42', '2025-09-29 05:07:42'),
(43, 3, 'petrol', 3.41, '2025-09-19 05:07:42', '2025-09-29 05:07:42'),
(44, 3, 'diesel', 3.81, '2025-09-19 05:07:42', '2025-09-29 05:07:42'),
(45, 3, 'petrol', 3.45, '2025-09-24 05:07:42', '2025-09-29 05:07:42'),
(46, 3, 'diesel', 3.85, '2025-09-24 05:07:42', '2025-09-29 05:07:42'),
(47, 3, 'petrol', 3.47, '2025-09-27 05:07:42', '2025-09-29 05:07:42'),
(48, 3, 'diesel', 3.89, '2025-09-27 05:07:42', '2025-09-29 05:07:42'),
(54, 4, 'petrol', 3.47, '2025-09-29 03:07:50', '2025-09-29 05:07:50'),
(55, 4, 'diesel', 3.91, '2025-09-29 03:07:50', '2025-09-29 05:07:50'),
(56, 5, 'petrol', 3.44, '2025-09-29 04:07:50', '2025-09-29 05:07:50'),
(57, 5, 'diesel', 3.88, '2025-09-29 04:07:50', '2025-09-29 05:07:50'),
(58, 6, 'petrol', 3.49, '2025-09-29 02:07:50', '2025-09-29 05:07:50'),
(59, 6, 'diesel', 3.93, '2025-09-29 02:07:50', '2025-09-29 05:07:50'),
(60, 7, 'petrol', 3.52, '2025-09-29 04:37:50', '2025-09-29 05:07:50'),
(61, 7, 'diesel', 3.96, '2025-09-29 04:37:50', '2025-09-29 05:07:50'),
(62, 8, 'petrol', 3.46, '2025-09-29 01:07:50', '2025-09-29 05:07:50'),
(63, 8, 'diesel', 3.90, '2025-09-29 01:07:50', '2025-09-29 05:07:50'),
(64, 9, 'petrol', 3.43, '2025-09-29 00:07:50', '2025-09-29 05:07:50'),
(65, 9, 'diesel', 3.87, '2025-09-29 00:07:50', '2025-09-29 05:07:50'),
(66, 10, 'petrol', 3.41, '2025-09-28 23:07:50', '2025-09-29 05:07:50'),
(67, 10, 'diesel', 3.84, '2025-09-28 23:07:50', '2025-09-29 05:07:50'),
(68, 11, 'petrol', 3.50, '2025-09-29 04:07:50', '2025-09-29 05:07:50'),
(69, 11, 'diesel', 3.94, '2025-09-29 04:07:50', '2025-09-29 05:07:50'),
(70, 12, 'petrol', 3.45, '2025-09-29 03:07:50', '2025-09-29 05:07:50'),
(71, 12, 'diesel', 3.89, '2025-09-29 03:07:50', '2025-09-29 05:07:50'),
(72, 13, 'petrol', 3.48, '2025-09-29 04:37:50', '2025-09-29 05:07:50'),
(73, 13, 'diesel', 3.92, '2025-09-29 04:37:50', '2025-09-29 05:07:50'),
(74, 1, 'petrol', 3.43, '2025-09-28 05:07:50', '2025-09-29 05:07:50'),
(75, 1, 'diesel', 3.87, '2025-09-28 05:07:50', '2025-09-29 05:07:50'),
(76, 2, 'petrol', 3.40, '2025-09-28 05:07:50', '2025-09-29 05:07:50'),
(77, 2, 'diesel', 3.83, '2025-09-28 05:07:50', '2025-09-29 05:07:50'),
(78, 3, 'petrol', 3.46, '2025-09-28 05:07:50', '2025-09-29 05:07:50'),
(79, 3, 'diesel', 3.90, '2025-09-28 05:07:50', '2025-09-29 05:07:50'),
(80, 1, 'petrol', 3.38, '2025-09-22 05:07:50', '2025-09-29 05:07:50'),
(81, 1, 'diesel', 3.82, '2025-09-22 05:07:50', '2025-09-29 05:07:50'),
(82, 2, 'petrol', 3.35, '2025-09-22 05:07:50', '2025-09-29 05:07:50'),
(83, 2, 'diesel', 3.78, '2025-09-22 05:07:50', '2025-09-29 05:07:50'),
(84, 3, 'petrol', 3.41, '2025-09-22 05:07:50', '2025-09-29 05:07:50'),
(85, 3, 'diesel', 3.85, '2025-09-22 05:07:50', '2025-09-29 05:07:50'),
(86, 1, 'petrol', 3.25, '2025-08-30 05:07:58', '2025-09-29 05:07:58'),
(87, 1, 'diesel', 3.65, '2025-08-30 05:07:58', '2025-09-29 05:07:58'),
(88, 1, 'petrol', 3.28, '2025-09-04 05:07:58', '2025-09-29 05:07:58'),
(89, 1, 'diesel', 3.68, '2025-09-04 05:07:58', '2025-09-29 05:07:58'),
(90, 1, 'petrol', 3.32, '2025-09-09 05:07:58', '2025-09-29 05:07:58'),
(91, 1, 'diesel', 3.72, '2025-09-09 05:07:58', '2025-09-29 05:07:58'),
(92, 1, 'petrol', 3.35, '2025-09-14 05:07:58', '2025-09-29 05:07:58'),
(93, 1, 'diesel', 3.75, '2025-09-14 05:07:58', '2025-09-29 05:07:58'),
(94, 1, 'petrol', 3.38, '2025-09-19 05:07:58', '2025-09-29 05:07:58'),
(95, 1, 'diesel', 3.78, '2025-09-19 05:07:58', '2025-09-29 05:07:58'),
(96, 1, 'petrol', 3.42, '2025-09-24 05:07:58', '2025-09-29 05:07:58'),
(97, 1, 'diesel', 3.82, '2025-09-24 05:07:58', '2025-09-29 05:07:58'),
(98, 1, 'petrol', 3.44, '2025-09-27 05:07:58', '2025-09-29 05:07:58'),
(99, 1, 'diesel', 3.86, '2025-09-27 05:07:58', '2025-09-29 05:07:58'),
(100, 2, 'petrol', 3.22, '2025-08-30 05:07:58', '2025-09-29 05:07:58'),
(101, 2, 'diesel', 3.62, '2025-08-30 05:07:58', '2025-09-29 05:07:58'),
(102, 2, 'petrol', 3.25, '2025-09-04 05:07:58', '2025-09-29 05:07:58'),
(103, 2, 'diesel', 3.65, '2025-09-04 05:07:58', '2025-09-29 05:07:58'),
(104, 2, 'petrol', 3.29, '2025-09-09 05:07:58', '2025-09-29 05:07:58'),
(105, 2, 'diesel', 3.69, '2025-09-09 05:07:58', '2025-09-29 05:07:58'),
(106, 2, 'petrol', 3.32, '2025-09-14 05:07:58', '2025-09-29 05:07:58'),
(107, 2, 'diesel', 3.72, '2025-09-14 05:07:58', '2025-09-29 05:07:58'),
(108, 2, 'petrol', 3.35, '2025-09-19 05:07:58', '2025-09-29 05:07:58'),
(109, 2, 'diesel', 3.75, '2025-09-19 05:07:58', '2025-09-29 05:07:58'),
(110, 2, 'petrol', 3.39, '2025-09-24 05:07:58', '2025-09-29 05:07:58'),
(111, 2, 'diesel', 3.79, '2025-09-24 05:07:58', '2025-09-29 05:07:58'),
(112, 2, 'petrol', 3.41, '2025-09-27 05:07:58', '2025-09-29 05:07:58'),
(113, 2, 'diesel', 3.83, '2025-09-27 05:07:58', '2025-09-29 05:07:58'),
(114, 3, 'petrol', 3.28, '2025-08-30 05:07:58', '2025-09-29 05:07:58'),
(115, 3, 'diesel', 3.68, '2025-08-30 05:07:58', '2025-09-29 05:07:58'),
(116, 3, 'petrol', 3.31, '2025-09-04 05:07:58', '2025-09-29 05:07:58'),
(117, 3, 'diesel', 3.71, '2025-09-04 05:07:58', '2025-09-29 05:07:58'),
(118, 3, 'petrol', 3.35, '2025-09-09 05:07:58', '2025-09-29 05:07:58'),
(119, 3, 'diesel', 3.75, '2025-09-09 05:07:58', '2025-09-29 05:07:58'),
(120, 3, 'petrol', 3.38, '2025-09-14 05:07:58', '2025-09-29 05:07:58'),
(121, 3, 'diesel', 3.78, '2025-09-14 05:07:58', '2025-09-29 05:07:58'),
(122, 3, 'petrol', 3.41, '2025-09-19 05:07:58', '2025-09-29 05:07:58'),
(123, 3, 'diesel', 3.81, '2025-09-19 05:07:58', '2025-09-29 05:07:58'),
(124, 3, 'petrol', 3.45, '2025-09-24 05:07:58', '2025-09-29 05:07:58'),
(125, 3, 'diesel', 3.85, '2025-09-24 05:07:58', '2025-09-29 05:07:58'),
(126, 3, 'petrol', 3.47, '2025-09-27 05:07:58', '2025-09-29 05:07:58'),
(127, 3, 'diesel', 3.89, '2025-09-27 05:07:58', '2025-09-29 05:07:58'),
(128, 1, 'petrol', 3.45, '2025-09-29 02:07:58', '2025-09-29 05:07:58'),
(129, 2, 'diesel', 3.85, '2025-09-29 03:07:58', '2025-09-29 05:07:58'),
(130, 3, 'petrol', 3.48, '2025-09-29 04:07:58', '2025-09-29 05:07:58'),
(131, 4, 'diesel', 3.91, '2025-09-29 01:07:58', '2025-09-29 05:07:58'),
(132, 5, 'petrol', 3.44, '2025-09-29 00:07:58', '2025-09-29 05:07:58'),
(133, 1, 'petrol', 3.49, '2025-09-28 05:07:58', '2025-09-29 05:07:58'),
(134, 1, 'diesel', 3.89, '2025-09-28 05:07:58', '2025-09-29 05:07:58'),
(135, 2, 'petrol', 3.46, '2025-09-28 05:07:58', '2025-09-29 05:07:58'),
(136, 2, 'diesel', 3.86, '2025-09-28 05:07:58', '2025-09-29 05:07:58'),
(137, 3, 'petrol', 3.52, '2025-09-28 05:07:58', '2025-09-29 05:07:58'),
(138, 3, 'diesel', 3.92, '2025-09-28 05:07:58', '2025-09-29 05:07:58'),
(139, 1, 'petrol', 3.55, '2025-09-08 05:07:58', '2025-09-29 05:07:58'),
(140, 1, 'diesel', 3.95, '2025-09-08 05:07:58', '2025-09-29 05:07:58'),
(141, 2, 'petrol', 3.52, '2025-09-08 05:07:58', '2025-09-29 05:07:58'),
(142, 2, 'diesel', 3.92, '2025-09-08 05:07:58', '2025-09-29 05:07:58'),
(143, 3, 'petrol', 3.58, '2025-09-08 05:07:58', '2025-09-29 05:07:58'),
(144, 3, 'diesel', 3.98, '2025-09-08 05:07:58', '2025-09-29 05:07:58');

-- --------------------------------------------------------

--
-- Table structure for table `fuel_stations`
--

CREATE TABLE `fuel_stations` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fuel_stations`
--

INSERT INTO `fuel_stations` (`id`, `owner_id`, `name`, `address`, `latitude`, `longitude`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 7, 'IndianOil - MG Road Kochi', 'MG Road, Kochi, Kerala', 9.93120000, 76.26730000, '+91-484-000-0101', 1, '2025-09-29 05:02:50', '2025-09-29 05:07:50'),
(2, 8, 'BPCL - Vyttila Mobility Hub', 'Vyttila, Kochi, Kerala', 9.96760000, 76.31810000, '+91-484-000-0102', 1, '2025-09-29 05:02:50', '2025-09-29 05:07:50'),
(3, 9, 'HP - Technopark Trivandrum', 'Technopark, Thiruvananthapuram, Kerala', 8.55960000, 76.87980000, '+91-471-000-0103', 1, '2025-09-29 05:02:50', '2025-09-29 05:07:50'),
(4, 7, 'IndianOil - Palayam Trivandrum', 'Palayam, Thiruvananthapuram, Kerala', 8.52410000, 76.93660000, '+91-471-000-0104', 1, '2025-09-29 05:07:50', '2025-09-29 05:07:50'),
(5, 7, 'BPCL - Edappally Kochi', 'Edappally, Kochi, Kerala', 10.02800000, 76.30830000, '+91-484-000-0105', 1, '2025-09-29 05:07:50', '2025-09-29 05:07:50'),
(6, 8, 'HP - Marine Drive Ernakulam', 'Marine Drive, Ernakulam, Kerala', 9.98200000, 76.28000000, '+91-484-000-0106', 1, '2025-09-29 05:07:50', '2025-09-29 05:07:50'),
(7, 8, 'BPCL - Cochin Intl Airport', 'CIAL, Nedumbassery, Kerala', 10.15180000, 76.40190000, '+91-484-000-0107', 1, '2025-09-29 05:07:50', '2025-09-29 05:07:50'),
(8, 9, 'IndianOil - Calicut Beach', 'Beach Rd, Kozhikode, Kerala', 11.25880000, 75.78040000, '+91-495-000-0108', 1, '2025-09-29 05:07:50', '2025-09-29 05:07:50'),
(9, 10, 'HP - Thrissur Swaraj Round', 'Swaraj Round, Thrissur, Kerala', 10.52760000, 76.21440000, '+91-487-000-0109', 1, '2025-09-29 05:07:50', '2025-09-29 05:07:50'),
(10, 10, 'IndianOil - Aluva Metro', 'Aluva, Kochi, Kerala', 10.10960000, 76.35160000, '+91-484-000-0110', 1, '2025-09-29 05:07:50', '2025-09-29 05:07:50'),
(11, 11, 'BPCL - Kollam Chinnakada', 'Chinnakada, Kollam, Kerala', 8.89320000, 76.61410000, '+91-474-000-0111', 1, '2025-09-29 05:07:50', '2025-09-29 05:07:50'),
(12, 11, 'HP - Palakkad Town', 'Stadium Bypass Rd, Palakkad, Kerala', 10.78670000, 76.65480000, '+91-491-000-0112', 1, '2025-09-29 05:07:50', '2025-09-29 05:07:50'),
(13, 7, 'IndianOil - Kochi 24/7', 'Banerji Rd, Ernakulam, Kerala', 9.98160000, 76.28510000, '+91-484-000-0113', 1, '2025-09-29 05:07:50', '2025-09-29 05:07:50');

-- --------------------------------------------------------

--
-- Table structure for table `save_to_buy`
--

CREATE TABLE `save_to_buy` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `station_id` int(11) DEFAULT NULL,
  `fuel_type` enum('petrol','diesel') NOT NULL,
  `locked_price` decimal(8,2) NOT NULL,
  `quantity` decimal(8,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `expiry_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','redeemed','expired') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `redeemed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `save_to_buy`
--

INSERT INTO `save_to_buy` (`id`, `user_id`, `station_id`, `fuel_type`, `locked_price`, `quantity`, `total_amount`, `expiry_date`, `status`, `created_at`, `redeemed_at`) VALUES
(1, 2, 1, 'petrol', 3.40, 25.00, 85.00, '2025-10-04 05:07:24', 'active', '2025-09-27 05:07:24', NULL),
(2, 3, 2, 'diesel', 3.80, 20.00, 76.00, '2025-10-03 05:07:24', 'active', '2025-09-28 05:07:24', NULL),
(3, 4, 3, 'petrol', 3.45, 30.00, 103.50, '2025-10-05 05:07:24', 'active', '2025-09-26 05:07:24', NULL),
(4, 2, 1, 'diesel', 3.75, 18.00, 67.50, '2025-09-29 05:07:50', 'redeemed', '2025-09-21 05:07:24', '2025-09-28 05:07:24'),
(5, 3, 2, 'petrol', 3.35, 22.00, 73.70, '2025-09-29 05:07:50', 'redeemed', '2025-09-20 05:07:24', '2025-09-28 05:07:24'),
(6, 5, 3, 'diesel', 3.78, 15.50, 58.59, '2025-09-29 05:07:50', 'redeemed', '2025-09-19 05:07:24', '2025-09-28 05:07:24'),
(7, 2, 1, 'petrol', 3.43, 20.00, 68.60, '2025-10-04 05:07:50', 'active', '2025-09-27 05:07:50', NULL),
(8, 3, 2, 'diesel', 3.83, 15.50, 59.37, '2025-10-02 05:07:50', 'active', '2025-09-25 05:07:50', NULL),
(9, 4, 3, 'petrol', 3.46, 25.00, 86.50, '2025-10-05 05:07:50', 'active', '2025-09-28 05:07:50', NULL),
(10, 5, 4, 'diesel', 3.89, 18.00, 70.02, '2025-10-03 05:07:50', 'active', '2025-09-26 05:07:50', NULL),
(11, 6, 5, 'petrol', 3.44, 22.50, 77.40, '2025-10-01 05:07:50', 'active', '2025-09-24 05:07:50', NULL),
(12, 2, 1, 'petrol', 3.38, 30.00, 101.40, '2025-09-29 05:07:50', 'redeemed', '2025-09-20 05:07:50', '2025-09-26 05:07:50'),
(13, 3, 2, 'diesel', 3.78, 20.00, 75.60, '2025-09-29 05:07:50', 'redeemed', '2025-09-21 05:07:50', '2025-09-27 05:07:50'),
(14, 4, 3, 'petrol', 3.41, 25.00, 85.25, '2025-09-29 05:07:50', 'redeemed', '2025-09-19 05:07:50', '2025-09-25 05:07:50'),
(15, 5, 6, 'diesel', 3.85, 16.50, 63.53, '2025-09-29 05:07:50', 'redeemed', '2025-09-18 05:07:50', '2025-09-24 05:07:50'),
(16, 6, 7, 'petrol', 3.45, 28.00, 96.60, '2025-09-29 05:07:50', 'redeemed', '2025-09-23 05:07:50', '2025-09-27 05:07:50'),
(17, 2, 8, 'diesel', 3.82, 12.00, 45.84, '2025-09-27 05:07:50', 'expired', '2025-09-14 05:07:50', NULL),
(18, 3, 9, 'petrol', 3.35, 18.50, 61.98, '2025-09-24 05:07:50', 'expired', '2025-09-17 05:07:50', NULL),
(19, 4, 10, 'diesel', 3.78, 21.00, 79.38, '2025-09-28 05:07:50', 'expired', '2025-09-15 05:07:50', NULL),
(20, 19, 7, 'petrol', 3.52, 23.00, 80.96, '2025-09-29 09:51:24', 'redeemed', '2025-09-29 05:31:18', '2025-09-29 09:51:24'),
(21, 19, 2, 'diesel', 3.92, 10.00, 39.20, '2025-10-06 06:21:57', 'active', '2025-09-29 09:51:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `station_services`
--

CREATE TABLE `station_services` (
  `id` int(11) NOT NULL,
  `station_id` int(11) DEFAULT NULL,
  `service_name` varchar(100) NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `price` decimal(8,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `station_services`
--

INSERT INTO `station_services` (`id`, `station_id`, `service_name`, `is_available`, `price`, `description`, `created_at`) VALUES
(1, 1, 'Air Filling', 1, 0.00, 'Free tire air filling service', '2025-09-29 05:02:50'),
(2, 1, 'Restrooms', 1, 0.00, 'Clean restroom facilities', '2025-09-29 05:02:50'),
(3, 1, 'Air Filling', 1, 0.00, 'Free tire air filling service', '2025-09-29 05:02:50'),
(4, 2, 'Restrooms', 1, 0.00, 'Clean restroom facilities', '2025-09-29 05:02:50'),
(5, 2, 'Air Filling', 1, 0.00, 'Free tire air filling service', '2025-09-29 05:02:50'),
(6, 3, 'Restrooms', 1, 0.00, 'Clean restroom facilities', '2025-09-29 05:02:50'),
(7, 3, 'Air Filling', 1, 0.00, 'Free tire air filling service', '2025-09-29 05:02:50'),
(8, 3, 'Air Filling', 1, 0.00, 'Free tire air filling service', '2025-09-29 05:02:50'),
(9, 1, 'Air Filling', 1, 0.00, 'Free tire air filling service', '2025-09-29 05:07:50'),
(10, 1, 'Restrooms', 1, 0.00, 'Clean restroom facilities', '2025-09-29 05:07:50'),
(11, 1, 'Air Filling', 1, 0.00, 'Free tire air filling service', '2025-09-29 05:07:50'),
(12, 1, 'Tire Pressure Check', 1, 0.00, 'Free tire pressure check', '2025-09-29 05:07:50'),
(13, 1, 'Windshield Cleaning', 1, 0.00, 'Complimentary windshield cleaning', '2025-09-29 05:07:50'),
(14, 1, 'ATM Service', 1, 0.00, 'On-site ATM available', '2025-09-29 05:07:50'),
(15, 2, 'Engine Oil Change', 1, 27.99, 'Conventional oil change service', '2025-09-29 05:07:50'),
(16, 2, 'Nitrogen Air Fill', 1, 4.50, 'Nitrogen tire inflation service', '2025-09-29 05:07:50'),
(17, 2, 'Convenience Store', 1, 0.00, '24/7 convenience store', '2025-09-29 05:07:50'),
(18, 2, 'Coffee & Snacks', 1, 0.00, 'Fresh coffee and snacks available', '2025-09-29 05:07:50'),
(19, 2, 'Restrooms', 1, 0.00, 'Clean restroom facilities', '2025-09-29 05:07:50'),
(20, 2, 'Phone Charging', 1, 0.00, 'Free phone charging station', '2025-09-29 05:07:50'),
(21, 3, 'Engine Oil Change', 1, 32.99, 'Premium oil change service', '2025-09-29 05:07:50'),
(22, 3, 'Nitrogen Air Fill', 1, 5.50, 'Nitrogen tire inflation service', '2025-09-29 05:07:50'),
(23, 3, 'Tire Pressure Check', 1, 0.00, 'Free tire pressure check', '2025-09-29 05:07:50'),
(24, 3, 'Car Vacuum', 1, 2.00, 'Self-service car vacuum', '2025-09-29 05:07:50'),
(25, 3, 'Air Fresheners', 1, 3.99, 'Various car air fresheners', '2025-09-29 05:07:50'),
(26, 3, 'Emergency Kit', 1, 19.99, 'Basic car emergency kit', '2025-09-29 05:07:50'),
(27, 4, 'Premium Car Wash', 1, 25.00, 'Premium car wash with wax', '2025-09-29 05:07:50'),
(28, 4, 'Oil Change Express', 1, 24.99, 'Quick 15-minute oil change', '2025-09-29 05:07:50'),
(29, 4, 'Tire Rotation', 1, 39.99, 'Professional tire rotation service', '2025-09-29 05:07:50'),
(30, 4, 'Battery Check', 1, 0.00, 'Free battery health check', '2025-09-29 05:07:50'),
(31, 4, 'Brake Fluid Check', 1, 0.00, 'Complimentary brake fluid check', '2025-09-29 05:07:50'),
(32, 4, 'WiFi Access', 1, 0.00, 'Free WiFi for customers', '2025-09-29 05:07:50'),
(33, 5, 'Engine Oil Change', 1, 28.99, 'Standard oil change service', '2025-09-29 05:07:50'),
(34, 5, 'Nitrogen Air Fill', 1, 4.99, 'Nitrogen tire inflation', '2025-09-29 05:07:50'),
(35, 5, 'Shopping Center Access', 1, 0.00, 'Direct access to shopping mall', '2025-09-29 05:07:50'),
(36, 5, 'Food Court Nearby', 1, 0.00, 'Food court within walking distance', '2025-09-29 05:07:50'),
(37, 5, 'Parking Validation', 1, 0.00, 'Free parking validation', '2025-09-29 05:07:50'),
(38, 6, 'Scenic View', 1, 0.00, 'Beautiful riverside location', '2025-09-29 05:07:50'),
(39, 6, 'Picnic Area', 1, 0.00, 'Small picnic area available', '2025-09-29 05:07:50'),
(40, 6, 'Engine Oil Change', 1, 30.99, 'Full service oil change', '2025-09-29 05:07:50'),
(41, 6, 'Fishing Supplies', 1, 0.00, 'Basic fishing supplies available', '2025-09-29 05:07:50'),
(42, 6, 'Ice & Beverages', 1, 0.00, 'Cold beverages and ice', '2025-09-29 05:07:50'),
(43, 7, 'Express Service', 1, 0.00, 'Quick fuel service for travelers', '2025-09-29 05:07:50'),
(44, 7, 'Travel Snacks', 1, 0.00, 'Travel-sized snacks and drinks', '2025-09-29 05:07:50'),
(45, 7, 'Engine Oil Change', 1, 35.99, 'Premium express oil change', '2025-09-29 05:07:50'),
(46, 7, 'Car Rental Info', 1, 0.00, 'Car rental information desk', '2025-09-29 05:07:50'),
(47, 7, 'Airport Shuttle', 1, 5.00, 'Shuttle service to airport terminal', '2025-09-29 05:07:50'),
(48, 8, 'Engine Oil Change', 1, 31.99, 'High-quality oil change service', '2025-09-29 05:07:50'),
(49, 8, 'Nitrogen Air Fill', 1, 5.25, 'Professional nitrogen service', '2025-09-29 05:07:50'),
(50, 8, 'Mechanic Services', 1, 0.00, 'Basic mechanical services available', '2025-09-29 05:07:50'),
(51, 8, 'Towing Service', 1, 0.00, 'Emergency towing service contact', '2025-09-29 05:07:50'),
(52, 8, 'Jumper Cables', 1, 15.99, 'Jumper cables for sale', '2025-09-29 05:07:50'),
(53, 9, 'Premium Services', 1, 0.00, 'Full-service fuel station', '2025-09-29 05:07:50'),
(54, 9, 'Engine Oil Change', 1, 33.99, 'Premium oil change with inspection', '2025-09-29 05:07:50'),
(55, 9, 'Car Detailing', 1, 89.99, 'Professional car detailing service', '2025-09-29 05:07:50'),
(56, 9, 'Tire Sales', 1, 0.00, 'New tire sales and installation', '2025-09-29 05:07:50'),
(57, 9, 'Credit Card Only', 1, 0.00, 'Accepts all major credit cards', '2025-09-29 05:07:50'),
(58, 10, 'Truck Services', 1, 0.00, 'Services for large vehicles', '2025-09-29 05:07:50'),
(59, 10, 'Engine Oil Change', 1, 26.99, 'Budget-friendly oil change', '2025-09-29 05:07:50'),
(60, 10, 'Diesel Exhaust Fluid', 1, 12.99, 'DEF for diesel vehicles', '2025-09-29 05:07:50'),
(61, 10, 'Truck Parking', 1, 0.00, 'Large vehicle parking available', '2025-09-29 05:07:50'),
(62, 10, 'Weigh Station Info', 1, 0.00, 'Nearby weigh station information', '2025-09-29 05:07:50'),
(63, 11, 'City Convenience', 1, 0.00, 'Urban convenience services', '2025-09-29 05:07:50'),
(64, 11, 'Engine Oil Change', 1, 29.49, 'Standard oil change service', '2025-09-29 05:07:50'),
(65, 11, 'Metro Card Sales', 1, 0.00, 'Public transit cards available', '2025-09-29 05:07:50'),
(66, 11, 'Bike Pump', 1, 0.00, 'Free bicycle tire pump', '2025-09-29 05:07:50'),
(67, 11, 'Electric Car Charging', 1, 15.00, 'Level 2 EV charging station', '2025-09-29 05:07:50'),
(68, 12, 'Industrial Services', 1, 0.00, 'Services for commercial vehicles', '2025-09-29 05:07:50'),
(69, 12, 'Bulk Fuel Sales', 1, 0.00, 'Bulk fuel for businesses', '2025-09-29 05:07:50'),
(70, 12, 'Engine Oil Change', 1, 28.49, 'Commercial-grade oil change', '2025-09-29 05:07:50'),
(71, 12, 'Fleet Discounts', 1, 0.00, 'Discounts for fleet customers', '2025-09-29 05:07:50'),
(72, 12, 'Invoice Billing', 1, 0.00, 'Business invoice billing available', '2025-09-29 05:07:50'),
(73, 13, '24/7 Service', 1, 0.00, 'Round-the-clock fuel service', '2025-09-29 05:07:50'),
(74, 13, 'Night Security', 1, 0.00, 'Well-lit and secure location', '2025-09-29 05:07:50'),
(75, 13, 'Engine Oil Change', 1, 30.99, '24-hour oil change service', '2025-09-29 05:07:50'),
(76, 13, 'Emergency Services', 1, 0.00, 'Emergency fuel delivery contact', '2025-09-29 05:07:50'),
(77, 13, 'Late Night Snacks', 1, 0.00, 'Snacks and beverages available 24/7', '2025-09-29 05:07:50');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','pump_owner','admin') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@petromine.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-09-29 05:02:50', '2025-09-29 05:02:50'),
(2, 'demo_customer', 'customer@demo.com', '$2y$10$8K1p/a0dChZnNPfOwt/My.Uy8lrjKBH1fRlmSxqrO8L.KlXg2Nu7W', 'customer', '2025-09-29 05:07:24', '2025-09-29 05:07:24'),
(3, 'alice_smith', 'alice@demo.com', '$2y$10$8K1p/a0dChZnNPfOwt/My.Uy8lrjKBH1fRlmSxqrO8L.KlXg2Nu7W', 'customer', '2025-09-29 05:07:24', '2025-09-29 05:07:24'),
(4, 'bob_jones', 'bob@demo.com', '$2y$10$8K1p/a0dChZnNPfOwt/My.Uy8lrjKBH1fRlmSxqrO8L.KlXg2Nu7W', 'customer', '2025-09-29 05:07:24', '2025-09-29 05:07:24'),
(5, 'carol_white', 'carol@demo.com', '$2y$10$8K1p/a0dChZnNPfOwt/My.Uy8lrjKBH1fRlmSxqrO8L.KlXg2Nu7W', 'customer', '2025-09-29 05:07:24', '2025-09-29 05:07:24'),
(6, 'demo_owner', 'owner@demo.com', '$2y$10$8K1p/a0dChZnNPfOwt/My.Uy8lrjKBH1fRlmSxqrO8L.KlXg2Nu7W', 'pump_owner', '2025-09-29 05:07:24', '2025-09-29 05:07:24'),
(7, 'station_manager', 'manager@demo.com', '$2y$10$8K1p/a0dChZnNPfOwt/My.Uy8lrjKBH1fRlmSxqrO8L.KlXg2Nu7W', 'pump_owner', '2025-09-29 05:07:24', '2025-09-29 05:07:24'),
(8, 'demo_admin', 'admin@demo.com', '$2y$10$8K1p/a0dChZnNPfOwt/My.Uy8lrjKBH1fRlmSxqrO8L.KlXg2Nu7W', 'admin', '2025-09-29 05:07:24', '2025-09-29 05:07:24'),
(9, 'john_doe', 'john.doe@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', '2025-09-29 05:07:50', '2025-09-29 05:07:50'),
(10, 'sarah_wilson', 'sarah.wilson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', '2025-09-29 05:07:50', '2025-09-29 05:07:50'),
(11, 'mike_johnson', 'mike.johnson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', '2025-09-29 05:07:50', '2025-09-29 05:07:50'),
(12, 'emma_davis', 'emma.davis@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', '2025-09-29 05:07:50', '2025-09-29 05:07:50'),
(13, 'alex_brown', 'alex.brown@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', '2025-09-29 05:07:50', '2025-09-29 05:07:50'),
(14, 'shell_owner', 'owner@shell-stations.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pump_owner', '2025-09-29 05:07:50', '2025-09-29 05:07:50'),
(15, 'bp_owner', 'owner@bp-express.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pump_owner', '2025-09-29 05:07:50', '2025-09-29 05:07:50'),
(16, 'exxon_owner', 'owner@exxon-mobile.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pump_owner', '2025-09-29 05:07:50', '2025-09-29 05:07:50'),
(17, 'chevron_owner', 'owner@chevron-stations.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pump_owner', '2025-09-29 05:07:50', '2025-09-29 05:07:50'),
(18, 'texaco_owner', 'owner@texaco-fuel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pump_owner', '2025-09-29 05:07:50', '2025-09-29 05:07:50'),
(19, 'ajin', 'chandyajin@gmail.com', '$2y$10$C3gAyfCsB63IP6zarVqOUuGLQ9JgelZKPYwLYO58VAcmQCoudiEA2', 'customer', '2025-09-29 05:10:50', '2025-09-29 09:51:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fuel_prices`
--
ALTER TABLE `fuel_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `station_id` (`station_id`);

--
-- Indexes for table `fuel_stations`
--
ALTER TABLE `fuel_stations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `save_to_buy`
--
ALTER TABLE `save_to_buy`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `station_id` (`station_id`);

--
-- Indexes for table `station_services`
--
ALTER TABLE `station_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `station_id` (`station_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fuel_prices`
--
ALTER TABLE `fuel_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT for table `fuel_stations`
--
ALTER TABLE `fuel_stations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `save_to_buy`
--
ALTER TABLE `save_to_buy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `station_services`
--
ALTER TABLE `station_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
