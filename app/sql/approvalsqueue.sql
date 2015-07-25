-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 25, 2015 at 10:38 PM
-- Server version: 5.6.24
-- PHP Version: 5.6.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `approvalsqueue`
--
CREATE DATABASE IF NOT EXISTS `approvalsqueue` DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci;
USE `approvalsqueue`;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE IF NOT EXISTS `courses` (
  `id_course` int(10) unsigned NOT NULL,
  `name` varchar(40) COLLATE latin1_general_ci NOT NULL,
  `descrip` varchar(200) COLLATE latin1_general_ci NOT NULL,
  `id_added_by` int(10) unsigned NOT NULL,
  `id_coordinator` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- RELATIONS FOR TABLE `courses`:
--   `id_added_by`
--       `users` -> `id_user`
--   `id_coordinator`
--       `users` -> `id_user`
--

-- --------------------------------------------------------

--
-- Table structure for table `queues`
--

CREATE TABLE IF NOT EXISTS `queues` (
  `id_queue` int(10) unsigned NOT NULL,
  `queue_type` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `name` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `descrip` varchar(200) COLLATE latin1_general_ci NOT NULL,
  `order_by` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `id_course` int(10) unsigned NOT NULL,
  `id_user` int(10) unsigned NOT NULL,
  `is_enabled` bit(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- RELATIONS FOR TABLE `queues`:
--   `id_course`
--       `courses` -> `id_course`
--

-- --------------------------------------------------------

--
-- Table structure for table `queue_elements`
--

CREATE TABLE IF NOT EXISTS `queue_elements` (
  `id_elements` int(10) unsigned NOT NULL,
  `id_queue` int(10) unsigned NOT NULL,
  `name` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `descrip` varchar(200) COLLATE latin1_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- RELATIONS FOR TABLE `queue_elements`:
--   `id_queue`
--       `queues` -> `id_queue`
--

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id_role` int(10) unsigned NOT NULL,
  `name` varchar(20) COLLATE latin1_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- RELATIONS FOR TABLE `roles`:
--

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE IF NOT EXISTS `submissions` (
  `id_submission` int(10) unsigned NOT NULL,
  `id_user` int(10) unsigned NOT NULL,
  `id_queue` int(10) unsigned NOT NULL,
  `status` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `is_instructor_approval` bit(2) NOT NULL,
  `created_by` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_last` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- RELATIONS FOR TABLE `submissions`:
--   `id_queue`
--       `queues` -> `id_queue`
--   `id_user`
--       `users` -> `id_user`
--

-- --------------------------------------------------------

--
-- Table structure for table `submission_attachments`
--

CREATE TABLE IF NOT EXISTS `submission_attachments` (
  `id_attachments` int(10) unsigned NOT NULL,
  `id_submission` int(10) unsigned NOT NULL,
  `name` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `content_type` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `content` blob NOT NULL,
  `comment` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_last` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- RELATIONS FOR TABLE `submission_attachments`:
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id_user` int(10) unsigned NOT NULL,
  `first_name` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `last_name` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `email` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `major` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `password` varchar(200) COLLATE latin1_general_ci NOT NULL,
  `id_advisor` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- RELATIONS FOR TABLE `users`:
--   `id_advisor`
--       `users` -> `id_user`
--

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE IF NOT EXISTS `user_roles` (
  `id_user` int(10) unsigned NOT NULL,
  `id_role` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- RELATIONS FOR TABLE `user_roles`:
--   `id_user`
--       `users` -> `id_user`
--   `id_role`
--       `roles` -> `id_role`
--

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id_course`), ADD KEY `id_added_by` (`id_added_by`), ADD KEY `id_coordinator` (`id_coordinator`);

--
-- Indexes for table `queues`
--
ALTER TABLE `queues`
  ADD PRIMARY KEY (`id_queue`), ADD KEY `id_course` (`id_course`);

--
-- Indexes for table `queue_elements`
--
ALTER TABLE `queue_elements`
  ADD PRIMARY KEY (`id_elements`), ADD KEY `id_queue` (`id_queue`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_role`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id_submission`), ADD KEY `id_queue` (`id_queue`), ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `submission_attachments`
--
ALTER TABLE `submission_attachments`
  ADD PRIMARY KEY (`id_attachments`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`), ADD KEY `id_advisor` (`id_advisor`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id_user`,`id_role`), ADD KEY `id_role` (`id_role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id_course` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `queues`
--
ALTER TABLE `queues`
  MODIFY `id_queue` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `queue_elements`
--
ALTER TABLE `queue_elements`
  MODIFY `id_elements` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id_submission` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `submission_attachments`
--
ALTER TABLE `submission_attachments`
  MODIFY `id_attachments` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`id_added_by`) REFERENCES `users` (`id_user`),
ADD CONSTRAINT `courses_ibfk_2` FOREIGN KEY (`id_coordinator`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `queues`
--
ALTER TABLE `queues`
ADD CONSTRAINT `queues_ibfk_1` FOREIGN KEY (`id_course`) REFERENCES `courses` (`id_course`);

--
-- Constraints for table `queue_elements`
--
ALTER TABLE `queue_elements`
ADD CONSTRAINT `queue_elements_ibfk_1` FOREIGN KEY (`id_queue`) REFERENCES `queues` (`id_queue`);

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`id_queue`) REFERENCES `queues` (`id_queue`),
ADD CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_advisor`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
