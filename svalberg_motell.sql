-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 01. Des, 2024 17:35 PM
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
(46, 7, 7, 49, '2025-03-07', '2025-03-08', 2, 'ine antonsen', 'ine@uia.no', '4712121212', 'nopp', '2024-12-01 16:31:19', '2024-12-01 16:31:19'),
(47, 7, 13, 50, '2024-12-27', '2024-12-29', 2, 'andrine flydal', 'a.f@gmail.com', '4755555555', 'hei', '2024-12-01 16:33:32', '2024-12-01 16:33:32');

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
(1, 2000, '2024-11-09 21:31:03', 'Vips', NULL, 'Completed'),
(2, 4000, '2024-11-09 21:31:03', 'Credit Card', NULL, 'Completed'),
(7, 1900, '2024-11-13 14:04:41', 'Vips', NULL, 'Completed'),
(8, 1300, '2024-11-13 14:06:47', 'Vips', NULL, 'Completed'),
(9, 1300, '2024-11-13 14:20:41', 'Vips', NULL, 'Completed'),
(10, 3000, '2024-11-13 14:25:53', 'Vips', NULL, 'Completed'),
(11, 3000, '2024-11-27 13:13:55', 'Vips', NULL, 'Completed'),
(12, 1300, '2024-11-27 13:19:12', 'Vips', NULL, 'Completed'),
(13, 1300, '2024-11-27 13:22:15', 'Vips', NULL, 'Completed'),
(14, 1900, '2024-11-27 13:25:28', 'Vips', NULL, 'Completed'),
(15, 1300, '2024-11-27 22:22:41', 'invoice', NULL, 'Completed'),
(19, 1300, '2024-11-28 12:43:40', 'invoice', 'faktura_19_1732797820.pdf', 'Completed'),
(20, 1900, '2024-11-28 12:45:47', 'invoice', 'faktura_20_1732797947.pdf', 'Completed'),
(21, 1300, '2024-11-28 12:49:01', 'invoice', 'faktura_21_1732798141.pdf', 'Completed'),
(22, 1300, '2024-11-28 14:10:26', 'invoice', 'faktura_22_1732803026.pdf', 'Completed'),
(23, 1300, '2024-11-28 16:14:45', 'invoice', 'faktura_23_1732810485.pdf', 'Completed'),
(24, 1300, '2024-11-28 16:17:15', 'invoice', 'faktura_24_1732810635.pdf', 'Completed'),
(25, 1300, '2024-11-28 16:22:56', 'invoice', 'faktura_25_1732810976.pdf', 'Completed'),
(26, 1300, '2024-11-28 17:32:45', 'invoice', 'faktura_26_1732815165.pdf', 'Completed'),
(27, 3000, '2024-11-28 19:50:33', 'Vips', NULL, 'Completed'),
(28, 1800, '2024-11-30 16:14:08', 'Vips', NULL, 'Completed'),
(29, 2000, '2024-11-30 16:18:16', 'Vips', NULL, 'Completed'),
(30, 1300, '2024-12-01 10:39:24', 'Vips', NULL, 'Completed'),
(31, 1300, '2024-12-01 14:32:03', 'Vips', NULL, 'Completed'),
(32, 1300, '2024-12-01 15:03:46', 'Vips', NULL, 'Completed'),
(49, 1500, '2024-12-01 16:31:19', 'invoice', 'faktura_49_1733070680.pdf', 'Completed'),
(50, 2000, '2024-12-01 16:33:32', 'invoice', 'faktura_50_1733070812.pdf', 'Completed');

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
(1, 1, 'Nei', 1, 'ledig', 'Nei', '2024-12-01 01:35:42', '2024-12-01 01:36:14'),
(2, 1, 'Nei', 1, 'ledig', 'Nei', '2024-12-01 01:36:58', '2024-12-01 01:38:14'),
(3, 1, 'Nei', 1, 'ledig', 'Nei', '2024-12-01 01:37:11', '2024-12-01 01:38:18'),
(4, 1, 'Nei', 1, 'ledig', 'Nei', '2024-12-01 01:37:27', '2024-12-01 01:37:27'),
(5, 1, 'Nei', 1, 'ledig', 'Nei', '2024-12-01 01:37:27', '2024-12-01 01:38:30'),
(6, 1, 'Nei', 1, 'ledig', 'Nei', '2024-12-01 01:37:45', '2024-12-01 01:38:41'),
(7, 2, 'Ja', 1, 'ledig', 'Nei', '2024-12-01 01:40:09', '2024-12-01 01:44:17'),
(8, 2, 'Ja', 1, 'ledig', 'Nei', '2024-12-01 01:40:09', '2024-12-01 01:44:35'),
(9, 2, 'Nei', 1, 'ledig', 'Nei', '2024-12-01 01:40:36', '2024-12-01 01:45:27'),
(10, 2, 'Nei', 1, 'ledig', 'Nei', '2024-12-01 01:40:54', '2024-12-01 01:45:34'),
(11, 3, 'Ja', 1, 'ledig', 'Nei', '2024-12-01 01:46:39', '2024-12-01 01:46:47'),
(12, 4, 'Ja', 1, 'ledig', 'Nei', '2024-12-01 01:47:07', '2024-12-01 01:48:00'),
(13, 4, 'Nei', 1, 'ledig', 'Nei', '2024-12-01 01:47:27', '2024-12-01 01:48:06'),
(14, 4, 'Nei', 1, 'ledig', 'Nei', '2024-12-01 01:47:39', '2024-12-01 01:48:10'),
(15, 6, 'Nei', 1, 'ledig', 'Nei', '2024-12-01 01:48:36', '2024-12-01 01:48:47'),
(16, 1, 'Nei', 2, 'ledig', 'Nei', '2024-12-01 01:38:00', '2024-12-01 01:43:36'),
(17, 1, 'Ja', 2, 'ledig', 'Nei', '2024-12-01 01:39:19', '2024-12-01 01:43:43'),
(18, 2, 'Nei', 2, 'ledig', 'Nei', '2024-12-01 01:41:16', '2024-12-01 01:45:49'),
(19, 2, 'Ja', 2, 'ledig', 'Nei', '2024-12-01 01:41:06', '2024-12-01 01:45:53'),
(20, 3, 'Ja', 2, 'ledig', 'Nei', '2024-12-01 01:49:45', '2024-12-01 01:50:10'),
(21, 3, 'Nei', 2, 'ledig', 'Nei', '2024-12-01 01:49:55', '2024-12-01 01:50:14'),
(22, 4, 'Ja', 2, 'ledig', 'Nei', '2024-12-01 01:50:45', '2024-12-01 01:51:05'),
(23, 4, 'Nei', 2, 'ledig', 'Nei', '2024-12-01 01:50:54', '2024-12-01 01:51:09'),
(24, 5, 'Nei', 2, 'ledig', 'Nei', '2024-12-01 01:51:20', '2024-12-01 01:51:48'),
(25, 6, 'Nei', 2, 'ledig', 'Nei', '2024-12-01 01:51:37', '2024-12-01 01:51:53');

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
  `phone` varchar(15) NOT NULL,
  `hired_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `login_attempts` int(11) NOT NULL DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `swx_staff`
--

INSERT INTO `swx_staff` (`staff_id`, `first_name`, `last_name`, `position`, `email`, `phone`, `hired_date`, `updated_at`, `login_attempts`, `locked_until`, `password`) VALUES
(1, 'Admin', 'Role', 'Admin', 'admin@svalberg.no', '77777777', '2024-11-27 21:03:35', '2024-11-27 21:42:43', 0, NULL, '$2y$10$.y9CIl3zFx8Hbn.ZDUdpqeOYX9pHFnYSuhzr0QKrzqO1XHz5C3p5K'),
(2, 'Staff', 'Role', 'Staff', 'staff@svalberg.no', '12345678', '2024-11-27 21:03:35', '2024-11-27 21:03:35', 0, NULL, '$2y$10$ZwuHOt8RvpCWE3TH6GHvCOusrSlgvB8d8oUlVfbYCS3s53bVHmF.O');

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
(3, 'test', 'one', 12345678, 'test@hotmail.com', 'test123', 'user', NULL, 2, NULL),
(4, 'bob', 'kåre', 11111111, 'ny.test@hotmail.com', 'test123', 'user', NULL, 0, NULL),
(5, 'jane', 'doe', 22222222, 'ikke_innlogget@gmail.com', 'test123', 'user', NULL, 0, NULL),
(6, 'test', 'test', 95451705, 'test@test.com', '$2y$10$IMjbILxh0VKdICqhBlO3W.576TypbG5kqVfmrHVbS3twWEUm7F6ry', 'user', NULL, 2, NULL),
(7, 'Danny', 'Elfman', 77666666, 'DannyE@gmail.com', '$2y$10$.Y/6JcPWx5EetHWm/wg2SuMDaG1whiQeUCzQGLjOwGj7j8CcMo4yq', 'user', 6100, 0, NULL),
(8, 'sebastian', 'yatra', 12345678, 's.y@gmail.com', '$2y$10$hu2PbdZVr41Xo9ZH0MCdZusFYlL/fDoRP5IYXEvgLEBHlmFbgQrTO', 'user', NULL, 0, NULL);

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
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `swx_payment`
--
ALTER TABLE `swx_payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
