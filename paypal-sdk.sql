-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 27, 2025 at 04:06 PM
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
-- Database: `paypal_sdk`
--

-- --------------------------------------------------------

--
-- Table structure for table `checkout_orders`
--

CREATE TABLE `checkout_orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `payer_email` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_captures`
--

CREATE TABLE `payment_captures` (
  `id` int(11) NOT NULL,
  `capture_id` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `payee_email` varchar(255) NOT NULL,
  `status` varchar(250) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `item_number` varchar(50) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `item_price` float(10,2) DEFAULT NULL,
  `item_price_currency` varchar(10) DEFAULT NULL,
  `payer_id` varchar(50) DEFAULT NULL,
  `payer_name` varchar(50) DEFAULT NULL,
  `payer_email` varchar(50) DEFAULT NULL,
  `payer_country` varchar(20) DEFAULT NULL,
  `merchant_id` varchar(255) DEFAULT NULL,
  `merchant_email` varchar(50) DEFAULT NULL,
  `order_id` varchar(50) NOT NULL,
  `transaction_id` varchar(50) NOT NULL,
  `paid_amount` float(10,2) NOT NULL,
  `paid_amount_currency` varchar(10) NOT NULL,
  `payment_source` varchar(50) DEFAULT NULL,
  `payment_status` varchar(25) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `checkout_orders`
--
ALTER TABLE `checkout_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_captures`
--
ALTER TABLE `payment_captures`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `checkout_orders`
--
ALTER TABLE `checkout_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_captures`
--
ALTER TABLE `payment_captures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
