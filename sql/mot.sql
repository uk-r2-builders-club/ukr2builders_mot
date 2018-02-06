-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 06, 2018 at 08:24 PM
-- Server version: 5.6.38
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chequers_mot`
--

-- --------------------------------------------------------

--
-- Table structure for table `droids`
--

CREATE TABLE `droids` (
  `droid_uid` int(11) NOT NULL,
  `member_uid` int(11) NOT NULL,
  `name` text NOT NULL,
  `type` text NOT NULL,
  `style` text NOT NULL,
  `radio_controlled` tinyint(1) NOT NULL DEFAULT '0',
  `transmitter_type` text NOT NULL,
  `material` text NOT NULL,
  `weight` text NOT NULL,
  `battery` text NOT NULL,
  `drive_voltage` text NOT NULL,
  `sound_system` text NOT NULL,
  `value` text NOT NULL,
  `photo_side` mediumblob,
  `photo_front` mediumblob,
  `photo_rear` mediumblob,
  `tier_two` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `droid_comments`
--

CREATE TABLE `droid_comments` (
  `uid` int(11) NOT NULL,
  `droid_uid` int(11) NOT NULL,
  `comment` longtext NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `added_by` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `member_uid` int(11) NOT NULL,
  `forename` text,
  `surname` text,
  `email` text NOT NULL,
  `pli_date` timestamp NULL DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mot`
--

CREATE TABLE `mot` (
  `mot_uid` int(11) NOT NULL,
  `droid_uid` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `location` text NOT NULL,
  `approval` tinyint(1) NOT NULL,
  `annual_mot` tinyint(1) NOT NULL,
  `approved` tinyint(1) NOT NULL,
  `user` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_uid` int(11) NOT NULL,
  `username` varchar(25) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `email` text NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `droids`
--
ALTER TABLE `droids`
  ADD PRIMARY KEY (`droid_uid`);

--
-- Indexes for table `droid_comments`
--
ALTER TABLE `droid_comments`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`member_uid`);

--
-- Indexes for table `mot`
--
ALTER TABLE `mot`
  ADD PRIMARY KEY (`mot_uid`),
  ADD KEY `droid_uid` (`droid_uid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `droids`
--
ALTER TABLE `droids`
  MODIFY `droid_uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `droid_comments`
--
ALTER TABLE `droid_comments`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `member_uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `mot`
--
ALTER TABLE `mot`
  MODIFY `mot_uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
