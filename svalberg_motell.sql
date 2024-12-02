-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 02. Des, 2024 01:31 AM
-- Tjener-versjon: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `svalberg_motell`
--

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `swx_booking`
--

CREATE TABLE `swx_booking` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `room_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `number_of_guests` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `tlf` varchar(20) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `swx_booking`
--

INSERT INTO `swx_booking` (`booking_id`, `user_id`, `room_id`, `payment_id`, `check_in_date`, `check_out_date`, `number_of_guests`, `name`, `email`, `tlf`, `comments`, `created_at`, `updated_at`) VALUES
(1, NULL, 1, 1, '2024-12-27', '2024-12-29', 2, 'Michael buble', 'ine.antonsen@hotmail.com', '4711111111', 'hei', '2024-12-02 00:30:11', '2024-12-02 00:30:11');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `swx_payment`
--

CREATE TABLE `swx_payment` (
  `payment_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` enum('Credit Card','Vips','invoice') NOT NULL,
  `invoice_path` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Completed','Failed') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `swx_payment`
--

INSERT INTO `swx_payment` (`payment_id`, `amount`, `payment_date`, `payment_method`, `invoice_path`, `status`) VALUES
(1, 1300, '2024-12-02 00:30:11', 'Credit Card', NULL, 'Completed');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `swx_room`
--

CREATE TABLE `swx_room` (
  `room_id` int(11) NOT NULL,
  `room_type` int(11) NOT NULL,
  `nearElevator` enum('Ja','Nei') NOT NULL,
  `floor` int(11) NOT NULL,
  `availability` enum('ledig','opptatt') NOT NULL,
  `under_construction` enum('Ja','Nei') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `swx_room`
--

INSERT INTO `swx_room` (`room_id`, `room_type`, `nearElevator`, `floor`, `availability`, `under_construction`, `created_at`, `updated_at`) VALUES
(1, 1, 'Nei', 1, 'ledig', 'Nei', '2024-12-01 00:35:42', '2024-12-01 00:36:14'),
(2, 1, 'Nei', 1, 'ledig', 'Nei', '2024-12-01 00:36:58', '2024-12-01 00:38:14'),
(3, 1, 'Nei', 1, 'ledig', 'Nei', '2024-12-01 00:37:11', '2024-12-01 00:38:18'),
(4, 1, 'Nei', 1, 'ledig', 'Nei', '2024-12-01 00:37:27', '2024-12-01 00:37:27'),
(5, 1, 'Nei', 1, 'ledig', 'Nei', '2024-12-01 00:37:27', '2024-12-01 00:38:30'),
(6, 1, 'Nei', 1, 'ledig', 'Nei', '2024-12-01 00:37:45', '2024-12-01 00:38:41'),
(7, 2, 'Ja', 1, 'ledig', 'Nei', '2024-12-01 00:40:09', '2024-12-01 00:44:17'),
(8, 2, 'Ja', 1, 'ledig', 'Nei', '2024-12-01 00:40:09', '2024-12-01 00:44:35'),
(9, 2, 'Nei', 1, 'ledig', 'Nei', '2024-12-01 00:40:36', '2024-12-01 00:45:27'),
(10, 2, 'Nei', 1, 'ledig', 'Nei', '2024-12-01 00:40:54', '2024-12-01 00:45:34'),
(11, 3, 'Ja', 1, 'ledig', 'Nei', '2024-12-01 00:46:39', '2024-12-01 00:46:47'),
(12, 4, 'Ja', 1, 'ledig', 'Nei', '2024-12-01 00:47:07', '2024-12-01 00:48:00'),
(13, 4, 'Nei', 1, 'ledig', 'Nei', '2024-12-01 00:47:27', '2024-12-01 00:48:06'),
(14, 4, 'Nei', 1, 'ledig', 'Nei', '2024-12-01 00:47:39', '2024-12-01 00:48:10'),
(15, 6, 'Nei', 1, 'ledig', 'Nei', '2024-12-01 00:48:36', '2024-12-01 00:48:47'),
(16, 1, 'Nei', 2, 'ledig', 'Nei', '2024-12-01 00:38:00', '2024-12-01 00:43:36'),
(17, 1, 'Ja', 2, 'ledig', 'Nei', '2024-12-01 00:39:19', '2024-12-01 00:43:43'),
(18, 2, 'Nei', 2, 'ledig', 'Nei', '2024-12-01 00:41:16', '2024-12-01 00:45:49'),
(19, 2, 'Ja', 2, 'ledig', 'Nei', '2024-12-01 00:41:06', '2024-12-01 00:45:53'),
(20, 3, 'Ja', 2, 'ledig', 'Nei', '2024-12-01 00:49:45', '2024-12-01 00:50:10'),
(21, 3, 'Nei', 2, 'ledig', 'Nei', '2024-12-01 00:49:55', '2024-12-01 00:50:14'),
(22, 4, 'Ja', 2, 'ledig', 'Nei', '2024-12-01 00:50:45', '2024-12-01 00:51:05'),
(23, 4, 'Nei', 2, 'ledig', 'Nei', '2024-12-01 00:50:54', '2024-12-01 00:51:09'),
(24, 5, 'Nei', 2, 'ledig', 'Nei', '2024-12-01 00:51:20', '2024-12-01 00:51:48'),
(25, 6, 'Nei', 2, 'ledig', 'Nei', '2024-12-01 00:51:37', '2024-12-01 00:51:53');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `swx_room_type`
--

CREATE TABLE `swx_room_type` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `max_capacity` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `swx_room_type`
--

INSERT INTO `swx_room_type` (`type_id`, `type_name`, `description`, `max_capacity`, `price`) VALUES
(1, 'Standardrom', 'Vårt standardrom er perfekt for en komfortabel overnatting. Rommet har moderne møbler og en koselig atmosfære.', 2, 500),
(2, 'Dobbeltrom', 'Dobbeltrommet vårt gir ekstra plass og komfort for par eller familier som ønsker å dele oppholdet.', 4, 700),
(3, 'Superior Rom', 'Superior rommet tilbyr luksuriøse fasiliteter med en fantastisk utsikt over havet.', 4, 1000),
(4, 'Familie Suite', 'Familiesuiten er ideell for familier, med plass til opptil fire personer og separate soveområder.', 5, 1200),
(5, 'Honeymoon Suite', 'Vår honeymoon suite er perfekt for nygifte, med romantisk innredning og fantastisk havutsikt.', 2, 2200),
(6, 'Deluxerom', 'Deluxerommet tilbyr en høyere standard med eksklusive fasiliteter og en ekstra touch av luksus.', 4, 1500);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `swx_staff`
--

CREATE TABLE `swx_staff` (
  `staff_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `position` enum('Admin','Staff','Manager','Receptionist','Housekeeper','Maintenance') NOT NULL,
  `email` varchar(100) NOT NULL,
  `hired_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `login_attempts` int(11) NOT NULL DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `swx_staff`
--

INSERT INTO `swx_staff` (`staff_id`, `first_name`, `last_name`, `position`, `email`, `hired_date`, `updated_at`, `login_attempts`, `locked_until`, `password`) VALUES
(1, 'Admin', 'Role', 'Admin', 'admin@svalberg.no', '2024-11-27 20:03:35', '2024-11-27 20:42:43', 0, NULL, '$2y$10$.y9CIl3zFx8Hbn.ZDUdpqeOYX9pHFnYSuhzr0QKrzqO1XHz5C3p5K'),
(2, 'Staff', 'Role', 'Staff', 'staff@svalberg.no', '2024-11-27 20:03:35', '2024-11-27 20:03:35', 0, NULL, '$2y$10$ZwuHOt8RvpCWE3TH6GHvCOusrSlgvB8d8oUlVfbYCS3s53bVHmF.O');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `swx_users`
--

CREATE TABLE `swx_users` (
  `user_id` int(11) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `tlf` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user') NOT NULL,
  `point` int(11) DEFAULT NULL,
  `login_attempts` int(11) NOT NULL DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `swx_users`
--

INSERT INTO `swx_users` (`user_id`, `firstName`, `lastName`, `tlf`, `username`, `password`, `role`, `point`, `login_attempts`, `locked_until`) VALUES
(1, 'Ine', 'Antonsen', 12345678, 'inea@hotmail.com', '$2y$10$xMSJcTavF43bh.6wW7afhunlpjWQ09mlqKYadDO0gyTN9MQXJpKC6', 'user', NULL, 0, NULL),
(2, 'Anna', 'Dang', 11111111, 'adang@hotmail.com', '$2y$10$6gVPV70NUVb/39Vx.cuZgOPDMHqZoDg/YVFelEVuayjq7TpxHdBaC', 'user', NULL, 0, NULL),
(3, 'test', 'one', 12345678, 'test@hotmail.com', 'test123', 'user', NULL, 2, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `swx_booking`
--
ALTER TABLE `swx_booking`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `payment_id` (`payment_id`),
  ADD KEY `fk_booking_room` (`room_id`);

--
-- Indexes for table `swx_payment`
--
ALTER TABLE `swx_payment`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `swx_room`
--
ALTER TABLE `swx_room`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `room_type` (`room_type`);

--
-- Indexes for table `swx_room_type`
--
ALTER TABLE `swx_room_type`
  ADD PRIMARY KEY (`type_id`),
  ADD UNIQUE KEY `type_name` (`type_name`);

--
-- Indexes for table `swx_staff`
--
ALTER TABLE `swx_staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `swx_users`
--
ALTER TABLE `swx_users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `swx_booking`
--
ALTER TABLE `swx_booking`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `swx_payment`
--
ALTER TABLE `swx_payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `swx_room`
--
ALTER TABLE `swx_room`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `swx_room_type`
--
ALTER TABLE `swx_room_type`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `swx_staff`
--
ALTER TABLE `swx_staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `swx_users`
--
ALTER TABLE `swx_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Begrensninger for dumpede tabeller
--

--
-- Begrensninger for tabell `swx_booking`
--
ALTER TABLE `swx_booking`
  ADD CONSTRAINT `fk_booking_room` FOREIGN KEY (`room_id`) REFERENCES `swx_room` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `swx_booking_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `swx_users` (`user_id`),
  ADD CONSTRAINT `swx_booking_ibfk_3` FOREIGN KEY (`payment_id`) REFERENCES `swx_payment` (`payment_id`);

--
-- Begrensninger for tabell `swx_room`
--
ALTER TABLE `swx_room`
  ADD CONSTRAINT `swx_room_ibfk_1` FOREIGN KEY (`room_type`) REFERENCES `swx_room_type` (`type_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
