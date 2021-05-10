-- phpMyAdmin SQL Dump
-- version 4.8.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 24, 2018 at 02:48 PM
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
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `maintenance` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `master` tinyint(1) NOT NULL DEFAULT '0',
  `encoding` tinyint(1) NOT NULL DEFAULT '0',
  `arrival` tinyint(1) NOT NULL DEFAULT '0',
  `e2m` tinyint(1) NOT NULL DEFAULT '0',
  `gatepass` tinyint(1) NOT NULL DEFAULT '0',
  `storage_validity` tinyint(1) NOT NULL DEFAULT '0',
  `container_movement` tinyint(1) NOT NULL DEFAULT '0',
  `safe_keep` tinyint(1) NOT NULL DEFAULT '0',
  `current_status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `employee_number`, `employee_name`, `password`, `maintenance`, `created_at`, `updated_at`, `master`, `encoding`, `arrival`, `e2m`, `gatepass`, `storage_validity`, `container_movement`, `safe_keep`, `current_status`) VALUES
(1, '28731', 'Jerome Del Rosario', '45b36e5ecc568d49a32b60f80f332b69', 1, NULL, '2018-11-23 21:40:47', 1, 1, 1, 1, 1, 1, 1, 1, 1),
(3, '21305', 'Robin', 'c9b29e8164b79f0977a6010de42fe787', 1, NULL, '2018-11-19 19:58:26', 0, 1, 0, 0, 0, 0, 0, 1, 0),
(4, '19701', 'Rosemarie', '14c7eca07aae3fc68903321ffdbea120', 0, NULL, '2018-11-19 19:58:40', 0, 0, 1, 0, 0, 0, 1, 0, 1),
(5, '25190', 'Lhyn Pacites', 'd31b97335d631727f3e7be457307c3a9', 1, NULL, '2018-11-22 21:31:11', 1, 0, 0, 1, 0, 0, 0, 0, 0),
(6, '35297', 'Jennifer Mirasol', 'ec4f1bd61012641a6eb0aa63cd06cf39', 0, NULL, NULL, 1, 1, 0, 0, 0, 0, 0, 0, 0),
(7, '25038', 'Imeery Julie Samot', '8a58beb9988f8b83e2f4ad93576c2f6a', 1, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(9, 'transpo', 'Transpo', '644cc8caa334d9ef6c93a483088d59b6', 0, NULL, NULL, 0, 0, 0, 0, 0, 0, 1, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
