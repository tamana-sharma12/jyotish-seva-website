-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 18, 2026 at 09:41 AM
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
-- Database: `jyotish_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password_hash`, `last_login`) VALUES
(1, 'admin', 'admin123', '2026-03-13 15:40:41');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `astrologer_name` varchar(100) DEFAULT NULL,
  `booking_date` datetime DEFAULT NULL,
  `status` enum('pending','confirmed','completed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `astrologer_info`
--

CREATE TABLE `astrologer_info` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `tagline` varchar(300) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `photo_url` varchar(500) DEFAULT NULL,
  `bg_img` varchar(255) DEFAULT NULL,
  `experience_years` int(11) DEFAULT NULL,
  `specialties` varchar(500) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `astrologer_info`
--

INSERT INTO `astrologer_info` (`id`, `name`, `tagline`, `bio`, `photo_url`, `bg_img`, `experience_years`, `specialties`, `phone`, `email`, `created_at`) VALUES
(1, 'Dr. Vedika Rajvansh', 'Empowering Your Life Journey through Ancient Vedic Wisdom, Precision Birth-Chart Analysis, and Divine Spiritual Remedies for a Prosperous and Harmonious Future.', 'Dr. Vedika Rajvansh is a highly respected Vedic Scholar and Spiritual Life Coach with a profound understanding of celestial impacts on human life. Over the years, she has helped thousands of individuals worldwide discover their true purpose, navigate career complexities, and mend broken relationships through her empathetic and scientific approach to ancient shastras. Her mission is to illuminate the path of success and mental peace for everyone who seeks divine guidance.', 'https://images.pexels.com/photos/7176026/pexels-photo-7176026.jpeg', 'https://images.pexels.com/photos/6014328/pexels-photo-6014328.jpeg', 18, 'Advanced Vedic Astrology, Corporate Vastu Shastra, Gemology Consultant, Lal Kitab Remedies, Medical Astrology', '9876543210', 'contact@jyotishseva.com', '2026-03-09 06:07:03');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `booking_ref` varchar(20) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `dob` date DEFAULT NULL,
  `tob` time DEFAULT NULL,
  `pob` varchar(255) DEFAULT NULL,
  `booking_date` date NOT NULL,
  `slot_id` int(11) DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `utm_source` varchar(100) DEFAULT NULL,
  `utm_medium` varchar(100) DEFAULT NULL,
  `utm_campaign` varchar(100) DEFAULT NULL,
  `gclid` varchar(255) DEFAULT NULL,
  `fbclid` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_ref`, `full_name`, `email`, `phone`, `dob`, `tob`, `pob`, `booking_date`, `slot_id`, `status`, `utm_source`, `utm_medium`, `utm_campaign`, `gclid`, `fbclid`, `created_at`) VALUES
(4, NULL, 'Rahul Kumar', 'rahul@example.com', '9876543210', NULL, NULL, NULL, '2026-03-15', 1, 'confirmed', NULL, NULL, NULL, NULL, NULL, '2026-03-10 07:12:14'),
(5, 'JS-B4DD4C', 'Rahul Kumar', 'rahul@example.com', '9876543210', NULL, NULL, '', '2026-03-15', 1, 'confirmed', 'direct', NULL, NULL, NULL, NULL, '2026-03-10 11:48:27'),
(6, 'JS-22CDBA', 'Rahul Kumar', 'rahul@example.com', '9876543210', NULL, NULL, '', '2026-03-15', 1, 'confirmed', 'direct', NULL, NULL, NULL, NULL, '2026-03-10 12:21:54'),
(7, 'JS-690415', 'Rahul Kumar', 'rahul@example.com', '9876543210', NULL, NULL, '', '2026-03-15', 1, 'confirmed', 'direct', NULL, NULL, NULL, NULL, '2026-03-10 12:47:34'),
(8, 'JS-8DD901', 'Rahul Kumar', 'rahul@example.com', '9876543210', NULL, NULL, '', '2026-03-15', 1, 'confirmed', 'direct', NULL, NULL, NULL, NULL, '2026-03-10 12:47:36');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_id`, `amount`, `status`, `created_at`) VALUES
(1, 'order_mock_fab274730633', 501.00, 'pending', '2026-03-10 04:41:20'),
(2, 'order_mock_ffb2d32dfd96', 501.00, 'pending', '2026-03-10 04:43:55'),
(3, 'order_mock_ee9a0d0cb139', 501.00, 'pending', '2026-03-10 04:43:57'),
(4, 'order_mock_5149bacea225', 501.00, 'pending', '2026-03-10 04:43:57'),
(5, 'order_mock_4ad84731aebc', 501.00, 'pending', '2026-03-10 04:43:58'),
(6, 'order_mock_1342415e34fd', 501.00, 'pending', '2026-03-10 12:58:26');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `razorpay_order_id` varchar(255) DEFAULT NULL,
  `razorpay_payment_id` varchar(255) DEFAULT NULL,
  `razorpay_signature` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `razorpay_order_id`, `razorpay_payment_id`, `razorpay_signature`, `amount`, `payment_status`, `payment_date`) VALUES
(1, 4, 'order_mock_123456', 'pay_mock_ku4itdix1', 'fake_signature_for_testing', 501.00, 'success', '2026-03-10 07:12:14'),
(2, 5, 'order_mock_123456', 'pay_mock_bk2vqob8t', 'fake_signature_for_testing', 501.00, 'success', '2026-03-10 11:48:27'),
(3, 6, 'order_mock_123456', 'pay_mock_oytwumzoh', 'fake_signature_for_testing', 501.00, 'success', '2026-03-10 12:21:54'),
(4, 7, 'order_mock_123456', 'pay_mock_eoj9xcq2q', 'fake_signature_for_testing', 501.00, 'success', '2026-03-10 12:47:34'),
(5, 8, 'order_mock_123456', 'pay_mock_dre2c4t81', 'fake_signature_for_testing', 501.00, 'success', '2026-03-10 12:47:36');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `reviewer_name` varchar(200) NOT NULL,
  `rating` tinyint(4) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `is_approved` tinyint(4) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `reviewer_name`, `rating`, `comment`, `is_approved`, `created_at`) VALUES
(1, 'Rajesh Kumar', 5, 'Excellent predictions! All my problems were solved with great accuracy.', 1, '2026-03-09 12:22:45'),
(2, 'Suman Devi', 4, 'The Vastu tips were very effective. Highly recommended.', 1, '2026-02-11 12:32:12'),
(3, 'Ankit Verma', 5, 'Very professional and deep knowledge of astrology.', 1, '2026-01-09 12:02:23'),
(4, 'Tamana Sharma', 2, 'tamana sharama jhfjagsdf ajsdhfiauwegf ajshdfawuefh jkahsdfjajefi  ahdfjkakf ajshfaihufas ', 1, '2026-03-11 13:39:26'),
(5, 'Tamana Sharma', 3, 'dfgsdg fgfhg fghfj gdfg', 1, '2026-03-11 15:57:05');

-- --------------------------------------------------------

--
-- Table structure for table `time_slots`
--

CREATE TABLE `time_slots` (
  `id` int(11) NOT NULL,
  `slot_label` varchar(50) DEFAULT NULL,
  `max_capacity` int(11) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `time_slots`
--

INSERT INTO `time_slots` (`id`, `slot_label`, `max_capacity`, `is_active`, `sort_order`) VALUES
(1, '09:00 AM - 10:00 AM', 1, 1, 1),
(2, '09:00 AM', 1, 1, 2),
(3, '10:00 AM', 1, 1, 3),
(4, '11:00 AM', 1, 1, 4),
(5, '12:00 PM', 1, 1, 5),
(6, '02:00 PM', 1, 1, 6),
(7, '03:00 PM', 1, 1, 7),
(8, '04:00 PM', 1, 1, 8),
(9, '05:00 PM', 1, 1, 9);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `astrologer_info`
--
ALTER TABLE `astrologer_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `slot_id` (`slot_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `time_slots`
--
ALTER TABLE `time_slots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `astrologer_info`
--
ALTER TABLE `astrologer_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `time_slots`
--
ALTER TABLE `time_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`slot_id`) REFERENCES `time_slots` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
