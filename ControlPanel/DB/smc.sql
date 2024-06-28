-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 21, 2024 at 10:04 AM
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
(0, 'SFRACC02932', '41.216.116.237', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-11 16:14:15'),
(0, 'SFRACC02932', '196.12.138.148', 'Rwanda', 'Southern Province', 'Runda', '', '', '2024-06-14 07:59:38'),
(0, 'SFRACC02932', '196.12.138.148', 'Rwanda', 'Southern Province', 'Runda', '', '', '2024-06-14 10:24:23'),
(0, 'SFRACC02932', '41.216.106.176', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-14 12:57:02'),
(0, 'SFRACC02932', '41.216.106.176', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-14 14:44:07'),
(0, 'SFRACC02932', '41.216.108.67', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-17 09:09:04'),
(0, 'SFRACC02932', '41.216.113.53', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-17 12:27:56'),
(0, 'SFRACC02932', '41.216.113.53', 'Rwanda', 'Kigali City', 'Kigali', '', '', '2024-06-17 15:12:05');

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
(1, 'SFRACC02932', 'William', 'CAMPBELL', 'sample@mail.com', 'sample@mail.com', '$2y$10$.Gii6ipfWj0Rv4mxztAsYOwlmqFZyhX9WWsghdVFJ7nj2fgeTxr3W', 'management', '06-17-2024 05:22:41', 'active', 'face1.png', 'used'),
(2, 'SFRACC15263', 'Anthony', 'CAMPBELL', 'teacher@mail.com', 'teacher@mail.com', '$2y$10$gdPO7UWrXirg4jvf49kCYOw./3b3Hu6FNv7K1pt4pk6NpECU.F4Oe', 'teacher', '06-17-2024 05:22:41', 'active', 'face1.png', '227237'),
(3, 'SFRACC15432', 'Moses', 'THEDDY', 'teacher2@mail.com', 'student@mail.com', '$2y$10$gdPO7UWrXirg4jvf49kCYOw./3b3Hu6FNv7K1pt4pk6NpECU.F4Oe', 'teacher', '06-17-2024 05:22:41', 'active', 'face2.jpg', '434324'),
(4, 'SFRACC43442', 'Ange', 'SYMPHONY', 'library@mail.com', 'library@mail.com', '$2y$10$gdPO7UWrXirg4jvf49kCYOw./3b3Hu6FNv7K1pt4pk6NpECU.F4Oe', 'librarian', '06-17-2024 05:22:41', 'active', 'face2.jpg', 'used');

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
(12, 'NW3552', 'upload/news/1.png', '2024-06-17 15:16:09');

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
(14, 'NW6862', 'Bertin is the one who is organising ', '&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer venenatis risus gravida auctor convallis. Sed nec enim id mauris volutpat pulvinar. Fusce consectetur est nulla, in faucibus dui euismod in. Donec id tortor sapien. Donec non rutrum justo. Aliquam erat volutpat. Nullam pellentesque condimentum ligula eget vehicula. Etiam aliquet efficitur velit. Nam pulvinar consequat congue. Fusce at velit sit amet nunc egestas consequat. In convallis sapien pellentesque metus viverra mollis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Cras gravida imperdiet arcu ut blandit. Donec sed mauris ultrices, dapibus velit quis, gravida nulla. Pellentesque at pellentesque massa, feugiat egestas ex. Nulla eu ipsum quis risus interdum venenatis.&lt;/p&gt;\r\n&lt;p&gt;Ut semper dolor nec tortor ornare, at lobortis nisi sollicitudin. Vestibulum urna dui, facilisis sit amet facilisis vel, ornare in ipsum. Fusce porta libero eu justo lacinia pellentesque. Etiam vitae neque ut massa ullamcorper posuere in eget tellus. Nunc vel nunc porta, scelerisque nisi sed, luctus purus. Vestibulum elementum augue eget convallis aliquet. Etiam sit amet imperdiet magna, vitae mattis ex. Vivamus quis rhoncus elit. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam egestas ultrices ultricies. Mauris felis massa, efficitur a pellentesque vitae, pharetra eget libero. Aenean lacinia leo vel ultrices pulvinar. Cras pharetra dolor arcu, ac placerat lorem egestas vel. Duis lacinia nisi sem, at tempor quam ornare non. Nam vel lectus quis quam eleifend dapibus sit amet eu elit.&lt;/p&gt;\r\n&lt;blockquote&gt;\r\n&lt;p&gt;Cras quis ullamcorper nunc, et consectetur est. Duis pulvinar mollis nisi, at viverra massa interdum ut. Quisque faucibus aliquam quam rhoncus posuere. Ut id nunc iaculis sem faucibus porttitor. Mauris pharetra dapibus elementum. Aliquam erat volutpat. Aliquam sed odio lectus. Donec pulvinar justo nec imperdiet bibendum.&lt;/p&gt;\r\n&lt;/blockquote&gt;', '2024-06-17 14:29:43'),
(15, 'NW2569', 'Welcome to the store', '&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer venenatis risus gravida auctor convallis. Sed nec enim id mauris volutpat pulvinar. Fusce consectetur est nulla, in faucibus dui euismod in. Donec id tortor sapien. Donec non rutrum justo. Aliquam erat volutpat. Nullam pellentesque condimentum ligula eget vehicula. Etiam aliquet efficitur velit. Nam pulvinar consequat congue. Fusce at velit sit amet nunc egestas consequat. In convallis sapien pellentesque metus viverra mollis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Cras gravida imperdiet arcu ut blandit. Donec sed mauris ultrices, dapibus velit quis, gravida nulla. Pellentesque at pellentesque massa, feugiat egestas ex. Nulla eu ipsum quis risus interdum venenatis.&lt;/p&gt;\r\n&lt;p&gt;Ut semper dolor nec tortor ornare, at lobortis nisi sollicitudin. Vestibulum urna dui, facilisis sit amet facilisis vel, ornare in ipsum. Fusce porta libero eu justo lacinia pellentesque. Etiam vitae neque ut massa ullamcorper posuere in eget tellus. Nunc vel nunc porta, scelerisque nisi sed, luctus purus. Vestibulum elementum augue eget convallis aliquet. Etiam sit amet imperdiet magna, vitae mattis ex. Vivamus quis rhoncus elit. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam egestas ultrices ultricies. Mauris felis massa, efficitur a pellentesque vitae, pharetra eget libero. Aenean lacinia leo vel ultrices pulvinar. Cras pharetra dolor arcu, ac placerat lorem egestas vel. Duis lacinia nisi sem, at tempor quam ornare non. Nam vel lectus quis quam eleifend dapibus sit amet eu elit.&lt;/p&gt;\r\n&lt;blockquote&gt;\r\n&lt;p&gt;Cras quis ullamcorper nunc, et consectetur est. Duis pulvinar mollis nisi, at viverra massa interdum ut. Quisque faucibus aliquam quam rhoncus posuere. Ut id nunc iaculis sem faucibus porttitor. Mauris pharetra dapibus elementum. Aliquam erat volutpat. Aliquam sed odio lectus. Donec pulvinar justo nec imperdiet bibendum.&lt;/p&gt;\r\n&lt;/blockquote&gt;', '2024-06-17 14:32:18'),
(34, 'NW5296', 'Bertin is the one who is organising ', '&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer venenatis risus gravida auctor convallis. Sed nec enim id mauris volutpat pulvinar. Fusce consectetur est nulla, in faucibus dui euismod in. Donec id tortor sapien. Donec non rutrum justo. Aliquam erat volutpat. Nullam pellentesque condimentum ligula eget vehicula. Etiam aliquet efficitur velit. Nam pulvinar consequat congue. Fusce at velit sit amet nunc egestas consequat. In convallis sapien pellentesque metus viverra mollis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Cras gravida imperdiet arcu ut blandit. Donec sed mauris ultrices, dapibus velit quis, gravida nulla. Pellentesque at pellentesque massa, feugiat egestas ex. Nulla eu ipsum quis risus interdum venenatis.&lt;/p&gt;\r\n&lt;p&gt;Ut semper dolor nec tortor ornare, at lobortis nisi sollicitudin. Vestibulum urna dui, facilisis sit amet facilisis vel, ornare in ipsum. Fusce porta libero eu justo lacinia pellentesque. Etiam vitae neque ut massa ullamcorper posuere in eget tellus. Nunc vel nunc porta, scelerisque nisi sed, luctus purus. Vestibulum elementum augue eget convallis aliquet. Etiam sit amet imperdiet magna, vitae mattis ex. Vivamus quis rhoncus elit. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam egestas ultrices ultricies. Mauris felis massa, efficitur a pellentesque vitae, pharetra eget libero. Aenean lacinia leo vel ultrices pulvinar. Cras pharetra dolor arcu, ac placerat lorem egestas vel. Duis lacinia nisi sem, at tempor quam ornare non. Nam vel lectus quis quam eleifend dapibus sit amet eu elit.&lt;/p&gt;\r\n&lt;p&gt;Cras quis ullamcorper nunc, et consectetur est. Duis pulvinar mollis nisi, at viverra massa interdum ut. Quisque faucibus aliquam quam rhoncus posuere. Ut id nunc iaculis sem faucibus porttitor. Mauris pharetra dapibus elementum. Aliquam erat volutpat. Aliquam sed odio lectus. Donec pulvinar justo nec imperdiet bibendum.&lt;/p&gt;', '2024-06-17 15:10:29'),
(35, 'NW3552', 'This is all american site', '&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer venenatis risus gravida auctor convallis. Sed nec enim id mauris volutpat pulvinar. Fusce consectetur est nulla, in faucibus dui euismod in. Donec id tortor sapien. Donec non rutrum justo. Aliquam erat volutpat. Nullam pellentesque condimentum ligula eget vehicula. Etiam aliquet efficitur velit. Nam pulvinar consequat congue. Fusce at velit sit amet nunc egestas consequat. In convallis sapien pellentesque metus viverra mollis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Cras gravida imperdiet arcu ut blandit. Donec sed mauris ultrices, dapibus velit quis, gravida nulla. Pellentesque at pellentesque massa, feugiat egestas ex. Nulla eu ipsum quis risus interdum venenatis.&lt;/p&gt;\r\n&lt;p&gt;Ut semper dolor nec tortor ornare, at lobortis nisi sollicitudin. Vestibulum urna dui, facilisis sit amet facilisis vel, ornare in ipsum. Fusce porta libero eu justo lacinia pellentesque. Etiam vitae neque ut massa ullamcorper posuere in eget tellus. Nunc vel nunc porta, scelerisque nisi sed, luctus purus. Vestibulum elementum augue eget convallis aliquet. Etiam sit amet imperdiet magna, vitae mattis ex. Vivamus quis rhoncus elit. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam egestas ultrices ultricies. Mauris felis massa, efficitur a pellentesque vitae, pharetra eget libero. Aenean lacinia leo vel ultrices pulvinar. Cras pharetra dolor arcu, ac placerat lorem egestas vel. Duis lacinia nisi sem, at tempor quam ornare non. Nam vel lectus quis quam eleifend dapibus sit amet eu elit.&lt;/p&gt;\r\n&lt;p&gt;Cras quis ullamcorper nunc, et consectetur est. Duis pulvinar mollis nisi, at viverra massa interdum ut. Quisque faucibus aliquam quam rhoncus posuere. Ut id nunc iaculis sem faucibus porttitor. Mauris pharetra dapibus elementum. Aliquam erat volutpat. Aliquam sed odio lectus. Donec pulvinar justo nec imperdiet bibendum.&lt;/p&gt;', '2024-06-17 15:12:39');

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
(6, 'PR3197', 'Bertin is the one who is organising ', 'Something is the data based below', '2024-06-14 14:26:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `file`
--
ALTER TABLE `file`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
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
-- AUTO_INCREMENT for table `file`
--
ALTER TABLE `file`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
