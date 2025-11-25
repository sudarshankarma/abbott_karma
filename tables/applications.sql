-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2025 at 08:37 AM
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
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `application_id` varchar(50) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `whatsapp` varchar(15) DEFAULT NULL,
  `piramal_uan` varchar(20) DEFAULT NULL,
  `abbott_uan` varchar(20) DEFAULT NULL,
  `piramal_id` varchar(50) DEFAULT NULL,
  `abbott_id` varchar(50) DEFAULT NULL,
  `pan_number` varchar(10) DEFAULT NULL,
  `aadhar_number` varchar(12) DEFAULT NULL,
  `pan_card` varchar(500) DEFAULT NULL,
  `aadhar_card` varchar(500) DEFAULT NULL,
  `cancelled_cheque` varchar(500) DEFAULT NULL,
  `acknowledge_doc` varchar(255) DEFAULT NULL,
  `consent_given` tinyint(1) DEFAULT 0,
  `status` enum('personal_details_completed','documents_uploaded','under_review','approved','rejected','pending_verification') DEFAULT 'personal_details_completed',
  `phone_verified` tinyint(1) DEFAULT 0,
  `whatsapp_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `submitted_at` timestamp NULL DEFAULT NULL,
  `admin_status` enum('pending','under_review','approved','rejected') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `pan_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `aadhar_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `cheque_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_reviewer_id` int(11) DEFAULT NULL,
  `document_reviewed_at` timestamp NULL DEFAULT NULL,
  `overall_status` enum('pending','under_review','approved','rejected') DEFAULT 'pending',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `application_id`, `full_name`, `email`, `phone`, `whatsapp`, `piramal_uan`, `abbott_uan`, `piramal_id`, `abbott_id`, `pan_number`, `aadhar_number`, `pan_card`, `aadhar_card`, `cancelled_cheque`, `acknowledge_doc`, `consent_given`, `status`, `phone_verified`, `whatsapp_verified`, `created_at`, `updated_at`, `submitted_at`, `admin_status`, `admin_notes`, `reviewed_by`, `reviewed_at`, `pan_status`, `aadhar_status`, `cheque_status`, `admin_reviewer_id`, `document_reviewed_at`, `overall_status`, `last_updated`) VALUES
(1, 'EMP-2025-9751', 'Sudarshan Thatypally', 'sudarshan.india1@gmail.com', '8898106409', '8898106409', '212121221221', '212121212313', NULL, NULL, 'AQPPP5544S', '121212122112', 'pan_card_1763806288_69218c50e74ad.png', 'aadhar_card_1763806318_69218c6e48266.png', 'cancelled_cheque_1763803785_69218289e6532.jpeg', 'acknowledge_doc_1763804488_6921854854961.pdf', 1, 'under_review', 1, 0, '2025-10-06 01:41:51', '2025-11-22 10:11:58', NULL, 'under_review', NULL, NULL, NULL, 'pending', 'pending', 'pending', NULL, NULL, 'pending', '2025-11-22 10:11:58'),
(2, 'EMP-2025-9100', 'Sudarshan Thatypally', 'sudarshan.india1@gmail.com', '8898106409', '8898106409', '676767767677', '656565655665', NULL, NULL, 'AQBPT7609P', '212121212212', 'pan_1759736258_68e371c2ee3bb.png', 'aadhar_1759736258_68e371c2eeb50.jpeg', 'cheque_1759736258_68e371c2ef20d.png', NULL, 1, 'under_review', 1, 0, '2025-10-06 02:07:38', '2025-11-10 08:11:29', NULL, 'pending', NULL, NULL, NULL, 'pending', 'pending', 'pending', NULL, NULL, 'pending', '2025-11-22 09:12:21'),
(3, 'EMP-2025-6947', 'Sudarshan Thatypally', 'sudarshan.india1@gmail.com', '8898106409', '8898106409', '676767767677', '656565655665', NULL, NULL, 'AQBPT7609P', '212121212212', 'pan_1759736330_68e3720ac7fd7.png', 'aadhar_1759736330_68e3720ac869c.jpeg', 'cheque_1759736330_68e3720ac911c.png', NULL, 1, 'under_review', 1, 0, '2025-10-06 02:08:50', '2025-11-10 08:11:29', NULL, 'pending', NULL, NULL, NULL, 'pending', 'pending', 'pending', NULL, NULL, 'pending', '2025-11-22 09:12:21'),
(4, 'EMP-2025-2560', 'Sudarshan Thatypally', 'sudarshan.india1@gmail.com', '8898106409', '8898106409', '676767767677', '656565655665', NULL, NULL, 'AQBPT7609P', '211290291222', 'pan_1759737055_68e374dfefdd7.png', 'aadhar_1759737055_68e374dff0613.png', 'cheque_1759737055_68e374dff0d11.png', NULL, 1, 'under_review', 1, 0, '2025-10-06 02:20:55', '2025-11-10 08:11:29', NULL, 'pending', NULL, NULL, NULL, 'pending', 'pending', 'pending', NULL, NULL, 'pending', '2025-11-22 09:12:21'),
(5, 'EMP-2025-4017', 'Sudarshan Thatypally', 'sudarshan.india1@gmail.com', '8898106409', '8898106409', '676767767677', '212121212313', NULL, NULL, 'AQBPT7609P', '211290291222', 'pan_1759737123_68e37523d4a1d.png', 'aadhar_1759737123_68e37523d4fa3.pdf', 'cheque_1759737123_68e37523d58a0.pdf', NULL, 1, 'under_review', 1, 0, '2025-10-06 02:22:03', '2025-11-10 08:11:29', NULL, 'pending', NULL, NULL, NULL, 'pending', 'pending', 'pending', NULL, NULL, 'pending', '2025-11-22 09:12:21'),
(6, 'EMP-2025-6044', 'Sudarshan Rajaram Thatypally', 'sudarshan.india1@gmail.com', '8898106409', '8898106409', '676767767677', '212121212313', NULL, NULL, 'AQBPT7609P', '211290291222', 'pan_1759740224_68e38140e9b94.jpg', 'aadhar_1759740224_68e38140ea1f9.jpeg', 'cheque_1759740224_68e38140eac60.jpg', NULL, 1, 'under_review', 1, 0, '2025-10-06 03:13:44', '2025-11-10 08:11:29', NULL, 'pending', NULL, NULL, NULL, 'pending', 'pending', 'pending', NULL, NULL, 'pending', '2025-11-22 09:12:21'),
(7, 'EMP-2025-0364', 'Sudarshan Rajaram Thatypally', 'sudarshan.india1@gmail.com', '8898106409', '8898106409', '676767767677', '212121212313', NULL, NULL, 'AQBPT7609P', '211290291222', 'pan_1759740557_68e3828dad569.pdf', 'aadhar_1759740557_68e3828dae15e.pdf', 'cheque_1759740557_68e3828daee07.png', NULL, 1, 'under_review', 1, 0, '2025-10-06 03:19:17', '2025-11-10 08:11:29', NULL, 'pending', NULL, NULL, NULL, 'pending', 'pending', 'pending', NULL, NULL, 'pending', '2025-11-22 09:12:21'),
(8, 'EPF-2025-2026', 'Test user', 'test@gmail.com', '8898999888', '8898999888', '145632589756', '125422136522', 'PIR1141', 'ABB1255', 'AQBPT7609P', '211290291222', 'pan_1762763388_6911a27ca0409.jpeg', 'aadhar_1762763388_6911a27ca1f29.png', 'cheque_1762763388_6911a27ca2c34.jpeg', NULL, 1, 'documents_uploaded', 1, 0, '2025-11-10 08:29:21', '2025-11-10 09:11:24', NULL, 'pending', NULL, NULL, NULL, 'rejected', 'pending', 'pending', 1, '2025-11-10 09:11:24', 'pending', '2025-11-22 09:12:21'),
(9, 'EPF-2025-7175', 'Vijay', 'vijay@gmailc.om', '9898989898', '9898989898', '123456788876', '877654334455', 'PIR11', 'ABB12', 'AYYHH8888S', '889898899999', 'pan_1762859513_691319f9879be.png', 'aadhar_1762859513_691319f988a2c.png', 'cheque_1762859513_691319f9897f4.png', NULL, 1, 'documents_uploaded', 1, 0, '2025-11-11 11:11:22', '2025-11-18 02:28:42', NULL, 'pending', '', NULL, NULL, 'pending', 'pending', 'pending', NULL, NULL, 'pending', '2025-11-22 09:12:21'),
(10, 'EPF-2025-7133', 'Mithun', 'mithun@karma.com', '7276973699', '7276973699', '676767767677', '125422136522', 'PIM111', 'ABB111', 'AYYHH8888S', '211290291222', 'pan_1763554670_691db56e1512f.png', 'aadhar_1763554670_691db56e1693d.png', 'cheque_1763554670_691db56e17b9a.png', NULL, 1, 'documents_uploaded', 1, 0, '2025-11-19 12:17:10', '2025-11-19 12:17:50', NULL, 'pending', NULL, NULL, NULL, 'pending', 'pending', 'pending', NULL, NULL, 'pending', '2025-11-22 09:12:21'),
(11, 'abbott-sudarshanrajaramthatypally-88988-3246', 'Sudarshan Rajaram Thatypally', 'sudarshan.india1@gmail.com', '8898888988', '8898888988', '676767767677', '877654334455', 'PIM111', 'ABB1255', 'AQBPT7609P', '211290291222', 'pan_1763558419_691dc413b3f65.png', 'aadhar_1763558419_691dc413b4f40.png', 'cheque_1763558419_691dc413b5c6b.jpeg', NULL, 1, 'documents_uploaded', 1, 0, '2025-11-19 13:19:48', '2025-11-19 13:20:19', NULL, 'pending', NULL, NULL, NULL, 'pending', 'pending', 'pending', NULL, NULL, 'pending', '2025-11-22 09:12:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_application_id` (`application_id`),
  ADD KEY `idx_phone` (`phone`),
  ADD KEY `idx_pan_number` (`pan_number`),
  ADD KEY `idx_aadhar_number` (`aadhar_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
