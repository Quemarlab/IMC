-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2024 at 08:37 PM
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
-- Database: `smc`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_logs`
--

CREATE TABLE `access_logs` (
  `id` int(255) NOT NULL DEFAULT 0,
  `account_id` varchar(255) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `region` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `access_logs`
--

INSERT INTO `access_logs` (`id`, `account_id`, `ip_address`, `country`, `region`, `city`, `latitude`, `longitude`, `time`) VALUES
(1, 'SFRACC02932', '197.157.145.61', 'Rwanda', 'Northern Province', 'Byumba', '-1.5791079', '30.0694123', '2024-04-02 18:55:00'),
(2, 'SFRACC02932', '197.157.165.154', 'Rwanda', 'Northern Province', 'Byumba', '-1.5791079', '30.0694123', '2024-04-02 19:54:18'),
(3, 'SFRACC02932', '197.157.165.154', 'Rwanda', 'Northern Province', 'Byumba', '-1.5791079', '30.0694123', '2024-04-02 19:55:12'),
(4, 'SFRACC02932', '197.157.145.61', 'Rwanda', 'Northern Province', 'Byumba', '-1.5791079', '30.0694123', '2024-04-02 20:49:38'),
(5, 'SFRACC02932', '197.157.145.61', 'Rwanda', 'Northern Province', 'Byumba', '-1.5791079', '30.0694123', '2024-04-02 20:51:30'),
(6, 'SFRACC02932', '197.157.135.89', 'Rwanda', 'Kigali City', 'Kigali', '-1.9705786', '30.1044288', '2024-04-03 11:53:16'),
(7, 'SFRACC02932', '197.157.135.89', 'Rwanda', 'Kigali City', 'Kigali', '-1.9705786', '30.1044288', '2024-04-03 13:10:04'),
(8, 'SFRACC02932', '197.157.155.150', 'Rwanda', 'Northern Province', 'Byumba', '-1.5791079', '30.0694123', '2024-04-04 01:46:19'),
(9, 'SFRACC02932', '197.157.155.150', 'Rwanda', 'Northern Province', 'Byumba', '-1.5791079', '30.0694123', '2024-04-04 02:44:13'),
(10, 'SFRACC02932', '196.12.147.120', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-10 08:36:59'),
(11, 'SFRACC02932', '196.12.147.120', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-10 09:26:53'),
(12, 'SFRACC02932', '196.12.147.120', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-10 11:46:24'),
(13, 'SFRACC02932', '196.12.147.120', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-10 15:07:50'),
(14, 'SFRACC02932', '41.216.108.175', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-10 16:13:58'),
(15, 'SFRACC02932', '41.216.108.175', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-10 16:18:17'),
(0, 'SFRACC02932', '196.12.150.183', 'Rwanda', 'Southern Province', 'Runda', '', '', '2024-06-11 15:05:31'),
(0, 'SFRACC02932', '41.216.116.237', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-11 16:09:03'),
(0, 'SFRACC02932', '41.216.116.237', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-11 16:14:15');

-- --------------------------------------------------------

--
-- Table structure for table `account_holders`
--

CREATE TABLE `account_holders` (
  `id` int(255) NOT NULL DEFAULT 0,
  `account_id` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `privilege` varchar(255) NOT NULL,
  `last_access` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `profile` varchar(255) NOT NULL,
  `forgot_code` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account_holders`
--

INSERT INTO `account_holders` (`id`, `account_id`, `firstname`, `lastname`, `email`, `username`, `password`, `privilege`, `last_access`, `status`, `profile`, `forgot_code`) VALUES
(1, 'SFRACC02932', 'William', 'CAMPBELL', 'sample@mail.com', 'sample@mail.com', '$2y$10$.Gii6ipfWj0Rv4mxztAsYOwlmqFZyhX9WWsghdVFJ7nj2fgeTxr3W', 'management', 'online', 'active', 'face1.png', 'used'),
(2, 'SFRACC15263', 'Anthony', 'CAMPBELL', 'teacher@mail.com', 'teacher@mail.com', '$2y$10$gdPO7UWrXirg4jvf49kCYOw./3b3Hu6FNv7K1pt4pk6NpECU.F4Oe', 'teacher', 'online', 'active', 'face1.png', '227237'),
(3, 'SFRACC15432', 'Moses', 'THEDDY', 'teacher2@mail.com', 'student@mail.com', '$2y$10$gdPO7UWrXirg4jvf49kCYOw./3b3Hu6FNv7K1pt4pk6NpECU.F4Oe', 'teacher', 'online', 'active', 'face2.jpg', '434324'),
(4, 'SFRACC43442', 'Ange', 'SYMPHONY', 'library@mail.com', 'library@mail.com', '$2y$10$gdPO7UWrXirg4jvf49kCYOw./3b3Hu6FNv7K1pt4pk6NpECU.F4Oe', 'librarian', 'online', 'active', 'face2.jpg', 'used');

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `id` int(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
