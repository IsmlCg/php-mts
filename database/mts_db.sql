-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 15, 2023 at 06:49 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `mts_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `medicine_list`
--

CREATE TABLE `medicine_list` (
  `id` bigint(30) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `medicine_list`
--

INSERT INTO `medicine_list` (`id`, `user_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 2, 'Sample Medicine 101', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. ', '2023-03-15 10:37:18', '2023-03-15 10:42:52'),
(2, 2, 'Medicine 102', 'Donec elementum bibendum iaculis. Suspendisse ultricies pellentesque mi eget mattis. Sed velit dolor, pulvinar vitae scelerisque varius, congue id sapien', '2023-03-15 10:43:23', '2023-03-15 10:43:36'),
(3, 2, 'Medicine 103', 'Cras commodo euismod purus, a fermentum quam vulputate in. Duis ut dapibus est, eget efficitur dolor.', '2023-03-15 10:44:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `schedule_list`
--

CREATE TABLE `schedule_list` (
  `id` bigint(30) NOT NULL,
  `user_id` int(11) NOT NULL,
  `medicine_id` bigint(30) NOT NULL,
  `day` text NOT NULL,
  `date_start` date NOT NULL,
  `until` date DEFAULT NULL,
  `time` time NOT NULL,
  `remarks` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `schedule_list`
--

INSERT INTO `schedule_list` (`id`, `user_id`, `medicine_id`, `day`, `date_start`, `until`, `time`, `remarks`, `created_at`, `updated_at`) VALUES
(2, 2, 2, 'Sunday, Tuesday, Wednesday, Thursday', '2023-03-15', NULL, '12:00:00', 'Aliquam augue purus, venenatis a ullamcorper in, ullamcorper et massa.', '2023-03-15 11:53:03', '2023-03-15 13:35:07'),
(3, 2, 3, 'Monday, Wednesday, Friday', '2023-03-15', NULL, '08:00:00', 'Phasellus nec nisi tellus. Vivamus quis tristique nisl.', '2023-03-15 13:12:43', '2023-03-15 13:12:43');

-- --------------------------------------------------------

--
-- Table structure for table `system_info`
--

CREATE TABLE `system_info` (
  `id` int(30) NOT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `system_info`
--

INSERT INTO `system_info` (`id`, `meta_field`, `meta_value`) VALUES
(1, 'name', 'Medicine Tracking System - PHP'),
(6, 'short_name', 'MTS - PHP'),
(11, 'logo', 'uploads/defaults/logo.png?v=1648173882'),
(14, 'cover', 'uploads/defaults/wallpaper.jpg?v=1648173974');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(250) NOT NULL,
  `middlename` text DEFAULT NULL,
  `lastname` varchar(250) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `middlename`, `lastname`, `username`, `password`, `last_login`, `date_added`, `date_updated`) VALUES
(2, 'Mark', 'D', 'Cooper', 'mcooper', '$2y$10$vbzwnysblhRRKERu5ZlPUu0hS06sG4r6R4gmy5hp47P.61AiNJk/C', NULL, '2023-03-15 10:15:13', '2023-03-15 10:15:13'),
(3, 'George', '', 'Wilson', 'gwilson', '$2y$10$QjC3qnuEZfajGRttQQVovOA9OByc.BL8X.oS0uereL8hxIlBS/i5m', NULL, '2023-03-15 13:48:38', '2023-03-15 13:48:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `medicine_list`
--
ALTER TABLE `medicine_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `med_user_id_fk` (`user_id`);

--
-- Indexes for table `schedule_list`
--
ALTER TABLE `schedule_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_fk` (`user_id`),
  ADD KEY `medicine_id_fk` (`medicine_id`);

--
-- Indexes for table `system_info`
--
ALTER TABLE `system_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `medicine_list`
--
ALTER TABLE `medicine_list`
  MODIFY `id` bigint(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `schedule_list`
--
ALTER TABLE `schedule_list`
  MODIFY `id` bigint(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `medicine_list`
--
ALTER TABLE `medicine_list`
  ADD CONSTRAINT `med_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `schedule_list`
--
ALTER TABLE `schedule_list`
  ADD CONSTRAINT `medicine_id_fk` FOREIGN KEY (`medicine_id`) REFERENCES `medicine_list` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;
