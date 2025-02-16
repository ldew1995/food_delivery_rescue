-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 16, 2025 at 06:14 PM
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
-- Database: `food_delivery_rescue`
--

-- --------------------------------------------------------

--
-- Table structure for table `delivery_partners`
--

CREATE TABLE `delivery_partners` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `latitude` decimal(10,6) NOT NULL,
  `longitude` decimal(10,6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_partners`
--

INSERT INTO `delivery_partners` (`id`, `name`, `phone`, `latitude`, `longitude`, `created_at`) VALUES
(1, 'Rahul Kumar', '+919876543210', 17.389044, 78.482671, '2025-02-15 15:04:46'),
(2, 'Amit Sharma', '+919874563210', 19.049500, 72.706297, '2025-02-15 15:04:46'),
(3, 'Sunil Verma', '+919865432110', 17.395044, 78.479671, '2025-02-15 15:04:46'),
(4, 'Deepak Reddy', '+919832145678', 17.399044, 78.490671, '2025-02-15 15:04:46'),
(5, 'Ravi Patel', '+919856741230', 17.400044, 78.485671, '2025-02-15 15:04:46'),
(6, 'Kunal Joshi', '+919823156789', 17.384044, 78.478671, '2025-02-15 15:04:46'),
(7, 'Vikas Gupta', '+919852367489', 17.390044, 78.486971, '2025-02-15 15:04:46'),
(8, 'Anil Mehta', '+919843257890', 17.388044, 78.484671, '2025-02-15 15:04:46'),
(9, 'Harish Kumar', '+919812345678', 17.387044, 78.485671, '2025-02-15 15:04:46'),
(10, 'Sandeep Yadav', '+919865478901', 17.392544, 78.483671, '2025-02-15 15:04:46');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `restaurant_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('pending','canceled','claimed') DEFAULT 'pending',
  `reason` varchar(100) DEFAULT NULL,
  `original_price` decimal(10,2) NOT NULL,
  `discounted_price` decimal(10,2) NOT NULL,
  `latitude` decimal(10,6) NOT NULL,
  `longitude` decimal(10,6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `parent_id`, `restaurant_id`, `user_id`, `status`, `reason`, `original_price`, `discounted_price`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(1, NULL, 1, 1, 'canceled', NULL, 250.00, 175.00, 17.385044, 78.486671, '2025-02-15 15:04:16', NULL),
(2, NULL, 2, 2, 'canceled', NULL, 300.00, 210.00, 17.392044, 78.489671, '2025-02-15 15:04:16', NULL),
(3, NULL, 3, 3, 'canceled', NULL, 450.00, 325.00, 19.049500, 72.856297, '2025-02-15 15:04:16', '2025-02-16 17:31:39'),
(4, NULL, 4, 4, 'canceled', NULL, 180.00, 125.00, 17.381044, 78.475671, '2025-02-15 15:04:16', NULL),
(5, NULL, 5, 5, 'canceled', NULL, 500.00, 350.00, 17.395044, 78.479671, '2025-02-15 15:04:16', NULL),
(6, NULL, 6, 6, 'canceled', NULL, 200.00, 140.00, 17.399044, 78.490671, '2025-02-15 15:04:16', NULL),
(7, NULL, 7, 7, 'canceled', NULL, 275.00, 190.00, 17.400044, 78.485671, '2025-02-15 15:04:16', NULL),
(8, NULL, 8, 8, 'canceled', NULL, 325.00, 230.00, 17.384044, 78.478671, '2025-02-15 15:04:16', NULL),
(9, NULL, 9, 9, 'canceled', NULL, 220.00, 160.00, 17.390044, 78.486971, '2025-02-15 15:04:16', NULL),
(10, NULL, 10, 10, 'claimed', NULL, 280.00, 195.00, 17.388044, 78.484671, '2025-02-15 15:04:16', '2025-02-16 20:30:54'),
(11, NULL, 1, 1, 'canceled', NULL, 250.00, 175.00, 17.385044, 78.486671, '2025-02-15 15:04:16', NULL),
(12, NULL, 2, 2, 'pending', NULL, 300.00, 210.00, 17.392044, 78.489671, '2025-02-15 15:04:16', '2025-02-16 21:34:03'),
(13, NULL, 3, 3, 'pending', NULL, 450.00, 325.00, 17.389044, 78.482671, '2025-02-15 15:04:16', '2025-02-16 21:35:49'),
(14, NULL, 4, 4, 'canceled', 'User does not want order now.', 180.00, 325.00, 17.381044, 78.475671, '2025-02-15 15:04:16', '2025-02-16 22:26:09'),
(15, NULL, 5, 5, 'pending', NULL, 500.00, 350.00, 17.395044, 78.479671, '2025-02-15 15:04:16', '2025-02-16 21:34:18'),
(16, NULL, 6, 6, 'claimed', NULL, 200.00, 140.00, 17.399044, 78.490671, '2025-02-15 15:04:16', '2025-02-16 22:31:19'),
(17, NULL, 7, 7, 'claimed', NULL, 275.00, 190.00, 17.400044, 78.485671, '2025-02-15 15:04:16', '2025-02-16 20:30:38'),
(18, NULL, 8, 8, 'claimed', NULL, 325.00, 230.00, 17.384044, 78.478671, '2025-02-15 15:04:16', '2025-02-16 20:30:32'),
(19, NULL, 9, 2, 'canceled', 'User does not want order now.', 220.00, 200.00, 17.390044, 78.486971, '2025-02-15 15:04:16', '2025-02-16 19:14:29'),
(20, NULL, 10, 10, 'canceled', 'Parcel delayed by 30mins', 280.00, 190.00, 17.388044, 78.484671, '2025-02-15 15:04:16', NULL),
(25, 18, 8, 2, 'claimed', NULL, 325.00, 230.00, 17.384044, 78.478671, '2025-02-16 14:58:41', NULL),
(26, 17, 7, 2, 'claimed', NULL, 275.00, 190.00, 17.400044, 78.485671, '2025-02-16 14:59:49', NULL),
(27, 10, 10, 2, 'claimed', NULL, 280.00, 195.00, 17.388044, 78.484671, '2025-02-16 15:00:54', NULL),
(28, 16, 6, 2, 'claimed', NULL, 200.00, 140.00, 17.399044, 78.490671, '2025-02-16 17:01:19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_delivery`
--

CREATE TABLE `order_delivery` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `delivery_partner_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','in-transit','delivered') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_delivery`
--

INSERT INTO `order_delivery` (`id`, `order_id`, `delivery_partner_id`, `assigned_at`, `status`) VALUES
(1, 1, 1, '2025-02-15 15:04:56', 'pending'),
(2, 2, 2, '2025-02-15 15:04:56', 'pending'),
(3, 3, 3, '2025-02-15 15:04:56', 'pending'),
(4, 4, 4, '2025-02-15 15:04:56', 'pending'),
(5, 5, 5, '2025-02-15 15:04:56', 'pending'),
(6, 6, 6, '2025-02-15 15:04:56', 'pending'),
(7, 7, 7, '2025-02-15 15:04:56', 'pending'),
(8, 8, 8, '2025-02-15 15:04:56', 'pending'),
(9, 9, 9, '2025-02-15 15:04:56', 'pending'),
(10, 10, 10, '2025-02-15 15:04:56', 'pending'),
(11, 11, 1, '2025-02-15 15:04:56', 'pending'),
(12, 12, 2, '2025-02-15 15:04:56', 'pending'),
(13, 13, 3, '2025-02-15 15:04:56', 'pending'),
(14, 14, 4, '2025-02-15 15:04:56', 'pending'),
(15, 15, 5, '2025-02-15 15:04:56', 'pending'),
(16, 16, 6, '2025-02-15 15:04:56', 'pending'),
(17, 17, 7, '2025-02-15 15:04:56', 'pending'),
(18, 18, 8, '2025-02-15 15:04:56', 'pending'),
(19, 19, 9, '2025-02-15 15:04:56', 'pending'),
(20, 20, 10, '2025-02-15 15:04:56', 'pending'),
(21, 25, 8, '2025-02-16 14:58:41', 'pending'),
(22, 26, 7, '2025-02-16 14:59:49', 'pending'),
(23, 27, 10, '2025-02-16 15:00:54', 'pending'),
(24, 28, 6, '2025-02-16 17:01:19', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `category` enum('veg','non-veg','sensitive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `item_name`, `category`) VALUES
(1, 1, 'Paneer Butter Masala', 'veg'),
(2, 1, 'Garlic Naan', 'veg'),
(3, 2, 'Chicken Biryani', 'non-veg'),
(4, 3, 'Mushroom Pizza', 'veg'),
(5, 3, 'French Fries', 'veg'),
(6, 4, 'Veg Fried Rice', 'veg'),
(7, 5, 'Grilled Chicken', 'non-veg'),
(8, 6, 'Veg Manchurian', 'veg'),
(9, 7, 'Dal Tadka', 'veg'),
(10, 8, 'Tandoori Roti', 'veg'),
(11, 9, 'French Fries', 'veg'),
(12, 10, 'Grilled Chicken', 'non-veg'),
(13, 11, 'Gobhi Butter Masala ', 'veg'),
(14, 12, 'Butter Naan', 'veg'),
(15, 13, 'Mutton Biryani', 'non-veg'),
(16, 14, 'Popcorn Pizza', 'veg'),
(17, 14, 'Momos Fries', 'veg'),
(18, 15, 'Veg Pullaw Rice', 'veg'),
(19, 16, 'Roasted Chicken', 'non-veg'),
(20, 16, 'Veg Momos', 'veg'),
(21, 17, 'Dal Fry', 'veg'),
(22, 18, 'Paratha', 'veg'),
(23, 19, 'Veg Fries', 'veg'),
(24, 20, 'Smokey Chicken', 'non-veg'),
(26, 25, 'Paratha', 'veg'),
(27, 26, 'Dal Fry', 'veg'),
(28, 27, 'Grilled Chicken', 'non-veg'),
(29, 28, 'Roasted Chicken', 'non-veg'),
(30, 28, 'Veg Momos', 'veg');

-- --------------------------------------------------------

--
-- Table structure for table `refresh_tokens`
--

CREATE TABLE `refresh_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `refresh_tokens`
--

INSERT INTO `refresh_tokens` (`id`, `user_id`, `token`, `expires_at`, `created_at`) VALUES
(1, 11, '4b7e6b61c8c87e9d7f62f0b1efb924e09d2592e11a8e684e2ba29f43602b8859', '2025-02-16 09:26:10', '2025-02-16 13:56:07'),
(2, 11, 'dab452b78b9ee51d0996e924102f921ab106e6313987821cc409eccf4497393c', '2025-02-16 10:29:37', '2025-02-16 13:59:37'),
(3, 11, '10058d72c455c9ce52297b570fba07d8b2e7f1ab91177212352020efba4a3ca7', '2025-02-16 10:30:24', '2025-02-16 14:00:24'),
(4, 11, 'a4a579d47518c1799844684c206c34a6885554e8d6c4bb6d676a7a400e95ed14', '2025-02-16 13:24:12', '2025-02-16 16:54:12'),
(5, 12, 'f75b9ef436b4c665818bdf4a96dbc45edf3e8fbd328b37d9ba3d6e93b8c69b09', '2025-02-16 13:40:03', '2025-02-16 17:10:03'),
(6, 12, '1f742d8cf03338badaed550889dff375c7f96ed66109ed63d23ed288de43104e', '2025-02-16 13:42:06', '2025-02-16 17:12:06');

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `latitude` decimal(10,6) NOT NULL,
  `longitude` decimal(10,6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `latitude`, `longitude`, `created_at`) VALUES
(1, 'Veggie Delight', 17.387044, 78.485671, '2025-02-15 15:03:54'),
(2, 'Spicy Hub', 17.395044, 78.492671, '2025-02-15 15:03:54'),
(3, 'Tandoori Treats', 17.389044, 78.488671, '2025-02-15 15:03:54'),
(4, 'South Indian Spice', 17.383044, 78.479671, '2025-02-15 15:03:54'),
(5, 'Pasta Paradise', 17.390044, 78.481671, '2025-02-15 15:03:54'),
(6, 'BBQ House', 17.401044, 78.487671, '2025-02-15 15:03:54'),
(7, 'Golden Dragon', 17.386044, 78.490671, '2025-02-15 15:03:54'),
(8, 'Urban Tiffins', 17.397044, 78.489671, '2025-02-15 15:03:54'),
(9, 'Green Earth Cafe', 17.385544, 78.484671, '2025-02-15 15:03:54'),
(10, 'The Burger Stop', 17.392544, 78.483671, '2025-02-15 15:03:54'),
(11, 'Veggie Delight', 17.387044, 78.485671, '2025-02-15 15:04:06'),
(12, 'Spicy Hub', 17.395044, 78.492671, '2025-02-15 15:04:06'),
(13, 'Tandoori Treats', 17.389044, 78.488671, '2025-02-15 15:04:06'),
(14, 'South Indian Spice', 17.383044, 78.479671, '2025-02-15 15:04:06'),
(15, 'Pasta Paradise', 17.390044, 78.481671, '2025-02-15 15:04:06'),
(16, 'BBQ House', 17.401044, 78.487671, '2025-02-15 15:04:06'),
(17, 'Golden Dragon', 17.386044, 78.490671, '2025-02-15 15:04:06'),
(18, 'Urban Tiffins', 17.397044, 78.489671, '2025-02-15 15:04:06'),
(19, 'Green Earth Cafe', 17.385544, 78.484671, '2025-02-15 15:04:06'),
(20, 'The Burger Stop', 17.392544, 78.483671, '2025-02-15 15:04:06');

-- --------------------------------------------------------

--
-- Table structure for table `revenue_sharing`
--

CREATE TABLE `revenue_sharing` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `original_user_share` decimal(10,2) NOT NULL,
  `restaurant_share` decimal(10,2) NOT NULL,
  `delivery_partner_share` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `revenue_sharing`
--

INSERT INTO `revenue_sharing` (`id`, `order_id`, `original_user_share`, `restaurant_share`, `delivery_partner_share`, `created_at`) VALUES
(1, 1, 30.00, 120.00, 25.00, '2025-02-15 15:05:12'),
(2, 2, 45.00, 150.00, 30.00, '2025-02-15 15:05:12'),
(3, 3, 65.00, 200.00, 60.00, '2025-02-15 15:05:12'),
(4, 4, 20.00, 80.00, 25.00, '2025-02-15 15:05:12'),
(5, 5, 70.00, 250.00, 50.00, '2025-02-15 15:05:12'),
(6, 6, 28.00, 90.00, 22.00, '2025-02-15 15:05:12'),
(7, 7, 35.00, 120.00, 35.00, '2025-02-15 15:05:12'),
(8, 8, 40.00, 150.00, 40.00, '2025-02-15 15:05:12'),
(9, 9, 30.00, 100.00, 30.00, '2025-02-15 15:05:12'),
(10, 10, 38.00, 125.00, 32.00, '2025-02-15 15:05:12'),
(11, 11, 30.00, 120.00, 25.00, '2025-02-15 15:05:12'),
(12, 12, 45.00, 150.00, 30.00, '2025-02-15 15:05:12'),
(13, 13, 65.00, 200.00, 60.00, '2025-02-15 15:05:12'),
(14, 14, 20.00, 80.00, 25.00, '2025-02-15 15:05:12'),
(15, 15, 70.00, 250.00, 50.00, '2025-02-15 15:05:12'),
(16, 16, 28.00, 90.00, 22.00, '2025-02-15 15:05:12'),
(17, 17, 35.00, 120.00, 35.00, '2025-02-15 15:05:12'),
(18, 18, 40.00, 150.00, 40.00, '2025-02-15 15:05:12'),
(19, 19, 30.00, 100.00, 30.00, '2025-02-15 15:05:12'),
(20, 20, 38.00, 125.00, 32.00, '2025-02-15 15:05:12');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,6) NOT NULL,
  `longitude` decimal(10,6) NOT NULL,
  `preference` enum('vegetarian','non-vegetarian') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `latitude`, `longitude`, `preference`, `created_at`) VALUES
(1, 'John Doe', 'john@example.com', '$2y$10$rxfC/Nx7o7n/ouzi9dMHIOIlyrCesXbA7yv95PtYOqFfcoNTsuLWW', 17.385044, 78.486671, 'vegetarian', '2025-02-15 15:03:44'),
(2, 'Alice Smith', 'alice@example.com', '$2y$10$rxfC/Nx7o7n/ouzi9dMHIOIlyrCesXbA7yv95PtYOqFfcoNTsuLWW', 19.076000, 72.877700, 'non-vegetarian', '2025-02-15 15:03:44'),
(3, 'Bob Johnson', 'bob@example.com', '$2y$10$rxfC/Nx7o7n/ouzi9dMHIOIlyrCesXbA7yv95PtYOqFfcoNTsuLWW', 17.389044, 78.482671, 'vegetarian', '2025-02-15 15:03:44'),
(4, 'Emma Brown', 'emma@example.com', '$2y$10$rxfC/Nx7o7n/ouzi9dMHIOIlyrCesXbA7yv95PtYOqFfcoNTsuLWW', 17.381044, 78.475671, 'non-vegetarian', '2025-02-15 15:03:44'),
(5, 'David Wilson', 'david@example.com', '$2y$10$rxfC/Nx7o7n/ouzi9dMHIOIlyrCesXbA7yv95PtYOqFfcoNTsuLWW', 17.395044, 78.479671, 'vegetarian', '2025-02-15 15:03:44'),
(6, 'Sophia Miller', 'sophia@example.com', '$2y$10$rxfC/Nx7o7n/ouzi9dMHIOIlyrCesXbA7yv95PtYOqFfcoNTsuLWW', 17.399044, 78.490671, 'vegetarian', '2025-02-15 15:03:44'),
(7, 'James Anderson', 'james@example.com', '$2y$10$rxfC/Nx7o7n/ouzi9dMHIOIlyrCesXbA7yv95PtYOqFfcoNTsuLWW', 17.400044, 78.485671, 'non-vegetarian', '2025-02-15 15:03:44'),
(8, 'Olivia Thomas', 'olivia@example.com', '$2y$10$rxfC/Nx7o7n/ouzi9dMHIOIlyrCesXbA7yv95PtYOqFfcoNTsuLWW', 17.384044, 78.478671, 'vegetarian', '2025-02-15 15:03:44'),
(9, 'Daniel Martinez', 'daniel@example.com', '$2y$10$rxfC/Nx7o7n/ouzi9dMHIOIlyrCesXbA7yv95PtYOqFfcoNTsuLWW', 17.390044, 78.486971, 'vegetarian', '2025-02-15 15:03:44'),
(10, 'Charlotte White', 'charlotte@example.com', '$2y$10$rxfC/Nx7o7n/ouzi9dMHIOIlyrCesXbA7yv95PtYOqFfcoNTsuLWW', 17.388044, 78.484671, 'non-vegetarian', '2025-02-15 15:03:44'),
(11, 'Lata', 'ld@gmail.com', '$2y$10$rxfC/Nx7o7n/ouzi9dMHIOIlyrCesXbA7yv95PtYOqFfcoNTsuLWW', 0.000000, 0.000000, 'vegetarian', '2025-02-16 13:51:50'),
(12, 'Jaya Pandey', 'jaya.pandey@gmail.com', '$2y$10$yfCXQCFHneFZv.fd1DSGtubtqyt6ANquM5qWGT4ETyilBq67PqiuO', 0.000000, 0.000000, 'vegetarian', '2025-02-16 17:08:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `delivery_partners`
--
ALTER TABLE `delivery_partners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD KEY `idx_delivery_location` (`latitude`,`longitude`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orders_status` (`status`),
  ADD KEY `idx_orders_location` (`latitude`,`longitude`),
  ADD KEY `idx_orders_created` (`created_at`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_restaurant_id` (`restaurant_id`);

--
-- Indexes for table `order_delivery`
--
ALTER TABLE `order_delivery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `delivery_partner_id` (`delivery_partner_id`),
  ADD KEY `idx_order_delivery_status` (`status`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_items_order_id` (`order_id`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_restaurants_location` (`latitude`,`longitude`);

--
-- Indexes for table `revenue_sharing`
--
ALTER TABLE `revenue_sharing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_revenue_order` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_location` (`latitude`,`longitude`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `delivery_partners`
--
ALTER TABLE `delivery_partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `order_delivery`
--
ALTER TABLE `order_delivery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `revenue_sharing`
--
ALTER TABLE `revenue_sharing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_delivery`
--
ALTER TABLE `order_delivery`
  ADD CONSTRAINT `order_delivery_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_delivery_ibfk_2` FOREIGN KEY (`delivery_partner_id`) REFERENCES `delivery_partners` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  ADD CONSTRAINT `refresh_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `revenue_sharing`
--
ALTER TABLE `revenue_sharing`
  ADD CONSTRAINT `revenue_sharing_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
