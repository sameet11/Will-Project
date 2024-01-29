-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 05, 2023 at 08:45 PM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `will_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `beneficiary_master`
--

CREATE TABLE `beneficiary_master` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `createdBy` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `createdOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedBy` int(11) NOT NULL,
  `updatedOn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `enabled` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `beneficiary_master`
--

INSERT INTO `beneficiary_master` (`id`, `name`, `phone`, `email`, `address`, `createdBy`, `company_id`, `createdOn`, `updatedBy`, `updatedOn`, `enabled`) VALUES
(1, 'Vaibhav Mandlik', '1234567890', 'abc@xyz.comm', 'Mumbai', 1, 0, '2023-03-04 19:22:04', 0, '2023-03-04 19:24:15', '1'),
(3, 'John Doe', '1234567890', 'xyz@abc.com', 'US', 1, 0, '2023-03-05 12:26:22', 0, '2023-03-05 12:26:22', '1'),
(4, 'David Johnson', '1234567890', 'xyz@abc.com', 'UK', 1, 0, '2023-03-05 12:26:40', 0, '2023-03-05 12:26:40', '1');

-- --------------------------------------------------------

--
-- Table structure for table `guarantor_master`
--

CREATE TABLE `guarantor_master` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `islawyer` enum('0','1') NOT NULL DEFAULT '0',
  `createdBy` int(11) NOT NULL,
  `createdOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedBy` int(11) NOT NULL,
  `updatedOn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `enabled` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `guarantor_master`
--

INSERT INTO `guarantor_master` (`id`, `name`, `phone`, `email`, `address`, `islawyer`, `createdBy`, `createdOn`, `updatedBy`, `updatedOn`, `enabled`) VALUES
(1, 'Rohit Jadhav', '8668723797', 'example@abc.com', 'Pune', '0', 1, '2022-11-26 08:38:51', 0, '2023-03-04 19:09:22', '1'),
(2, 'Omkar gattawar', '7758077101', 'on@1.com', 'Nashik', '1', 1, '2022-11-29 17:04:39', 0, '2023-03-05 09:48:15', '1'),
(3, 'Atharva ghotekar', '1234567890', 'ag@1.com', 'Nashik', '0', 1, '2022-11-29 17:05:04', 0, '2023-03-05 13:50:42', '1'),
(4, 'Bhushan Chaudhari', '7218057088', 'xyz@gmail.com', 'Nashik', '1', 1, '2022-12-17 13:53:00', 0, '2023-03-05 09:39:06', '1'),
(5, 'Bhushan Mandlik', '9309821233', 'xyz@gmail.com', 'Nashik', '0', 1, '2022-12-17 13:53:47', 0, '2022-12-17 13:53:47', '1'),
(6, 'Ajay Ghate', '9922174485', 'xyz@gmail.com', 'Nashik', '1', 1, '2022-12-17 13:54:36', 0, '2023-03-05 09:39:10', '1'),
(7, 'Devansh Kulthe', '9764884880', 'xyz@abc.com', 'Blu Ridge, Hinjewadi, Pune', '0', 1, '2023-02-05 13:26:09', 0, '2023-02-05 13:26:09', '1'),
(8, 'Vaibhav Mandlik', '8668723797', 'xyz@abc.com', 'Nashik', '1', 1, '2023-02-05 13:30:12', 0, '2023-03-05 09:39:14', '1'),
(9, 'Sushilkumar Bodhi', '9595262668', 'xyz@abc.com', 'Pimpri-Chinchwad, Pune', '0', 1, '2023-02-09 17:14:06', 0, '2023-02-09 17:14:06', '1'),
(12, 'Rushikesh Gangurde', '1234567890', 'xyz@abc.com', 'Pune', '1', 1, '2023-03-04 14:34:39', 0, '2023-03-05 09:39:17', '1'),
(13, 'Yash Belgaonkar', '1234567890', 'xyz@abc.com', 'Mumbai', '0', 1, '2023-03-04 19:20:40', 0, '2023-03-04 19:20:40', '1');

-- --------------------------------------------------------

--
-- Table structure for table `user_master`
--

CREATE TABLE `user_master` (
  `id` int(11) NOT NULL,
  `category` varchar(10) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `phone_number` varchar(13) NOT NULL,
  `address` varchar(255) NOT NULL,
  `email_id` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `createdBy` int(11) NOT NULL,
  `createdOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedBy` int(11) NOT NULL,
  `updatedOn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `enabled` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_master`
--

INSERT INTO `user_master` (`id`, `category`, `first_name`, `last_name`, `phone_number`, `address`, `email_id`, `password`, `createdBy`, `createdOn`, `updatedBy`, `updatedOn`, `enabled`) VALUES
(1, 'superadmin', 'Super', 'Admin', '', '', 'admin', '123', 0, '2021-09-05 11:56:07', 0, '2023-02-09 17:29:28', '1'),
(2, '', 'Vaibhav', 'Mandlik', '', '', 'abc@xyz.comm', '123', 0, '2023-03-05 13:40:08', 0, '2023-03-05 13:40:08', '1'),
(3, '', 'Omkar', 'Gattawar', '', '', 'on@1.com', '123', 0, '2023-03-05 13:45:51', 0, '2023-03-05 13:45:51', '1');

-- --------------------------------------------------------

--
-- Table structure for table `will_beneficiary_master`
--

CREATE TABLE `will_beneficiary_master` (
  `id` int(11) NOT NULL,
  `willId` int(11) NOT NULL,
  `beneficiaryId` int(11) NOT NULL,
  `percentage` int(11) NOT NULL,
  `createdBy` varchar(255) NOT NULL,
  `createdOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedBy` varchar(255) NOT NULL,
  `updatedOn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `enabled` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `will_beneficiary_master`
--

INSERT INTO `will_beneficiary_master` (`id`, `willId`, `beneficiaryId`, `percentage`, `createdBy`, `createdOn`, `updatedBy`, `updatedOn`, `enabled`) VALUES
(1, 1, 1, 80, '1', '2023-03-05 13:16:25', '1', '2023-03-05 13:16:48', '0'),
(2, 1, 3, 20, '1', '2023-03-05 13:16:25', '1', '2023-03-05 13:16:48', '0'),
(3, 1, 1, 80, '1', '2023-03-05 13:16:48', '1', '2023-03-05 13:16:48', '1'),
(4, 1, 3, 20, '1', '2023-03-05 13:16:48', '1', '2023-03-05 13:16:48', '1');

-- --------------------------------------------------------

--
-- Table structure for table `will_guarantor_master`
--

CREATE TABLE `will_guarantor_master` (
  `id` int(11) NOT NULL,
  `willId` int(11) NOT NULL,
  `guarantorId` int(11) NOT NULL,
  `hasApproved` enum('0','1','2') NOT NULL DEFAULT '0',
  `createdBy` varchar(255) NOT NULL,
  `createdOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedBy` varchar(255) NOT NULL,
  `updatedOn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `enabled` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `will_guarantor_master`
--

INSERT INTO `will_guarantor_master` (`id`, `willId`, `guarantorId`, `hasApproved`, `createdBy`, `createdOn`, `updatedBy`, `updatedOn`, `enabled`) VALUES
(1, 1, 1, '0', '1', '2023-03-05 13:16:25', '1', '2023-03-05 13:16:48', '0'),
(2, 1, 2, '0', '1', '2023-03-05 13:16:25', '1', '2023-03-05 13:16:48', '0'),
(3, 1, 3, '0', '1', '2023-03-05 13:16:25', '1', '2023-03-05 13:16:48', '0'),
(4, 1, 1, '0', '1', '2023-03-05 13:16:48', '1', '2023-03-05 13:16:48', '1'),
(5, 1, 2, '1', '1', '2023-03-05 13:16:48', '3', '2023-03-05 19:38:22', '1'),
(6, 1, 3, '0', '1', '2023-03-05 13:16:48', '1', '2023-03-05 13:16:48', '1');

-- --------------------------------------------------------

--
-- Table structure for table `will_master`
--

CREATE TABLE `will_master` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `approved` enum('0','1') NOT NULL DEFAULT '0',
  `createdBy` varchar(255) NOT NULL,
  `createdOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedBy` varchar(255) NOT NULL,
  `updatedOn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `enabled` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `will_master`
--

INSERT INTO `will_master` (`id`, `title`, `description`, `approved`, `createdBy`, `createdOn`, `updatedBy`, `updatedOn`, `enabled`) VALUES
(1, 'First Will', 'Description of the first will updated', '0', '1', '2023-03-05 13:16:25', '1', '2023-03-05 13:16:48', '1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `beneficiary_master`
--
ALTER TABLE `beneficiary_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guarantor_master`
--
ALTER TABLE `guarantor_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_master`
--
ALTER TABLE `user_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `will_beneficiary_master`
--
ALTER TABLE `will_beneficiary_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `will_guarantor_master`
--
ALTER TABLE `will_guarantor_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `will_master`
--
ALTER TABLE `will_master`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `beneficiary_master`
--
ALTER TABLE `beneficiary_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `guarantor_master`
--
ALTER TABLE `guarantor_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_master`
--
ALTER TABLE `user_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `will_beneficiary_master`
--
ALTER TABLE `will_beneficiary_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `will_guarantor_master`
--
ALTER TABLE `will_guarantor_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `will_master`
--
ALTER TABLE `will_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
