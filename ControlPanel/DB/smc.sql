-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 15, 2024 at 03:59 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.0

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
  `id` int(255) NOT NULL,
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
(16, 'SFRACC02932', '196.12.150.183', 'Rwanda', 'Southern Province', 'Runda', '', '', '2024-06-11 15:05:31'),
(17, 'SFRACC02932', '41.216.116.237', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-11 16:09:03'),
(18, 'SFRACC02932', '41.216.116.237', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-11 16:14:15'),
(19, 'SFRACC02932', '196.12.138.148', 'Rwanda', 'Southern Province', 'Runda', '', '', '2024-06-14 07:59:38'),
(20, 'SFRACC02932', '196.12.138.148', 'Rwanda', 'Southern Province', 'Runda', '', '', '2024-06-14 10:24:23'),
(21, 'SFRACC02932', '41.216.106.176', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-14 12:57:02'),
(22, 'SFRACC02932', '41.216.106.176', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-14 14:44:07'),
(23, 'SFRACC02932', '41.216.108.67', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-17 09:09:04'),
(24, 'SFRACC02932', '41.216.113.53', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-17 12:27:56'),
(25, 'SFRACC02932', '41.216.113.53', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-17 15:12:05'),
(26, 'SFRACC02932', '41.216.101.33', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-24 08:32:10'),
(27, 'SFRACC02932', '41.216.101.33', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-24 10:05:46'),
(28, 'SFRACC02932', '41.216.101.33', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-24 11:50:54'),
(29, 'SFRACC02932', '41.216.101.33', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-24 14:20:41'),
(30, 'SFRACC02932', '41.216.101.33', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-25 09:05:59'),
(31, 'SFRACC02932', '41.216.101.33', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-25 11:17:59'),
(32, 'SFRACC02932', '196.12.139.182', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-25 18:14:07'),
(46, 'SFRACC02932', '196.12.139.182', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-25 19:08:49'),
(47, 'SFRACC02932', '41.173.248.221', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-28 17:23:40'),
(48, 'SFRACC02932', '41.216.108.164', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-07-01 09:30:48'),
(49, 'SFRACC02932', '41.216.108.164', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-07-01 10:26:30'),
(50, 'SFRACC02932', '41.216.108.164', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-07-01 11:26:40'),
(51, 'SFRACC02932', '41.216.108.164', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-07-01 11:27:18'),
(52, 'SFRACC02932', '41.216.116.131', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-07-01 15:15:25'),
(53, 'SFRACC02932', '41.216.116.131', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-07-01 15:18:39'),
(54, 'SFRACC02932', '41.216.116.131', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-07-02 08:55:01'),
(55, 'SFRACC02932', '41.216.107.208', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-07-02 09:46:15'),
(56, 'SFRACC02932', '41.216.107.208', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-07-02 11:27:14'),
(57, 'SFRACC02932', '41.216.107.208', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-07-02 12:53:39'),
(58, 'SFRACC02932', '41.216.107.208', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-07-02 15:17:58'),
(59, 'SFRACC02932', '41.216.107.208', 'Rwanda', 'Kigali City', 'Kigali', '-1.9705786', '30.1044288', '2024-07-02 15:51:07'),
(60, 'SFRACC02932', '41.216.107.208', 'Rwanda', 'Kigali City', 'Kigali', '-1.9705786', '30.1044288', '2024-07-02 16:27:40'),
(61, 'SFRACC02932', '41.216.107.208', 'Rwanda', 'Kigali City', 'Kigali', '-1.9705786', '30.1044288', '2024-07-02 17:42:00'),
(62, 'SFRACC02932', '41.216.117.63', 'Rwanda', 'Kigali City', 'Kigali', '-1.9705786', '30.1044288', '2024-07-03 16:30:34');

-- --------------------------------------------------------

--
-- Table structure for table `account_holders`
--

CREATE TABLE `account_holders` (
  `id` int(255) NOT NULL,
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
  `token` varchar(255) NOT NULL,
  `generated_time` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account_holders`
--

INSERT INTO `account_holders` (`id`, `account_id`, `firstname`, `lastname`, `email`, `username`, `password`, `privilege`, `last_access`, `status`, `profile`, `token`, `generated_time`) VALUES
(1, 'SFRACC02932', 'William', 'CAMPBELL', 'manzi9.bertin@gmail.com', 'sample', '$2y$10$7tYySqDnZ5QCBWPEmOfBB.U5l2/cvW0AF8p7idAduXYB/E4oE.CVq', 'management', '07-03-2024 07:14:01', 'active', 'user.jpg', 'used', ''),
(2, 'SFRACC15263', 'Anthony', 'CAMPBELL', 'manzi9.bertin@gmail.com', 'teacher@mail.com', '$2y$10$gdPO7UWrXirg4jvf49kCYOw./3b3Hu6FNv7K1pt4pk6NpECU.F4Oe', 'teacher', '07-03-2024 07:14:01', 'active', 'face1.png', '65e4b6b63be39380cfd21a132f697f3a', ''),
(3, 'SFRACC15432', 'Moses', 'THEDDY', 'xprince250@gmail.com', 'student@mail.com', '$2y$10$uPkTla5x6fHg3bjar0kL0uU5LAoQRn6dhjBk/jWa8V03Rf2Wpm5dG', 'teacher', '07-03-2024 07:14:01', 'active', 'face2.jpg', '604cf4e6cf81f0c20114528868d342da', '10:53:23 01-07-2024'),
(4, 'SFRACC43442', 'Ange', 'SYMPHONY', 'library@mail.com', 'library@mail.com', '$2y$10$gdPO7UWrXirg4jvf49kCYOw./3b3Hu6FNv7K1pt4pk6NpECU.F4Oe', 'librarian', '07-03-2024 07:14:01', 'active', 'face2.jpg', 'used', '');

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `note` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `code`, `name`, `email`, `phone`, `address`, `note`, `date`) VALUES
(1, 'CF4744', 'MUGISHA Prince', 'xprince250@gmail.com', '0780614437', 'Gasaro', 'Njyewe sinzi Evaliste nshaka amata', '2024-06-24 18:56:46'),
(2, 'CF4211', 'MUGIHSA Prince', 'xsolution250@gmail.com', '0788223042', 'Rwimbazi', 'Welcome to the store that are availabler', '2024-06-24 19:07:13'),
(3, 'CF3646', 'MUGISHA Prince', 'xprince250@gmail.com', '0788223042', 'Kagugu', 'Welcome to the store', '2024-06-24 19:12:38'),
(4, 'CF941', 'MUGIHSA Prince', 'xprince250@gmail.com', '0780614437', 'Kicukiro', 'Welcome to the testing panel', '2024-07-03 16:28:35');

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

CREATE TABLE `file` (
  `id` int(11) NOT NULL,
  `file_code` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `file`
--

INSERT INTO `file` (`id`, `file_code`, `file`, `date`) VALUES
(4, 'PR9189', 'FILE529_1.png', '2024-06-14 13:03:39'),
(5, 'PR6462', 'FILE5853_1.png', '2024-06-14 13:12:05'),
(6, 'PR3197', 'FILE9792_Screenshot 2024-06-09 115946.png', '2024-06-14 14:26:22'),
(9, 'FILE7825', 'upload/news/Array', '2024-06-17 15:10:29'),
(10, 'FILE8987', 'upload/news/Array', '2024-06-17 15:10:29'),
(11, 'FILE5241', 'upload/news/Screenshot 2024-06-16 220504.png', '2024-06-17 15:12:39'),
(12, 'NW3552', 'upload/news/1.png', '2024-06-17 15:16:09'),
(13, 'Screenshot 2024-06-22 153412.png', 'FILE', '2024-06-24 10:55:01'),
(14, 'Screenshot 2024-06-22 153358.png', 'FILE', '2024-06-24 10:57:04'),
(15, 'Screenshot 2024-06-22 153412.png', 'FILE', '2024-06-24 10:57:04'),
(16, 'Screenshot 2024-06-22 001238.png', 'FILE', '2024-06-24 10:57:04'),
(17, 'Screenshot 2024-06-21 120601.png', 'FILE', '2024-06-24 10:57:04'),
(18, 'Screenshot 2024-06-22 153358.png', 'FILE', '2024-06-24 10:58:12'),
(19, 'Screenshot 2024-06-22 153412.png', 'FILE', '2024-06-24 10:58:12'),
(20, 'Screenshot 2024-06-22 001238.png', 'FILE', '2024-06-24 10:58:12'),
(21, 'Screenshot 2024-06-21 120601.png', 'FILE', '2024-06-24 10:58:12'),
(22, 'Screenshot 2024-06-22 153412.png', 'FILE', '2024-06-24 10:58:42'),
(23, 'GAd39346a0', 'Screenshot 2024-06-22 153412.png', '2024-06-24 11:05:12'),
(24, 'GA698f41a4', 'Screenshot 2024-06-22 153358.png', '2024-06-24 11:05:12'),
(25, 'GAaafb8256', 'Screenshot 2024-06-22 001238.png', '2024-06-24 11:05:12'),
(27, 'NW133', 'FILE4205Screenshot 2024-06-21 120601.png', '2024-06-24 11:52:04'),
(28, 'NW133', 'FILE6836Screenshot 2024-06-17 141005.png', '2024-06-24 11:52:04'),
(29, 'PR7987', 'FILE1957_Screenshot 2024-06-18 083746.png', '2024-06-24 12:03:51'),
(30, 'NW9865', 'FILE7439_Screenshot 2024-06-18 114334.png', '2024-06-24 14:28:11'),
(31, 'NW9865', 'FILE2944_Screenshot 2024-06-18 210313.png', '2024-06-24 14:28:19'),
(32, 'GAa08a821e', 'Screenshot 2024-06-25 111027.png', '2024-06-25 09:15:14'),
(33, 'GA4fabcb8d', 'Screenshot 2024-06-22 153412.png', '2024-06-25 09:15:14'),
(34, 'GA0ff9cedc', 'Screenshot 2024-06-22 153358.png', '2024-06-25 09:15:14'),
(35, 'GAc78ddfdc', 'Screenshot 2024-06-22 001238.png', '2024-06-25 09:15:14'),
(36, 'NW6653', 'FILE7487_Screenshot 2024-06-18 210235.png', '2024-06-25 18:56:18'),
(37, 'NW6653', 'FILE6897_Screenshot 2024-06-18 201133.png', '2024-06-25 18:56:18'),
(38, 'NW6653', 'FILE3018_Screenshot 2024-06-18 210235.png', '2024-06-25 18:56:18'),
(39, 'NW6653', 'FILE3137_Screenshot 2024-06-18 210313.png', '2024-06-25 18:56:18'),
(40, 'NW6653', 'FILE3281_Screenshot 2024-06-19 073210.png', '2024-06-25 18:56:18');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `code`, `date`) VALUES
(1, 'GAd39346a0', '2024-06-24 11:05:12'),
(2, 'GA698f41a4', '2024-06-24 11:05:12'),
(3, 'GAaafb8256', '2024-06-24 11:05:12'),
(5, 'GAa08a821e', '2024-06-25 09:15:14'),
(6, 'GA4fabcb8d', '2024-06-25 09:15:14'),
(7, 'GA0ff9cedc', '2024-06-25 09:15:14'),
(8, 'GAc78ddfdc', '2024-06-25 09:15:14');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(100) NOT NULL,
  `code` varchar(100) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `code`, `title`, `content`, `created_at`) VALUES
(15, 'NW2569', 'Welcome to the store', '&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer venenatis risus gravida auctor convallis. Sed nec enim id mauris volutpat pulvinar. Fusce consectetur est nulla, in faucibus dui euismod in. Donec id tortor sapien. Donec non rutrum justo. Aliquam erat volutpat. Nullam pellentesque condimentum ligula eget vehicula. Etiam aliquet efficitur velit. Nam pulvinar consequat congue. Fusce at velit sit amet nunc egestas consequat. In convallis sapien pellentesque metus viverra mollis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Cras gravida imperdiet arcu ut blandit. Donec sed mauris ultrices, dapibus velit quis, gravida nulla. Pellentesque at pellentesque massa, feugiat egestas ex. Nulla eu ipsum quis risus interdum venenatis.&lt;/p&gt;\r\n&lt;p&gt;Ut semper dolor nec tortor ornare, at lobortis nisi sollicitudin. Vestibulum urna dui, facilisis sit amet facilisis vel, ornare in ipsum. Fusce porta libero eu justo lacinia pellentesque. Etiam vitae neque ut massa ullamcorper posuere in eget tellus. Nunc vel nunc porta, scelerisque nisi sed, luctus purus. Vestibulum elementum augue eget convallis aliquet. Etiam sit amet imperdiet magna, vitae mattis ex. Vivamus quis rhoncus elit. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam egestas ultrices ultricies. Mauris felis massa, efficitur a pellentesque vitae, pharetra eget libero. Aenean lacinia leo vel ultrices pulvinar. Cras pharetra dolor arcu, ac placerat lorem egestas vel. Duis lacinia nisi sem, at tempor quam ornare non. Nam vel lectus quis quam eleifend dapibus sit amet eu elit.&lt;/p&gt;\r\n&lt;blockquote&gt;\r\n&lt;p&gt;Cras quis ullamcorper nunc, et consectetur est. Duis pulvinar mollis nisi, at viverra massa interdum ut. Quisque faucibus aliquam quam rhoncus posuere. Ut id nunc iaculis sem faucibus porttitor. Mauris pharetra dapibus elementum. Aliquam erat volutpat. Aliquam sed odio lectus. Donec pulvinar justo nec imperdiet bibendum.&lt;/p&gt;\r\n&lt;/blockquote&gt;', '2024-06-17 14:32:18'),
(34, 'NW5296', 'Bertin is the one who is organising ', '&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer venenatis risus gravida auctor convallis. Sed nec enim id mauris volutpat pulvinar. Fusce consectetur est nulla, in faucibus dui euismod in. Donec id tortor sapien. Donec non rutrum justo. Aliquam erat volutpat. Nullam pellentesque condimentum ligula eget vehicula. Etiam aliquet efficitur velit. Nam pulvinar consequat congue. Fusce at velit sit amet nunc egestas consequat. In convallis sapien pellentesque metus viverra mollis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Cras gravida imperdiet arcu ut blandit. Donec sed mauris ultrices, dapibus velit quis, gravida nulla. Pellentesque at pellentesque massa, feugiat egestas ex. Nulla eu ipsum quis risus interdum venenatis.&lt;/p&gt;\r\n&lt;p&gt;Ut semper dolor nec tortor ornare, at lobortis nisi sollicitudin. Vestibulum urna dui, facilisis sit amet facilisis vel, ornare in ipsum. Fusce porta libero eu justo lacinia pellentesque. Etiam vitae neque ut massa ullamcorper posuere in eget tellus. Nunc vel nunc porta, scelerisque nisi sed, luctus purus. Vestibulum elementum augue eget convallis aliquet. Etiam sit amet imperdiet magna, vitae mattis ex. Vivamus quis rhoncus elit. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam egestas ultrices ultricies. Mauris felis massa, efficitur a pellentesque vitae, pharetra eget libero. Aenean lacinia leo vel ultrices pulvinar. Cras pharetra dolor arcu, ac placerat lorem egestas vel. Duis lacinia nisi sem, at tempor quam ornare non. Nam vel lectus quis quam eleifend dapibus sit amet eu elit.&lt;/p&gt;\r\n&lt;p&gt;Cras quis ullamcorper nunc, et consectetur est. Duis pulvinar mollis nisi, at viverra massa interdum ut. Quisque faucibus aliquam quam rhoncus posuere. Ut id nunc iaculis sem faucibus porttitor. Mauris pharetra dapibus elementum. Aliquam erat volutpat. Aliquam sed odio lectus. Donec pulvinar justo nec imperdiet bibendum.&lt;/p&gt;', '2024-06-17 15:10:29'),
(35, 'NW3552', 'This is all american site', '&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer venenatis risus gravida auctor convallis. Sed nec enim id mauris volutpat pulvinar. Fusce consectetur est nulla, in faucibus dui euismod in. Donec id tortor sapien. Donec non rutrum justo. Aliquam erat volutpat. Nullam pellentesque condimentum ligula eget vehicula. Etiam aliquet efficitur velit. Nam pulvinar consequat congue. Fusce at velit sit amet nunc egestas consequat. In convallis sapien pellentesque metus viverra mollis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Cras gravida imperdiet arcu ut blandit. Donec sed mauris ultrices, dapibus velit quis, gravida nulla. Pellentesque at pellentesque massa, feugiat egestas ex. Nulla eu ipsum quis risus interdum venenatis.&lt;/p&gt;\r\n&lt;p&gt;Ut semper dolor nec tortor ornare, at lobortis nisi sollicitudin. Vestibulum urna dui, facilisis sit amet facilisis vel, ornare in ipsum. Fusce porta libero eu justo lacinia pellentesque. Etiam vitae neque ut massa ullamcorper posuere in eget tellus. Nunc vel nunc porta, scelerisque nisi sed, luctus purus. Vestibulum elementum augue eget convallis aliquet. Etiam sit amet imperdiet magna, vitae mattis ex. Vivamus quis rhoncus elit. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam egestas ultrices ultricies. Mauris felis massa, efficitur a pellentesque vitae, pharetra eget libero. Aenean lacinia leo vel ultrices pulvinar. Cras pharetra dolor arcu, ac placerat lorem egestas vel. Duis lacinia nisi sem, at tempor quam ornare non. Nam vel lectus quis quam eleifend dapibus sit amet eu elit.&lt;/p&gt;\r\n&lt;p&gt;Cras quis ullamcorper nunc, et consectetur est. Duis pulvinar mollis nisi, at viverra massa interdum ut. Quisque faucibus aliquam quam rhoncus posuere. Ut id nunc iaculis sem faucibus porttitor. Mauris pharetra dapibus elementum. Aliquam erat volutpat. Aliquam sed odio lectus. Donec pulvinar justo nec imperdiet bibendum.&lt;/p&gt;', '2024-06-17 15:12:39'),
(36, 'NW133', 'We are the all new generation', '&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis ut eros vehicula, accumsan odio sit amet, consequat tellus. Fusce ac convallis arcu. Nam ut laoreet justo. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent ornare purus et dui viverra dignissim. Vivamus semper ullamcorper luctus. Duis et mauris at enim pretium pulvinar at vel velit.&lt;/p&gt;\r\n&lt;p&gt;Curabitur urna lorem, tempor sit amet faucibus vel, interdum id nunc. Nulla eros risus, consequat in ipsum eget, rutrum faucibus nibh. Maecenas imperdiet feugiat ornare. Nulla et lectus fermentum, cursus velit eget, sodales felis. Vestibulum convallis iaculis mollis. Aenean orci sem, tincidunt ac massa et, feugiat ultricies dolor. Vestibulum sollicitudin sem nisi, pretium semper ipsum iaculis sit amet. Integer at leo mi. Sed arcu turpis, pulvinar at pellentesque eget, fringilla ac lacus. Fusce non metus felis. Morbi a dignissim ante. Phasellus massa elit, malesuada ac eleifend in, tristique eget metus. Quisque laoreet sem consectetur, euismod lectus nec, mattis leo. Praesent non nunc dui. Praesent id porta lacus, ac suscipit felis.&lt;/p&gt;\r\n&lt;p&gt;In at neque lectus. Maecenas at bibendum augue, et blandit dui. Nullam elementum arcu in scelerisque elementum. Nunc fermentum in erat vitae rutrum. Pellentesque faucibus a massa vitae sagittis. Vivamus consectetur justo dolor, nec congue velit tempor vitae. Sed venenatis purus vel iaculis mollis. Aliquam sit amet vehicula dui, et molestie arcu. Nullam congue tempus nulla. Sed eu sem purus. Sed volutpat ligula non semper ultrices. Fusce lorem neque, blandit ut elit nec, faucibus dapibus eros. Curabitur laoreet quis lectus sed sodales. Nunc blandit lectus non nulla mollis consectetur. Donec consequat ipsum eros, eget rutrum mauris laoreet non.&lt;/p&gt;', '2024-06-24 11:52:04'),
(37, 'NW9865', 'We the best ', '&lt;p style=&quot;margin-right: 0px; margin-bottom: 15px; margin-left: 0px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif;&quot;&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec ornare eget eros a laoreet. Praesent ultrices metus sit amet luctus tristique. Pellentesque vestibulum porta magna, ut luctus justo tincidunt eu. Curabitur fermentum feugiat ligula vitae ultricies. Nullam mollis turpis ultricies magna facilisis lobortis. Nam auctor tellus sapien, nec ultrices nulla tincidunt in. Integer et tortor turpis. Nam sed mi aliquet elit porttitor varius vitae ut felis. Donec et massa odio. Fusce condimentum, nibh eget bibendum aliquam, massa eros facilisis magna, non interdum lacus odio vitae ipsum. Donec eu nibh varius lorem ornare vestibulum ac quis nibh. Duis feugiat sodales magna, sed semper arcu tincidunt sit amet. Fusce ac vestibulum mauris.&lt;/p&gt;&lt;p style=&quot;margin-right: 0px; margin-bottom: 15px; margin-left: 0px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif;&quot;&gt;Quisque at faucibus quam, a luctus eros. Proin fermentum lacus at tortor condimentum vehicula. Integer sollicitudin tincidunt efficitur. Mauris vulputate sagittis dictum. Maecenas neque turpis, efficitur quis magna ac, vehicula bibendum nisl. Suspendisse ac nisl nulla. Donec porta dolor a cursus egestas. Maecenas dolor neque, eleifend ut posuere vitae, venenatis quis purus. Donec at imperdiet nunc, et iaculis quam. Mauris ex nisl, porta sed ultricies sit amet, fringilla sit amet nulla. Sed quis porta nisi. Suspendisse tincidunt diam scelerisque, faucibus mi ut, tempor orci. Nam risus leo, posuere et rhoncus eu, ullamcorper a dolor. Nulla ipsum ex, aliquam vitae viverra eu, dignissim quis urna. Maecenas iaculis tortor vitae nibh egestas, et pellentesque lacus ullamcorper. Integer tristique dapibus elit, in dignissim nisl cursus a.&lt;/p&gt;&lt;p style=&quot;margin-right: 0px; margin-bottom: 15px; margin-left: 0px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif;&quot;&gt;Donec faucibus et arcu non egestas. Sed vel massa vitae nibh luctus pharetra eu nec velit. Nam fringilla porttitor molestie. Aliquam sit amet suscipit orci. Nam tincidunt massa leo, ac interdum dui iaculis sit amet. Maecenas sed erat semper, molestie metus nec, rhoncus erat. Etiam auctor porta ante, id imperdiet nulla placerat eget. Nullam orci libero, fringilla eget facilisis a, porttitor ut libero. Sed commodo euismod odio, eget efficitur erat sodales at. Curabitur varius arcu quis nulla scelerisque vestibulum.&lt;/p&gt;', '2024-06-25 16:07:10'),
(47, 'NW6653', 'We the greate generation', '&lt;p style=&quot;margin-right: 0px; margin-bottom: 15px; margin-left: 0px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif;&quot;&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam efficitur rhoncus nisl, ac consectetur justo commodo vitae. Vestibulum vel pulvinar velit. Nulla condimentum commodo risus non viverra. Integer ac molestie nunc. Mauris elementum vestibulum molestie. Aenean sit amet felis sapien. Sed ultrices mollis vehicula. Sed efficitur vulputate lorem et blandit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Sed tortor lectus, ornare vitae mauris in, porta porttitor turpis. Fusce condimentum a risus sit amet egestas. Etiam ullamcorper viverra nisi cursus varius. Ut venenatis risus dolor, sed rhoncus mauris facilisis vel. Cras posuere lobortis turpis.&lt;/p&gt;&lt;p style=&quot;margin-right: 0px; margin-bottom: 15px; margin-left: 0px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif;&quot;&gt;Ut vitae ante vitae quam luctus euismod. Vestibulum malesuada turpis nulla, vitae viverra odio maximus nec. Maecenas non quam ac velit rutrum posuere. Mauris accumsan ligula quis lacus lobortis aliquam. Fusce eleifend orci malesuada nisi varius, vestibulum iaculis nisl tempor. Phasellus congue ante at orci congue sagittis. Mauris hendrerit sed magna at tempus. Integer consequat eget augue id venenatis. Curabitur in maximus sapien. Aenean sit amet suscipit urna, non efficitur libero.&lt;/p&gt;&lt;p style=&quot;margin-right: 0px; margin-bottom: 15px; margin-left: 0px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif;&quot;&gt;Duis et ante posuere, feugiat neque vitae, fringilla tellus. Fusce vel convallis diam, eu ultrices diam. Vivamus quis nibh et sem porttitor lobortis eget vulputate dolor. Quisque eu nisl quis mi congue sollicitudin. Fusce sit amet augue erat. Cras consequat urna tortor, et aliquam enim rhoncus eget. In luctus porttitor turpis, at hendrerit mauris pharetra sed. Ut quis pharetra quam. Donec nec suscipit lacus. Ut risus nisl, bibendum eu est eget, venenatis vehicula ipsum. Pellentesque consequat bibendum mi, non luctus massa feugiat non. Sed malesuada mattis consectetur.&lt;/p&gt;', '2024-06-25 18:56:18');

-- --------------------------------------------------------

--
-- Table structure for table `newslater`
--

CREATE TABLE `newslater` (
  `id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `newslater`
--

INSERT INTO `newslater` (`id`, `code`, `email`, `created_at`) VALUES
(1, 'NL3612', 'xprince250@gmail.com', '2024-06-24 18:24:21'),
(2, 'NL2062', 'manzi9.bertin@gmail.com', '2024-06-28 16:37:56'),
(3, 'NL7712', 'usabuwerakendlyquemar@gmail.com', '2024-07-03 16:29:27');

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `id` int(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`id`, `code`, `title`, `content`, `date`) VALUES
(4, 'PR9189', 'Welcome to the store', 'sdjhkl hgjhbjhb&nbsp;', '2024-06-14 13:03:39'),
(5, 'PR6462', 'This is all american site', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean mollis nisi sit amet elit vestibulum fermentum. Sed vestibulum congue neque, finibus viverra dui volutpat vitae. Suspendisse ut posuere mi, sit amet porttitor velit. In lectus ipsum, posuere vitae auctor non, fermentum at nulla. Donec ut dignissim dolor. Donec consectetur aliquet luctus. Mauris porta in velit sit amet aliquet. Vivamus et neque mi. Nullam leo arcu, placerat sed faucibus ac, pellentesque non tortor.', '2024-06-14 13:12:05'),
(6, 'PR3197', 'Bertin is the one who is organising ', 'Something is the data based below', '2024-06-14 14:26:22'),
(9, 'PR7987', 'Sample American', '&lt;blockquote style=&quot;margin-right: 0px; margin-bottom: 15px; margin-left: 0px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif;&quot; class=&quot;blockquote&quot;&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis ut eros vehicula, accumsan odio sit amet, consequat tellus. Fusce ac convallis arcu. Nam ut laoreet justo. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent ornare purus et dui viverra dignissim. Vivamus semper ullamcorper luctus. Duis et mauris at enim pretium pulvinar at vel velit.&lt;/blockquote&gt;&lt;p style=&quot;margin-right: 0px; margin-bottom: 15px; margin-left: 0px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif;&quot;&gt;Curabitur urna lorem, tempor sit amet faucibus vel, interdum id nunc. Nulla eros risus, consequat in ipsum eget, rutrum faucibus nibh. Maecenas imperdiet feugiat ornare. Nulla et lectus fermentum, cursus velit eget, sodales felis. Vestibulum convallis iaculis mollis. Aenean orci sem, tincidunt ac massa et, feugiat ultricies dolor. Vestibulum sollicitudin sem nisi, pretium semper ipsum iaculis sit amet. Integer at leo mi. Sed arcu turpis, pulvinar at pellentesque eget, fringilla ac lacus. Fusce non metus felis. Morbi a dignissim ante. Phasellus massa elit, malesuada ac eleifend in, tristique eget metus. Quisque laoreet sem consectetur, euismod lectus nec, mattis leo. Praesent non nunc dui. Praesent id porta lacus, ac suscipit felis.&lt;/p&gt;&lt;p style=&quot;margin-right: 0px; margin-bottom: 15px; margin-left: 0px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif;&quot;&gt;In at neque lectus. Maecenas at bibendum augue, et blandit dui. Nullam elementum arcu in scelerisque elementum. Nunc fermentum in erat vitae rutrum. Pellentesque faucibus a massa vitae sagittis. Vivamus consectetur justo dolor, nec congue velit tempor vitae. Sed venenatis purus vel iaculis mollis. Aliquam sit amet vehicula dui, et molestie arcu. Nullam congue tempus nulla. Sed eu sem purus. Sed volutpat ligula non semper ultrices. Fusce lorem neque, blandit ut elit nec, faucibus dapibus eros. Curabitur laoreet quis lectus sed sodales. Nunc blandit lectus non nulla mollis consectetur. Donec consequat ipsum eros, eget rutrum mauris laoreet non.&lt;/p&gt;', '2024-06-24 12:03:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_logs`
--
ALTER TABLE `access_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `account_holders`
--
ALTER TABLE `account_holders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `file`
--
ALTER TABLE `file`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newslater`
--
ALTER TABLE `newslater`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_logs`
--
ALTER TABLE `access_logs`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `account_holders`
--
ALTER TABLE `account_holders`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `file`
--
ALTER TABLE `file`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `newslater`
--
ALTER TABLE `newslater`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
