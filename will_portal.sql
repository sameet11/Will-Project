-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2023 at 06:05 PM
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
-- Creation: Mar 22, 2023 at 04:04 PM
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

-- --------------------------------------------------------

--
-- Table structure for table `guarantor_master`
--
-- Creation: Mar 22, 2023 at 04:04 PM
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

-- --------------------------------------------------------

--
-- Table structure for table `user_file_master`
--
-- Creation: Mar 22, 2023 at 04:04 PM
--

CREATE TABLE `user_file_master` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `fileType` enum('0','1','2','3') NOT NULL,
  `filePath` varchar(255) NOT NULL,
  `createdBy` varchar(255) NOT NULL,
  `createdOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedBy` varchar(255) NOT NULL,
  `updatedOn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `enabled` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user_master`
--
-- Creation: Mar 22, 2023 at 04:04 PM
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

-- --------------------------------------------------------

--
-- Table structure for table `will_bank_beneficiary_master`
--
-- Creation: Mar 22, 2023 at 04:04 PM
--

CREATE TABLE `will_bank_beneficiary_master` (
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

-- --------------------------------------------------------

--
-- Table structure for table `will_bank_file_master`
--
-- Creation: Mar 22, 2023 at 04:04 PM
--

CREATE TABLE `will_bank_file_master` (
  `id` int(11) NOT NULL,
  `willId` int(11) NOT NULL,
  `bankId` int(11) NOT NULL,
  `filePath` varchar(255) NOT NULL,
  `createdBy` int(11) NOT NULL,
  `createdOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedBy` int(11) NOT NULL,
  `updatedOn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `enabled` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `will_bank_master`
--
-- Creation: Mar 22, 2023 at 04:04 PM
--

CREATE TABLE `will_bank_master` (
  `id` int(11) NOT NULL,
  `willId` int(11) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `branch` varchar(255) NOT NULL,
  `account_number` varchar(255) NOT NULL,
  `account_type` enum('0','1') NOT NULL,
  `nominee_name` varchar(255) NOT NULL,
  `signature` varchar(255) NOT NULL,
  `createdBy` int(11) NOT NULL,
  `createdOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedBy` int(11) NOT NULL,
  `updatedOn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `enabled` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `will_fd_beneficiary_master`
--
-- Creation: Mar 22, 2023 at 04:04 PM
--

CREATE TABLE `will_fd_beneficiary_master` (
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

-- --------------------------------------------------------

--
-- Table structure for table `will_fd_file_master`
--
-- Creation: Mar 22, 2023 at 04:04 PM
--

CREATE TABLE `will_fd_file_master` (
  `id` int(11) NOT NULL,
  `willId` int(11) NOT NULL,
  `fdId` int(11) NOT NULL,
  `filePath` varchar(255) NOT NULL,
  `createdBy` int(11) NOT NULL,
  `createdOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedBy` int(11) NOT NULL,
  `updatedOn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `enabled` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `will_fd_master`
--
-- Creation: Mar 22, 2023 at 04:04 PM
--

CREATE TABLE `will_fd_master` (
  `id` int(11) NOT NULL,
  `willId` int(11) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `fdr_number` varchar(255) NOT NULL,
  `deposit_date` date NOT NULL,
  `due_date` date NOT NULL,
  `amount` varchar(255) NOT NULL,
  `signature` varchar(255) NOT NULL,
  `createdBy` int(11) NOT NULL,
  `createdOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedBy` int(11) NOT NULL,
  `updatedOn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `enabled` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `will_guarantor_master`
--
-- Creation: Mar 22, 2023 at 04:04 PM
-- Last update: Apr 02, 2023 at 05:18 PM
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

-- --------------------------------------------------------

--
-- Table structure for table `will_insurance_beneficiary_master`
--
-- Creation: Mar 22, 2023 at 04:04 PM
--

CREATE TABLE `will_insurance_beneficiary_master` (
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

-- --------------------------------------------------------

--
-- Table structure for table `will_insurance_master`
--
-- Creation: Mar 22, 2023 at 04:04 PM
--

CREATE TABLE `will_insurance_master` (
  `id` int(11) NOT NULL,
  `willId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `policy_number` varchar(255) NOT NULL,
  `insured_amount` int(11) NOT NULL,
  `issue_date` date NOT NULL,
  `maturity_date` date NOT NULL,
  `premium` varchar(255) NOT NULL,
  `createdBy` int(11) NOT NULL,
  `createdOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedBy` int(11) NOT NULL,
  `updatedOn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `enabled` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `will_master`
--
-- Creation: Mar 22, 2023 at 04:04 PM
-- Last update: Apr 02, 2023 at 05:18 PM
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

-- --------------------------------------------------------

--
-- Table structure for table `will_mc_beneficiary_master`
--
-- Creation: Mar 22, 2023 at 04:04 PM
-- Last update: Apr 02, 2023 at 04:55 PM
--

CREATE TABLE `will_mc_beneficiary_master` (
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

-- --------------------------------------------------------

--
-- Table structure for table `will_mediclaim_master`
--
-- Creation: Mar 22, 2023 at 04:04 PM
-- Last update: Apr 02, 2023 at 04:55 PM
--

CREATE TABLE `will_mediclaim_master` (
  `id` int(11) NOT NULL,
  `willid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `policy_number` varchar(255) NOT NULL,
  `insured_amount` int(11) NOT NULL,
  `issue_date` date NOT NULL,
  `maturity_date` date NOT NULL,
  `premium` varchar(255) NOT NULL,
  `createdBy` int(11) NOT NULL,
  `createdOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedBy` int(11) NOT NULL,
  `updatedOn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `enabled` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `will_mf_beneficiary_master`
--
-- Creation: Mar 22, 2023 at 04:04 PM
--

CREATE TABLE `will_mf_beneficiary_master` (
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

-- --------------------------------------------------------

--
-- Table structure for table `will_mf_master`
--
-- Creation: Mar 22, 2023 at 04:04 PM
--

CREATE TABLE `will_mf_master` (
  `id` int(11) NOT NULL,
  `willId` int(11) NOT NULL,
  `folio_number` varchar(255) NOT NULL,
  `fund_name` varchar(255) NOT NULL,
  `nominee_name` varchar(255) NOT NULL,
  `invested_amount` int(11) NOT NULL,
  `createdBy` int(11) NOT NULL,
  `createdOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedBy` int(11) NOT NULL,
  `updatedOn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `enabled` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `will_property_beneficiary_master`
--
-- Creation: Mar 22, 2023 at 04:04 PM
-- Last update: Mar 24, 2023 at 03:57 PM
--

CREATE TABLE `will_property_beneficiary_master` (
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

-- --------------------------------------------------------

--
-- Table structure for table `will_property_file_master`
--
-- Creation: Mar 22, 2023 at 04:04 PM
-- Last update: Mar 24, 2023 at 03:57 PM
--

CREATE TABLE `will_property_file_master` (
  `id` int(11) NOT NULL,
  `willId` int(11) NOT NULL,
  `propertyId` int(11) NOT NULL,
  `file_type` enum('0','1') NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `createdBy` int(11) NOT NULL,
  `createdOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedBy` int(11) NOT NULL,
  `updatedOn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `enabled` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `will_property_master`
--
-- Creation: Mar 22, 2023 at 04:04 PM
-- Last update: Mar 24, 2023 at 03:57 PM
--

CREATE TABLE `will_property_master` (
  `id` int(11) NOT NULL,
  `willid` int(11) NOT NULL,
  `property_details` varchar(255) NOT NULL,
  `registration_number` varchar(255) NOT NULL,
  `share_certificate_no` varchar(255) NOT NULL,
  `property_card` varchar(255) NOT NULL,
  `createdBy` int(11) NOT NULL,
  `createdOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedBy` int(11) NOT NULL,
  `updatedOn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `enabled` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `will_share_beneficiary_master`
--
-- Creation: Mar 22, 2023 at 04:04 PM
--

CREATE TABLE `will_share_beneficiary_master` (
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

-- --------------------------------------------------------

--
-- Table structure for table `will_share_master`
--
-- Creation: Mar 22, 2023 at 04:04 PM
--

CREATE TABLE `will_share_master` (
  `id` int(11) NOT NULL,
  `willId` int(11) NOT NULL,
  `company` varchar(255) NOT NULL,
  `share_quantity` int(11) NOT NULL,
  `demat_no` varchar(255) NOT NULL,
  `nominee_name` varchar(255) NOT NULL,
  `createdBy` int(11) NOT NULL,
  `createdOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedBy` int(11) NOT NULL,
  `updatedOn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `enabled` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
-- Indexes for table `user_file_master`
--
ALTER TABLE `user_file_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_master`
--
ALTER TABLE `user_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `will_bank_beneficiary_master`
--
ALTER TABLE `will_bank_beneficiary_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `will_bank_file_master`
--
ALTER TABLE `will_bank_file_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `will_bank_master`
--
ALTER TABLE `will_bank_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `will_fd_beneficiary_master`
--
ALTER TABLE `will_fd_beneficiary_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `will_fd_file_master`
--
ALTER TABLE `will_fd_file_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `will_fd_master`
--
ALTER TABLE `will_fd_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `will_guarantor_master`
--
ALTER TABLE `will_guarantor_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `will_insurance_beneficiary_master`
--
ALTER TABLE `will_insurance_beneficiary_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `will_insurance_master`
--
ALTER TABLE `will_insurance_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `will_master`
--
ALTER TABLE `will_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `will_mc_beneficiary_master`
--
ALTER TABLE `will_mc_beneficiary_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `will_mediclaim_master`
--
ALTER TABLE `will_mediclaim_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `will_mf_beneficiary_master`
--
ALTER TABLE `will_mf_beneficiary_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `will_mf_master`
--
ALTER TABLE `will_mf_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `will_property_beneficiary_master`
--
ALTER TABLE `will_property_beneficiary_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `will_property_file_master`
--
ALTER TABLE `will_property_file_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `will_property_master`
--
ALTER TABLE `will_property_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `will_share_beneficiary_master`
--
ALTER TABLE `will_share_beneficiary_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `will_share_master`
--
ALTER TABLE `will_share_master`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `beneficiary_master`
--
ALTER TABLE `beneficiary_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guarantor_master`
--
ALTER TABLE `guarantor_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_file_master`
--
ALTER TABLE `user_file_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_master`
--
ALTER TABLE `user_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `will_bank_beneficiary_master`
--
ALTER TABLE `will_bank_beneficiary_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `will_bank_file_master`
--
ALTER TABLE `will_bank_file_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `will_bank_master`
--
ALTER TABLE `will_bank_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `will_fd_beneficiary_master`
--
ALTER TABLE `will_fd_beneficiary_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `will_fd_file_master`
--
ALTER TABLE `will_fd_file_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `will_fd_master`
--
ALTER TABLE `will_fd_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `will_guarantor_master`
--
ALTER TABLE `will_guarantor_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `will_insurance_beneficiary_master`
--
ALTER TABLE `will_insurance_beneficiary_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `will_insurance_master`
--
ALTER TABLE `will_insurance_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `will_master`
--
ALTER TABLE `will_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `will_mc_beneficiary_master`
--
ALTER TABLE `will_mc_beneficiary_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `will_mediclaim_master`
--
ALTER TABLE `will_mediclaim_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `will_mf_beneficiary_master`
--
ALTER TABLE `will_mf_beneficiary_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `will_mf_master`
--
ALTER TABLE `will_mf_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `will_property_beneficiary_master`
--
ALTER TABLE `will_property_beneficiary_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `will_property_file_master`
--
ALTER TABLE `will_property_file_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `will_property_master`
--
ALTER TABLE `will_property_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `will_share_beneficiary_master`
--
ALTER TABLE `will_share_beneficiary_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `will_share_master`
--
ALTER TABLE `will_share_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
