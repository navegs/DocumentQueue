-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 01, 2015 at 01:48 PM
-- Server version: 5.5.28-log
-- PHP Version: 5.6.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `document_manager`
--
CREATE DATABASE IF NOT EXISTS `document_manager` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `document_manager`;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
CREATE TABLE IF NOT EXISTS `courses` (
  `id_course` int(10) unsigned NOT NULL,
  `name` varchar(40) NOT NULL,
  `description` varchar(200) NOT NULL,
  `id_added_by` int(10) unsigned NOT NULL,
  `id_coordinator` int(10) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id_course`, `name`, `description`, `id_added_by`, `id_coordinator`) VALUES
(1, 'Course 1', 'Proin aliquet magna eget justo facilisis, in egestas libero iaculis. In rhoncus non ante quis tincidunt. Pellentesque magna ex, fringilla finibus lectus consectetur, euismod rutrum velit', 1, 3),
(2, 'Course 2', 'Sed eget elit sit amet tellus vehicula iaculis ut non neque. Etiam sit amet massa vel libero facilisis efficitur vitae laoreet enim. Fusce at interdum erat, in pulvinar arcu. Phasellus suscipit ante.', 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `queues`
--

DROP TABLE IF EXISTS `queues`;
CREATE TABLE IF NOT EXISTS `queues` (
  `id_queue` int(10) unsigned NOT NULL,
  `queue_type` varchar(30) NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(200) NOT NULL,
  `order_by` varchar(20) NOT NULL,
  `id_course` int(10) unsigned NOT NULL,
  `id_user` int(10) unsigned NOT NULL,
  `is_enabled` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `queue_elements`
--

DROP TABLE IF EXISTS `queue_elements`;
CREATE TABLE IF NOT EXISTS `queue_elements` (
  `id_element` int(10) unsigned NOT NULL,
  `id_queue` int(10) unsigned NOT NULL,
  `name` varchar(20) NOT NULL,
  `description` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id_role` int(10) unsigned NOT NULL,
  `name` varchar(20) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id_role`, `name`) VALUES
(1, 'ADMIN'),
(2, 'ADVISOR'),
(3, 'INSTRUCTOR');

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

DROP TABLE IF EXISTS `submissions`;
CREATE TABLE IF NOT EXISTS `submissions` (
  `id_submission` int(10) unsigned NOT NULL,
  `id_user` int(10) unsigned NOT NULL,
  `id_queue` int(10) unsigned NOT NULL,
  `status` varchar(20) NOT NULL,
  `is_approved` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_last` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `submission_attachments`
--

DROP TABLE IF EXISTS `submission_attachments`;
CREATE TABLE IF NOT EXISTS `submission_attachments` (
  `id_attachment` int(10) unsigned NOT NULL,
  `id_submission` int(10) unsigned NOT NULL,
  `name` varchar(30) NOT NULL,
  `content_type` varchar(20) NOT NULL,
  `content` blob NOT NULL,
  `comment` varchar(500) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_last` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id_user` int(10) unsigned NOT NULL,
  `first_name` varchar(30) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `major` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `id_advisor` int(10) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `first_name`, `last_name`, `email`, `major`, `password`, `id_advisor`) VALUES
(1, 'Steven', 'Admin', 'admin@stevens.edu', 'Everything', '$2y$10$nUP1PoLDK9PlykwCLpjJXe3OoTbtgFIxZda3W6rIn9BBiVkE0iyXW', 0),
(2, 'Steven', 'Advisor', 'advisor@stevens.edu', 'Advising', '$2y$10$ifdjyxR1p47U.unYP/QEJOI6y77U/aqpdXj5uzObQFfuCBWLUq.Ae', 0),
(3, 'Steven', 'Instructor', 'instructor@stevens.edu', 'Instructing', '$2y$10$BUFpslEqI7nAJzQ6AAyZd.QQzE/X0CCB85zyTT8x21jMJLHSHuiZO', 0),
(4, 'Steven', 'Student', 'student@stevens.edu', 'Studying', '$2y$10$v8lu2g8s5rXR10jWSFv50eEBiMzM4L2dzKmiFwVbWbMucrB7Nx1AS', 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE IF NOT EXISTS `user_roles` (
  `id_user` int(10) unsigned NOT NULL,
  `id_role` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id_user`, `id_role`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 2),
(3, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id_course`),
  ADD KEY `id_added_by` (`id_added_by`),
  ADD KEY `id_coordinator` (`id_coordinator`);

--
-- Indexes for table `queues`
--
ALTER TABLE `queues`
  ADD PRIMARY KEY (`id_queue`),
  ADD KEY `id_course` (`id_course`);

--
-- Indexes for table `queue_elements`
--
ALTER TABLE `queue_elements`
  ADD PRIMARY KEY (`id_element`),
  ADD KEY `id_queue` (`id_queue`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_role`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id_submission`),
  ADD KEY `id_queue` (`id_queue`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `submission_attachments`
--
ALTER TABLE `submission_attachments`
  ADD PRIMARY KEY (`id_attachment`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id_user`,`id_role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id_course` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `queues`
--
ALTER TABLE `queues`
  MODIFY `id_queue` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `queue_elements`
--
ALTER TABLE `queue_elements`
  MODIFY `id_element` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id_submission` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `submission_attachments`
--
ALTER TABLE `submission_attachments`
  MODIFY `id_attachment` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;