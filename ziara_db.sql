-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 14, 2025 at 04:16 AM
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
-- Database: `ziara_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:09:35'),
(2, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:13:33'),
(3, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:14:10'),
(4, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:14:13'),
(5, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:14:36'),
(6, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:15:12'),
(7, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:15:15'),
(8, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:15:45'),
(9, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:16:16'),
(10, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:18:47'),
(11, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:19:56'),
(12, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:23:48'),
(13, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:24:25'),
(14, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:24:41'),
(15, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:25:08'),
(16, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:26:02'),
(17, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:27:30'),
(18, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:28:30'),
(19, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:29:15'),
(20, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:29:46'),
(21, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:30:10'),
(22, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:30:28'),
(23, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:31:26'),
(24, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:32:02'),
(25, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:32:36'),
(26, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:33:14'),
(27, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:33:53'),
(28, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:34:32'),
(29, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:35:08'),
(30, 'Hashini', 'hashini@gmail.com', 'floral dress', 'discount price', '2025-09-13 16:35:20');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `size` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `created_at`) VALUES
(1, 3, 23.00, 'pending', '2025-09-12 06:27:43');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 23.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `description`, `price`, `stock`, `image`, `created_at`) VALUES
(1, 'tshirt', 'men cloths', 'sdfghjkjjgreasrhgfjjhgdfsafvcb', 23.00, 11, '', '2025-09-12 06:25:47'),
(2, 'Floral Sundress', 'Women', 'Linen Blend', 8900.00, 50, '', '2025-09-12 19:48:46'),
(3, 'Linen Casual Shirt', 'men', 'Pure Linen', 7500.00, 25, 'product_2.png', '2025-09-12 19:51:32'),
(4, 'Tailored Trousers', 'Women', 'Sand Beige', 9200.00, 23, 'product_3.png', '2025-09-12 19:56:45'),
(5, 'Leather Crossbody Bag', 'Accessories', 'Tan Brown', 12500.00, 5, 'product_4.png', '2025-09-12 20:14:59'),
(6, 'Floral maxi dress', 'Women', 'viscorse', 6290.00, 15, 'women7.jpeg', '2025-09-13 19:20:49'),
(7, 'Sky Blue Mini Dress', 'Women', 'soft cotton', 7600.00, 20, 'women2.jpeg', '2025-09-13 19:22:00'),
(8, 'Navy blue Polo T-Shirt', 'men', 'Cotton polo', 8900.00, 30, 'men5.jpeg', '2025-09-13 19:24:44'),
(9, 'White Mini dress', 'kids', 'Soft Cotton', 4500.00, 23, 'kid6.jpeg', '2025-09-13 19:25:45'),
(10, 'Off White Shirt', 'men', 'pure linen', 8900.00, 5, '', '2025-09-13 19:28:06'),
(11, 'Baggy T-shirt', 'men', 'Dark blue', 7500.00, 25, 'men1.jpeg', '2025-09-13 19:29:11'),
(12, 'Kid\'s Mom Jin', 'kids', 'Light Blue', 5500.00, 11, 'kid2.jpeg', '2025-09-13 19:30:06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'sanilka', 'sanilkagimhan777@gmail.com', '$2y$10$6t0jmlsbY.DBk2TWxia5OOk5rpudYIlk4dEfg0JeUjjGSuGVLw0OG', 'admin', '2025-09-12 06:11:34'),
(2, 'rathnayaka', 'dsrathnayake2003@gmail.com', '$2y$10$rm/sU1YSMscgJG.KiiSxGOs23u7mNPXiDCMA8vsum0XO.iOXqG1tS', 'customer', '2025-09-12 06:19:19'),
(3, 'pathum', 'pathumlakshan123@gmail.com', '$2y$10$uMpT2TPVzyFgCZ1MEVLLV.vRLV5NBB6RQuOsvolukhxq2W.UrlzDW', 'customer', '2025-09-12 06:21:14'),
(4, 'Hashini', 'hashini@gamil.com', '$2y$10$thNsA4dMqE6f.SLUJHD9HOH/2C19Djw5zZidbBHFmsobdoSkWmDEy', 'admin', '2025-09-12 14:08:19');

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

-- --------------------------------------------------------

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
