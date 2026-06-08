-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2026 at 11:48 AM
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
-- Database: `assetcare`
--

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `tag` varchar(100) NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `serial` varchar(100) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `purchaseDate` varchar(50) DEFAULT NULL,
  `repairCount` int(11) DEFAULT 0,
  `repairs_json` longtext DEFAULT NULL,
  `deliveryCount` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`tag`, `type`, `brand`, `model`, `serial`, `status`, `purchaseDate`, `repairCount`, `repairs_json`, `deliveryCount`) VALUES
('BCP/QBL-101', 'Laptop', 'Lenovo', 'T470s', 'PC0MEQSH', 'N/A', '', 0, '[]', 0),
('BCP/QBL-103', 'Laptop', 'Lenovo', 'T460s', 'PC0HR9QS', 'N/A', '', 0, '[]', 0),
('BCP/QBL-104', 'Laptop', 'Lenovo', 'T470s', 'PC0P7LHV', 'N/A', '', 0, '[]', 0),
('BCP/QBL-105', 'Laptop', 'Lenovo', 'T460s', 'PC0HRC0W', 'N/A', '', 0, '[]', 0),
('BCP/QBL-106', 'Laptop', 'Lenovo', 'T460s', 'PC0KA47J', 'N/A', '', 0, '[]', 0),
('BCP/QBL-107', 'Laptop', 'Lenovo', 'T470s', 'PC0P7LY4', 'N/A', '', 0, '[]', 0),
('BCP/QBL-108', 'Laptop', 'Lenovo', 'T460s', 'PC0HR9RC', 'N/A', '', 0, '[]', 0),
('BCP/QBL-109', 'Laptop', 'Lenovo', 'T470s', 'PC0NT7PG', 'N/A', '', 0, '[]', 0),
('BCP/QBL-110', 'Laptop', 'Lenovo', 'T470s', 'PC0MEQRK', 'N/A', '', 0, '[]', 0),
('BCP/QBL-111', 'Laptop', 'Lenovo', 'T470s', 'PC0MEQUR', 'N/A', '', 0, '[]', 0),
('BCP/QBL-138', 'Laptop', 'Lenovo', 'T460s', 'PC0JZZ8Q', 'N/A', '', 0, '[]', 0),
('BCP/QBL-139', 'Laptop', 'Lenovo', 'T470s', 'PC0MEQUP', 'N/A', '', 0, '[]', 0),
('BCP/QBL-140', 'Laptop', 'Lenovo', 'T460s', 'PC0DKA9H', 'N/A', '', 0, '[]', 0),
('BCP/QBL-142', 'Laptop', 'Lenovo', 'T470s', 'PC0NT7S5', 'N/A', '', 0, '[]', 0),
('BCP/QBL-143', 'Laptop', 'Lenovo', 'T460s', 'PC0MJYZN', 'N/A', '', 0, '[]', 0),
('BCP/QBL-144', 'Laptop', 'Lenovo', 'T470s', 'PC0FXH89', 'N/A', '', 0, '[]', 0),
('BCP/QBL-145', 'Laptop', 'Lenovo', 'T460s', 'PC0G8KNX', 'N/A', '', 0, '[]', 0),
('BCP/QBL-147', 'Laptop', 'Lenovo', 'T460s', 'PC0LN9WV', 'N/A', '', 0, '[]', 0),
('BCP/QBL-148', 'Laptop', 'Lenovo', 'T460s', 'PC0G7ZZW', 'N/A', '', 0, '[]', 0),
('BCP/QBL-149', 'Laptop', 'Lenovo', 'T460s', 'PC0LN9WS', 'N/A', '', 0, '[]', 0),
('BCP/QBL-150', 'Laptop', 'Lenovo', 'T460s', 'PC0JYT1S', 'N/A', '', 0, '[]', 0),
('BCP/QBL-151', 'Laptop', 'Lenovo', 'T460s', 'PC0DT76P', 'N/A', '', 0, '[]', 0),
('BCP/QBL-152', 'Laptop', 'Lenovo', 'T460s', 'PC0HPTUM', 'N/A', '', 0, '[]', 0),
('BCP/QBL-154', 'Laptop', 'Lenovo', 'T460s', 'PC0FGFZK', 'N/A', '', 0, '[]', 0),
('BCP/QBL-155', 'Laptop', 'Lenovo', 'T460s', 'PC0F0ZF2', 'N/A', '', 0, '[]', 0),
('BCP/QBL-156', 'Laptop', 'Lenovo', 'T460s', 'PC0KUM0Z', 'N/A', '', 0, '[]', 0),
('BCP/QBL-157', 'Laptop', 'Lenovo', 'T460s', 'PC0H8DH9', 'N/A', '', 0, '[]', 0),
('BCP/QBL-158', 'Laptop', 'Lenovo', 'T460s', 'PC0K0F55', 'N/A', '', 0, '[]', 0),
('BCP/QBL-159', 'Laptop', 'Lenovo', 'T460s', 'PC0JYT38', 'N/A', '', 0, '[]', 0),
('BCP/QBL-160', 'Laptop', 'Lenovo', 'T470s', 'PC0NT7PW', 'N/A', '', 0, '[]', 0),
('BCP/QBL-27', 'Laptop', 'Lenovo', 'T460s', 'PC0JYTEA', 'N/A', '', 0, '[]', 0),
('BCP/QBL-29', 'Laptop', 'Lenovo', 'T470s', 'PC0UB2HA', 'N/A', '', 0, '[]', 0),
('BCP/QBL-34', 'Laptop', 'Lenovo', 'T460s', 'PC0MK2D7', 'N/A', '', 0, '[]', 0),
('BCP/QBL-35', 'Laptop', 'Lenovo', 'T470s', 'PC0MEQS5', 'N/A', '', 0, '[]', 0),
('BCP/QBL-36', 'Laptop', 'Lenovo', 'T460s', 'PC0HRBYV', 'N/A', '', 0, '[]', 0),
('BCP/QBL-39', 'Laptop', 'Lenovo', 'T460s', 'PC0J274Q', 'N/A', '', 0, '[]', 0),
('BCP/QBL-40', 'Laptop', 'Lenovo', 'T460s', 'PC0KA4AT', 'N/A', '', 0, '[]', 0),
('BCP/QBL-41', 'Laptop', 'Lenovo', 'T460s', 'PC0HRBY3', 'N/A', '', 0, '[]', 0),
('BCP/QBL-47', 'Laptop', 'Lenovo', 'T460s', 'PC0HR9RL', 'N/A', '', 0, '[]', 0),
('BCP/QBL-50', 'Laptop', 'Lenovo', 'T460s', 'MJ05TBD7', 'N/A', '', 0, '[]', 0),
('BCP/QBL-51', 'Laptop', 'Lenovo', 'T460s', 'PC0FKR7U', 'N/A', '', 0, '[]', 0),
('BCP/QBL-52', 'Laptop', 'Lenovo', 'T460s', 'PC0D7HP5', 'N/A', '', 0, '[]', 0),
('BCP/QBL-56', 'Laptop', 'Lenovo', 'T460s', 'PC0J0DKM', 'N/A', '', 0, '[]', 0),
('BCP/QBL-57', 'Laptop', 'Lenovo', 'T470s', 'PC0P7LLA', 'N/A', '', 0, '[]', 0),
('BCP/QBL-58', 'Laptop', 'Lenovo', 'T460s', 'PC0D7J39', 'N/A', '', 0, '[]', 0),
('BCP/QBL-59', 'Laptop', 'Lenovo', 'T460s', 'PC0J28PR', 'N/A', '', 0, '[]', 0),
('BCP/QBL-60', 'Laptop', 'Lenovo', 'T460s', 'PC0LRGVC', 'N/A', '', 0, '[]', 0),
('BCP/QBL-61', 'Laptop', 'Lenovo', 'T460s', 'PC0J25EK', 'N/A', '', 0, '[]', 0),
('BCP/QBL-62', 'Laptop', 'Lenovo', 'T460s', 'PC0LASSV', 'N/A', '', 0, '[]', 0),
('BCP/QBL-63', 'Laptop', 'Lenovo', 'T460s', 'PC0FGFRA', 'N/A', '', 0, '[]', 0),
('BCP/QBL-64', 'Laptop', 'Lenovo', 'T460s', 'PC0F0Z9M', 'N/A', '', 0, '[]', 0),
('BCP/QBL-65', 'Laptop', 'Lenovo', 'T460s', '', 'N/A', '', 0, '[]', 0),
('BCP/QBL-66', 'Laptop', 'Lenovo', 'T460s', 'PC0J27JE', 'N/A', '', 0, '[]', 0),
('BCP/QBL-67', 'Laptop', 'Lenovo', 'T460s', 'PC0H9K1F', 'N/A', '', 0, '[]', 0),
('BCP/QBL-68', 'Laptop', 'Lenovo', 'T460s', 'PC0H6C92', 'N/A', '', 0, '[]', 0),
('BCP/QBL-69', 'Laptop', 'Lenovo', 'T460s', 'PC0JYTEB', 'N/A', '', 0, '[]', 0),
('BCP/QBL-70', 'Laptop', 'Lenovo', 'T460s', 'PC0EH7XM', 'N/A', '', 0, '[]', 0),
('BCP/QBL-71', 'Laptop', 'Lenovo', 'T460s', 'PC0H68N2', 'N/A', '', 0, '[]', 0),
('BCP/QBL-72', 'Laptop', 'Lenovo', 'T460s', 'PC0L8K4Y', 'N/A', '', 0, '[]', 0),
('BCP/QBL-74', 'Laptop', 'Lenovo', 'T460s', 'PC0J0DK7', 'N/A', '', 0, '[]', 0),
('BCP/QBL-75', 'Laptop', 'Lenovo', 'T460s', 'PC0KA482', 'N/A', '', 0, '[]', 0),
('BCP/QBL-76', 'Laptop', 'Lenovo', 'T470s', 'PC0P7LWY', 'N/A', '', 0, '[]', 0),
('BCP/QBL-77', 'Laptop', 'Lenovo', 'T470s', 'PC0P7LWW', 'N/A', '', 0, '[]', 0),
('BCP/QBL-79', 'Laptop', 'Lenovo', 'T460s', 'PC0J4RWQ', 'N/A', '', 0, '[]', 0),
('BCP/QBL-82', 'Laptop', 'Lenovo', 'T470s', 'PC0MG8VU', 'N/A', '', 0, '[]', 0),
('BCP/QBL-83', 'Laptop', 'Lenovo', 'T460s', 'PC0JYTEF', 'N/A', '', 0, '[]', 0),
('BCP/QBL-84', 'Laptop', 'Lenovo', 'T470s', 'PC0SKVJ1', 'N/A', '', 0, '[]', 0),
('BCP/QBL-85', 'Laptop', 'Lenovo', 'T460s', 'PC0J27U9', 'N/A', '', 0, '[]', 0),
('BCP/QBL-86', 'Laptop', 'Lenovo', 'T460s', 'PC0UQ22U', 'N/A', '', 0, '[]', 0),
('BCP/QBL-87', 'Laptop', 'Lenovo', 'T470s', 'PC0MF9J4', 'N/A', '', 0, '[]', 0),
('BCP/QBL-90', 'Laptop', 'Lenovo', 'T470s', 'PC0U8GB6', 'N/A', '', 0, '[]', 0),
('BCP/QBL-93', 'Laptop', 'Lenovo', 'T460s', 'PC0JZG8V', 'N/A', '', 0, '[]', 0),
('BCP/QBL-94', 'Laptop', 'Lenovo', 'T460s', 'PC0DX3C2', 'N/A', '', 0, '[]', 0),
('BCP/QBL-95', 'Laptop', 'Lenovo', 'T470s', 'PC0SKWXE', 'N/A', '', 0, '[]', 0),
('BCP/QBL-96', 'Laptop', 'Lenovo', 'T460s', 'PC0MK25C', 'N/A', '', 0, '[]', 0),
('BCP/QBL-97', 'Laptop', 'Lenovo', 'T460s', 'PC0J25NV', 'N/A', '', 0, '[]', 0),
('BCP/QBL-99', 'Laptop', 'Lenovo', 'T460s', 'PC0J0EAQ', 'N/A', '', 0, '[]', 0);

-- --------------------------------------------------------

--
-- Table structure for table `slots`
--

CREATE TABLE `slots` (
  `id` int(11) NOT NULL,
  `sn` varchar(255) DEFAULT NULL,
  `date_val` varchar(255) DEFAULT NULL,
  `slotNo` varchar(255) DEFAULT NULL,
  `slotName` varchar(255) DEFAULT NULL,
  `totalAssets` int(11) DEFAULT 0,
  `returnToIT` int(11) DEFAULT 0,
  `eol` int(11) DEFAULT 0,
  `pending` int(11) DEFAULT 0,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `slots`
--

INSERT INTO `slots` (`id`, `sn`, `date_val`, `slotNo`, `slotName`, `totalAssets`, `returnToIT`, `eol`, `pending`, `remarks`) VALUES
(8, '01', '2026-06-04', 's1', 'laptop', 100, 15, 20, 85, ''),
(9, '02', '2026-06-07', 'S2', 'laptop', 20, 10, 22, 2, ''),
(10, '3', '2026-06-08', '22', 'lp', 20, 0, 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `passwordHash` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `requestDate` varchar(100) DEFAULT NULL,
  `lastSeen` bigint(20) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`username`, `email`, `passwordHash`, `role`, `status`, `requestDate`, `lastSeen`) VALUES
('admin', 'admin@assetcare.com', 'U3VwZXJBZG1pbiMyMDI2', 'admin', 'active', '', 1780912083287);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`tag`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_tag` (`tag`);

--
-- Indexes for table `slots`
--
ALTER TABLE `slots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `slots`
--
ALTER TABLE `slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
