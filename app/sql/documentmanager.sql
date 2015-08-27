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
  `name` varchar(80) NOT NULL,
  `description` varchar(200) NOT NULL,
  `id_added_by` int(10) unsigned NOT NULL,
  `id_coordinator` int(10) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Seed data for table `courses`
--

INSERT INTO `courses` (`id_course`, `name`, `description`, `id_added_by`, `id_coordinator`) VALUES
    (1, 'CS105 (Introduction To Scientific Computing)', 'CS105 (Introduction To Scientific Computing)', 1, 3),
    (2, 'CS488 (Computer  Architecture)', 'CS488 (Computer  Architecture)', 1, 3),
    (3, 'CS510 (Principles of Programming Languages)', 'CS510 (Principles of Programming Languages)', 1, 3),
    (4, 'CS513 or SOC550 (Knowledge Discovery and Data Mining)', 'CS513 or SOC550 (Knowledge Discovery and Data Mining)', 1, 3),
    (5, 'CS546 (Web Programming)', 'CS546 (Web Programming)', 1, 3),
    (6, 'CS548 or SOC542 (Enterprise Software Architecture and Design)', 'CS548 or SOC542 (Enterprise Software Architecture and Design)', 1, 3),
    (7, 'CS549 (Distributed Systems and Cloud Computing)', 'CS549 (Distributed Systems and Cloud Computing)', 1, 3),
    (8, 'CS558 (Computer Vision)', 'CS558 (Computer Vision)', 1, 3),
    (9, 'SSW533 (Software Cost Estimation and Metrics)', 'SSW533 (Software Cost Estimation and Metrics)', 1, 4),
    (10, 'SSW540 (Fundamentals of Software Engineering)', 'SSW540 (Fundamentals of Software Engineering)', 1, 4),
    (11, 'SSW541 (Fundamentals of Software Engineering for Non-Software Engineering)', 'SSW541 (Fundamentals of Software Engineering for Non-Software Engineering)', 1, 4),
    (12, 'SSW555 (Agile Methods for Software Development)', 'SSW555 (Agile Methods for Software Development)', 1, 4),
    (13, 'SSW556 (Software Development for Trusted Systems)', 'SSW556 (Software Development for Trusted Systems)', 1, 4),
    (14, 'SSW564/SOC521 (Software Requirements Analysis and Engineering)', 'SSW564/SOC521 (Software Requirements Analysis and Engineering)', 1, 4),
    (15, 'SSW565 (Software Architecture and Component-based Design)', 'SSW565 (Software Architecture and Component-based Design)', 1, 4),
    (16, 'SYS501 (Probability and Statistics for Systems Engineering)', 'SYS501 (Probability and Statistics for Systems Engineering)', 1, 5),
    (17, 'SYS595 (Design of Experiments and Optimization)', 'SYS595 (Design of Experiments and Optimization)', 1, 5),
    (18, 'SYS605 (Systems Integration)', 'SYS605 (Systems Integration)', 1, 5),
    (19, 'SYS632 (Designing Space Missions and Systems)', 'SYS632 (Designing Space Missions and Systems)', 1, 5),
    (20, 'SYS635 (Human Spaceflight)', 'SYS635 (Human Spaceflight)', 1, 5),
    (21, 'SYS636 (Space Launch and Transportation Systems)', 'SYS636 (Space Launch and Transportation Systems)', 1, 5),
    (22, 'SYS637 (Cost-Effective Space Mission Operations)', 'SYS637 (Cost-Effective Space Mission Operations)', 1, 5);
-- --------------------------------------------------------

--
-- Table structure for table `queues`
--

DROP TABLE IF EXISTS `queues`;
CREATE TABLE IF NOT EXISTS `queues` (
  `id_queue` int(10) unsigned NOT NULL,
  `queueable_type` varchar(30) NOT NULL,
  `queueable_id` varchar(30) NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(200) NOT NULL,
  `is_enabled` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Seed data for table `queues`
--

INSERT INTO `queues` (`id_queue`, `queueable_type`, `queueable_id`, `name`, `description`, `is_enabled`) VALUES
    (1, 'DocManager\\Course\\Course', 1, 'Add/Drop', 'Add/Drop Queue', 1),
    (2, 'DocManager\\Course\\Course', 2, 'Add/Drop', 'Add/Drop Queue', 1),
    (3, 'DocManager\\Course\\Course', 3, 'Add/Drop', 'Add/Drop Queue', 1),
    (4, 'DocManager\\Course\\Course', 4, 'Add/Drop', 'Add/Drop Queue', 1),
    (5, 'DocManager\\Course\\Course', 5, 'Add/Drop', 'Add/Drop Queue', 1),
    (6, 'DocManager\\Course\\Course', 6, 'Add/Drop', 'Add/Drop Queue', 1),
    (7, 'DocManager\\Course\\Course', 7, 'Add/Drop', 'Add/Drop Queue', 1),
    (8, 'DocManager\\Course\\Course', 8, 'Add/Drop', 'Add/Drop Queue', 1),
    (9, 'DocManager\\Course\\Course', 9, 'Add/Drop', 'Add/Drop Queue', 1),
    (10, 'DocManager\\Course\\Course', 10, 'Add/Drop', 'Add/Drop Queue', 1),
    (11, 'DocManager\\Course\\Course', 11, 'Add/Drop', 'Add/Drop Queue', 1),
    (12, 'DocManager\\Course\\Course', 12, 'Add/Drop', 'Add/Drop Queue', 1),
    (13, 'DocManager\\Course\\Course', 13, 'Add/Drop', 'Add/Drop Queue', 1),
    (14, 'DocManager\\Course\\Course', 14, 'Add/Drop', 'Add/Drop Queue', 1),
    (15, 'DocManager\\Course\\Course', 15, 'Add/Drop', 'Add/Drop Queue', 1),
    (16, 'DocManager\\Course\\Course', 16, 'Add/Drop', 'Add/Drop Queue', 1),
    (17, 'DocManager\\Course\\Course', 17, 'Add/Drop', 'Add/Drop Queue', 1),
    (18, 'DocManager\\Course\\Course', 18, 'Add/Drop', 'Add/Drop Queue', 1),
    (19, 'DocManager\\Course\\Course', 19, 'Add/Drop', 'Add/Drop Queue', 1),
    (20, 'DocManager\\Course\\Course', 20, 'Add/Drop', 'Add/Drop Queue', 1),
    (21, 'DocManager\\Course\\Course', 21, 'Add/Drop', 'Add/Drop Queue', 1),
    (22, 'DocManager\\Course\\Course', 22, 'Add/Drop', 'Add/Drop Queue', 1);
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

--
-- Seed data for table `queue_elements`
--

INSERT INTO `queue_elements` (`id_element`, `id_queue`, `name`, `description`) VALUES
    (1, 1, 'Add/Drop Form', 'Stevens Add/Drop Form'),
    (2, 2, 'Add/Drop Form', 'Stevens Add/Drop Form'),
    (3, 3, 'Add/Drop Form', 'Stevens Add/Drop Form'),
    (4, 4, 'Add/Drop Form', 'Stevens Add/Drop Form'),
    (5, 5, 'Add/Drop Form', 'Stevens Add/Drop Form'),
    (6, 6, 'Add/Drop Form', 'Stevens Add/Drop Form'),
    (7, 7, 'Add/Drop Form', 'Stevens Add/Drop Form'),
    (8, 8, 'Add/Drop Form', 'Stevens Add/Drop Form'),
    (9, 9, 'Add/Drop Form', 'Stevens Add/Drop Form'),
    (10, 10, 'Add/Drop Form', 'Stevens Add/Drop Form'),
    (11, 11, 'Add/Drop Form', 'Stevens Add/Drop Form'),
    (12, 12, 'Add/Drop Form', 'Stevens Add/Drop Form'),
    (13, 13, 'Add/Drop Form', 'Stevens Add/Drop Form'),
    (14, 14, 'Add/Drop Form', 'Stevens Add/Drop Form'),
    (15, 15, 'Add/Drop Form', 'Stevens Add/Drop Form'),
    (16, 16, 'Add/Drop Form', 'Stevens Add/Drop Form'),
    (17, 17, 'Add/Drop Form', 'Stevens Add/Drop Form'),
    (18, 18, 'Add/Drop Form', 'Stevens Add/Drop Form'),
    (19, 19, 'Add/Drop Form', 'Stevens Add/Drop Form'),
    (20, 20, 'Add/Drop Form', 'Stevens Add/Drop Form'),
    (21, 21, 'Add/Drop Form', 'Stevens Add/Drop Form'),
    (22, 22, 'Add/Drop Form', 'Stevens Add/Drop Form');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `id_comment` int(10) unsigned NOT NULL,
  `commentable_type` varchar(40) NOT NULL,
  `commentable_id` varchar(30) NOT NULL,
  `comment` varchar(200) NOT NULL,
  `id_user` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
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
-- Seed data for table `roles`
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
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
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
  'size' int(10) NOT NULL,
  `content_type` varchar(20) NOT NULL,
  `content` mediumblob NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
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
  `id_advisor` int(10) unsigned NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Seed data for table `users`
--

INSERT INTO `users` (`id_user`, `first_name`, `last_name`, `email`, `major`, `password`, `id_advisor`) VALUES
(1, 'Steven', 'Admin', 'admin@stevens.edu', 'Everything', '$2y$10$nUP1PoLDK9PlykwCLpjJXe3OoTbtgFIxZda3W6rIn9BBiVkE0iyXW', 0),
(2, 'Steven', 'Advisor', 'advisor@stevens.edu', 'Advising', '$2y$10$ifdjyxR1p47U.unYP/QEJOI6y77U/aqpdXj5uzObQFfuCBWLUq.Ae', 0),
(3, 'Steven', 'Instructor1', 'instructor1@stevens.edu', 'Instructing', '$2y$10$BUFpslEqI7nAJzQ6AAyZd.QQzE/X0CCB85zyTT8x21jMJLHSHuiZO', 0),
(4, 'Steven', 'Instructor2', 'instructor2@stevens.edu', 'Instructing', '$2y$10$BUFpslEqI7nAJzQ6AAyZd.QQzE/X0CCB85zyTT8x21jMJLHSHuiZO', 0),
(5, 'Steven', 'Instructor3', 'instructor3@stevens.edu', 'Instructing', '$2y$10$BUFpslEqI7nAJzQ6AAyZd.QQzE/X0CCB85zyTT8x21jMJLHSHuiZO', 0),
(6, 'Steven', 'Student1', 'student1@stevens.edu', 'Studying', '$2y$10$v8lu2g8s5rXR10jWSFv50eEBiMzM4L2dzKmiFwVbWbMucrB7Nx1AS', 2),
(7, 'Steven', 'Student2', 'student2@stevens.edu', 'Studying', '$2y$10$v8lu2g8s5rXR10jWSFv50eEBiMzM4L2dzKmiFwVbWbMucrB7Nx1AS', 2);

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
-- Seed data for table `user_roles`
--

INSERT INTO `user_roles` (`id_user`, `id_role`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 2),
(3, 3),
(4, 3),
(5, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id_course`),

--
-- Indexes for table `queues`
--
ALTER TABLE `queues`
  ADD PRIMARY KEY (`id_queue`);

--
-- Indexes for table `queue_elements`
--
ALTER TABLE `queue_elements`
  ADD PRIMARY KEY (`id_element`),

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id_comment`),

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
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id_comment` int(10) unsigned NOT NULL AUTO_INCREMENT;
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