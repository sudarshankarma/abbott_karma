-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2025 at 08:21 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `abbott_admin`
--

-- --------------------------------------------------------

--
-- Table structure for table `document_comments`
--

CREATE TABLE `document_comments` (
  `id` int(11) NOT NULL,
  `application_id` varchar(50) NOT NULL,
  `document_type` enum('pan_card','aadhar_card','cancelled_cheque','acknowledge_doc') NOT NULL,
  `comment` text NOT NULL,
  `commented_by` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `document_comments`
--

INSERT INTO `document_comments` (`id`, `application_id`, `document_type`, `comment`, `commented_by`, `created_at`) VALUES
(1, 'EMP-2025-9751', 'pan_card', 'test', 'user', '2025-11-22 10:10:00'),
(2, 'EMP-2025-9751', 'pan_card', 'its very simple msg', 'user', '2025-11-22 10:10:10'),
(3, 'EMP-2025-9751', 'pan_card', 'test', 'user', '2025-11-22 10:10:17'),
(4, 'EMP-2025-9751', 'pan_card', 'kjsajska', 'user', '2025-11-22 10:10:20'),
(5, 'EMP-2025-9751', 'pan_card', 'aalkkaklaslk', 'user', '2025-11-22 10:10:23'),
(6, 'EMP-2025-9751', 'pan_card', 'salkslalsa', 'user', '2025-11-22 10:10:27'),
(7, 'EMP-2025-9751', 'pan_card', 'salklasksak', 'user', '2025-11-22 10:10:30'),
(8, 'EMP-2025-9751', 'pan_card', 'slakslaslaksl', 'user', '2025-11-22 10:10:33'),
(9, 'EMP-2025-9751', 'pan_card', 'akslkaksla', 'user', '2025-11-22 10:10:37'),
(10, 'EMP-2025-9751', 'pan_card', 'test\r\n', 'user', '2025-11-22 11:36:06'),
(11, 'EMP-2025-9751', 'pan_card', 'assas', 'user', '2025-11-22 11:36:10'),
(12, 'EMP-2025-9751', 'aadhar_card', 'bvbv', 'user', '2025-11-24 06:44:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `document_comments`
--
ALTER TABLE `document_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_application_document` (`application_id`,`document_type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `document_comments`
--
ALTER TABLE `document_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `document_comments`
--
ALTER TABLE `document_comments`
  ADD CONSTRAINT `fk_document_comments_application` FOREIGN KEY (`application_id`) REFERENCES `applications` (`application_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
