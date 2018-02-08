-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 08, 2018 at 11:44 AM
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
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` text NOT NULL,
  `style` text NOT NULL,
  `radio_controlled` varchar(5) NOT NULL,
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
  `topps_id` int(10) DEFAULT '0',
  `topps_front` mediumblob,
  `topps_rear` mediumblob,
  `tier_two` varchar(5) NOT NULL DEFAULT 'No'
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
  `username` varchar(25) DEFAULT NULL,
  `pli_date` date DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mot`
--

CREATE TABLE `mot` (
  `mot_uid` int(11) NOT NULL,
  `droid_uid` int(11) NOT NULL,
  `date` date NOT NULL,
  `location` text NOT NULL,
  `approval` varchar(5) NOT NULL,
  `annual_mot` varchar(5) NOT NULL,
  `struct_overall` varchar(5) NOT NULL,
  `struct_left_leg` varchar(5) NOT NULL,
  `struct_right_leg` varchar(5) NOT NULL,
  `struct_left_foot_ankle` varchar(5) NOT NULL,
  `struct_right_foot_ankle` varchar(5) NOT NULL,
  `struct_left_shoulder` varchar(5) NOT NULL,
  `struct_right_shoulder` varchar(5) NOT NULL,
  `struct_center_foot` varchar(5) NOT NULL,
  `struct_center_ankle` varchar(5) NOT NULL,
  `struct_body_skirt_frame` varchar(5) NOT NULL,
  `struct_dome_mech` varchar(5) NOT NULL,
  `struct_dome` varchar(5) NOT NULL,
  `struct_details` varchar(5) NOT NULL,
  `mech_center_wheel` varchar(5) NOT NULL,
  `mech_drive` varchar(5) NOT NULL,
  `mech_two_three_two` varchar(5) NOT NULL,
  `mech_dome` varchar(5) NOT NULL,
  `mech_utility_arms` varchar(5) NOT NULL,
  `mech_rear_door_skins` varchar(5) NOT NULL,
  `mech_doors` varchar(5) NOT NULL,
  `elec_overall` varchar(5) NOT NULL,
  `elec_transmitter` varchar(5) NOT NULL,
  `elec_receiver` varchar(5) NOT NULL,
  `elec_feet` varchar(5) NOT NULL,
  `elec_dome` varchar(5) NOT NULL,
  `elec_audio` varchar(5) NOT NULL,
  `elec_other` varchar(5) NOT NULL,
  `gadget_danger` varchar(5) NOT NULL,
  `gadget_1` varchar(5) NOT NULL,
  `gadget_2` varchar(5) NOT NULL,
  `gadget_3` varchar(5) NOT NULL,
  `gadget_4` varchar(5) NOT NULL,
  `drive_general` varchar(5) NOT NULL,
  `drive_dizzy` varchar(5) NOT NULL,
  `drive_boomerang` varchar(5) NOT NULL,
  `drive_gnaremoob` varchar(5) NOT NULL,
  `drive_eight` varchar(5) NOT NULL,
  `drive_speed` varchar(5) NOT NULL,
  `drive_estop` varchar(5) NOT NULL,
  `drive_dome_spin` varchar(5) NOT NULL,
  `drive_range` varchar(5) NOT NULL,
  `approved` varchar(5) NOT NULL,
  `user` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mot_comments`
--

CREATE TABLE `mot_comments` (
  `uid` int(11) NOT NULL,
  `mot_uid` int(11) NOT NULL,
  `comment` longtext NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `added_by` int(11) NOT NULL DEFAULT '0'
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
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `admin` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
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
-- Indexes for table `mot_comments`
--
ALTER TABLE `mot_comments`
  ADD PRIMARY KEY (`uid`);

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
  MODIFY `droid_uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `droid_comments`
--
ALTER TABLE `droid_comments`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `member_uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
--
-- AUTO_INCREMENT for table `mot`
--
ALTER TABLE `mot`
  MODIFY `mot_uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `mot_comments`
--
ALTER TABLE `mot_comments`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
