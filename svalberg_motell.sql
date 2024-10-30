-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 30. Okt, 2024 18:23 PM
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
-- Tabellstruktur for tabell `booking`
--

CREATE TABLE `booking` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `number_of_guests` int(11) NOT NULL,
  `status` enum('Pending','Confirmed','Cancelled') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `booking`
--

INSERT INTO `booking` (`booking_id`, `user_id`, `room_id`, `check_in_date`, `check_out_date`, `number_of_guests`, `status`, `created_at`, `updated_at`) VALUES
(2, 3, 1, '2024-10-01', '2024-10-02', 1, 'Pending', '2024-10-30 17:21:07', '2024-10-30 17:21:07');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` enum('Cash','Credit Card','Bank Transfer') NOT NULL,
  `status` enum('Pending','Completed','Failed') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `room`
--

CREATE TABLE `room` (
  `room_id` int(11) NOT NULL,
  `room_type` int(11) NOT NULL,
  `availability` enum('ledig','opptatt') NOT NULL,
  `under_construction` enum('Ja','Nei') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `room`
--

INSERT INTO `room` (`room_id`, `room_type`, `availability`, `under_construction`, `created_at`, `updated_at`) VALUES
(1, 1, 'ledig', 'Nei', '2024-10-30 16:42:13', '2024-10-30 16:42:13'),
(2, 2, 'ledig', 'Nei', '2024-10-30 16:42:13', '2024-10-30 16:42:13'),
(3, 3, 'ledig', 'Nei', '2024-10-30 16:42:48', '2024-10-30 16:42:48'),
(4, 4, 'ledig', 'Nei', '2024-10-30 16:42:48', '2024-10-30 16:53:40'),
(5, 5, 'ledig', 'Nei', '2024-10-30 16:43:24', '2024-10-30 16:43:24'),
(6, 6, 'ledig', 'Nei', '2024-10-30 16:43:24', '2024-10-30 16:43:24');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `room_type`
--

CREATE TABLE `room_type` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `max_capacity` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `room_type`
--

INSERT INTO `room_type` (`type_id`, `type_name`, `description`, `max_capacity`, `price`) VALUES
(1, 'Standardrom', 'Vårt standardrom er perfekt for en komfortabel overnatting. Rommet har moderne møbler og en koselig atmosfære.', 2, 500),
(2, 'Dobbeltrom', 'Dobbeltrommet vårt gir ekstra plass og komfort for par eller familier som ønsker å dele oppholdet.', 4, 700),
(3, 'Superior Rom', 'Superior rommet tilbyr luksuriøse fasiliteter med en fantastisk utsikt over havet.', 4, 1000),
(4, 'Familie Suite', 'Familiesuiten er ideell for familier, med plass til opptil fire personer og separate soveområder.', 5, 1200),
(5, 'Honeymoon Suite', 'Vår honeymoon suite er perfekt for nygifte, med romantisk innredning og fantastisk havutsikt.', 2, 2200),
(6, 'Deluxerom', 'Deluxerommet tilbyr en høyere standard med eksklusive fasiliteter og en ekstra touch av luksus.', 4, 1500);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `position` enum('Manager','Receptionist','Housekeeper','Maintenance') NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `hired_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `staff`
--

INSERT INTO `staff` (`staff_id`, `name`, `position`, `email`, `phone`, `hired_date`, `updated_at`) VALUES
(1, 'jane doe', 'Manager', 'janed@svalberg.no', '77666666', '2024-10-30 16:47:26', '2024-10-30 16:47:26'),
(2, 'justin bieber', 'Housekeeper', 'jb@svalberg.no', '12345678', '2024-10-30 16:48:16', '2024-10-30 16:48:16');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','staff') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`) VALUES
(3, 'test@hotmail.com', 'test123', 'user'),
(4, 'ny.test@hotmail.com', 'test123', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `room_type` (`room_type`);

--
-- Indexes for table `room_type`
--
ALTER TABLE `room_type`
  ADD PRIMARY KEY (`type_id`),
  ADD UNIQUE KEY `type_name` (`type_name`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `room_type`
--
ALTER TABLE `room_type`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Begrensninger for dumpede tabeller
--

--
-- Begrensninger for tabell `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `room_type` (`type_id`),
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Begrensninger for tabell `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`) ON DELETE CASCADE;

--
-- Begrensninger for tabell `room`
--
ALTER TABLE `room`
  ADD CONSTRAINT `room_ibfk_1` FOREIGN KEY (`room_type`) REFERENCES `room_type` (`type_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
