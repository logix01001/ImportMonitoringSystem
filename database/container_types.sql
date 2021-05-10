-- phpMyAdmin SQL Dump
-- version 4.8.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 24, 2018 at 02:47 PM
-- Server version: 5.7.22
-- PHP Version: 7.2.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laravel_logistic_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `container_types`
--

CREATE TABLE `container_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `added_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `added_cpu_used` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `added_ip_used` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_cpu_used` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_ip_used` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `container_types`
--

INSERT INTO `container_types` (`id`, `name`, `added_by`, `added_cpu_used`, `added_ip_used`, `deleted_at`, `deleted_by`, `deleted_cpu_used`, `deleted_ip_used`, `created_at`, `updated_at`) VALUES
(1, '20 FR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, '20 HC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, '20 OT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, '20 RF', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, '20 STD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, '20 TK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, '40 HC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, '40 RF', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, '40 STD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `container_types`
--
ALTER TABLE `container_types`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `container_types`
--
ALTER TABLE `container_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
