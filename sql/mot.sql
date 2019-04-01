-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 01, 2019 at 11:32 AM
-- Server version: 5.6.43
-- PHP Version: 7.2.7

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
CREATE DATABASE IF NOT EXISTS `chequers_mot` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `chequers_mot`;

-- --------------------------------------------------------

--
-- Table structure for table `achievements`
--

DROP TABLE IF EXISTS `achievements`;
CREATE TABLE `achievements` (
  `achievement_uid` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `description` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `image` mediumblob,
  `icon` blob
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
  `config_uid` int(11) NOT NULL,
  `email_treasurer` varchar(50) NOT NULL,
  `email_mot` varchar(50) NOT NULL,
  `site_base` varchar(50) NOT NULL,
  `paypal_link` varchar(50) NOT NULL,
  `paypal_email` varchar(50) NOT NULL,
  `primary_cost` int(25) NOT NULL,
  `other_cost` int(5) NOT NULL,
  `google_map_api` varchar(48) DEFAULT NULL,
  `course_api` varchar(36) DEFAULT NULL,
  `from_email` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `course_runs`
--

DROP TABLE IF EXISTS `course_runs`;
CREATE TABLE `course_runs` (
  `run_uid` int(11) NOT NULL,
  `member_uid` int(11) NOT NULL,
  `droid_uid` int(11) NOT NULL,
  `first_half` int(11) NOT NULL,
  `second_half` int(11) NOT NULL,
  `clock_time` int(11) NOT NULL,
  `final_time` int(11) NOT NULL,
  `num_penalties` int(11) NOT NULL DEFAULT '0',
  `penalties` text,
  `dribble` int(1) NOT NULL DEFAULT '0',
  `run_timestamp` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `droids`
--

DROP TABLE IF EXISTS `droids`;
CREATE TABLE `droids` (
  `droid_uid` int(11) NOT NULL,
  `member_uid` int(11) NOT NULL,
  `name` text NOT NULL,
  `primary_droid` varchar(5) NOT NULL,
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
  `thumb_front` mediumblob,
  `thumb_side` mediumblob,
  `thumb_rear` mediumblob,
  `topps_id` int(10) DEFAULT '0',
  `topps_front` mediumblob,
  `topps_rear` mediumblob,
  `tier_two` varchar(5) NOT NULL DEFAULT 'No',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `rfid` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `droid_comments`
--

DROP TABLE IF EXISTS `droid_comments`;
CREATE TABLE `droid_comments` (
  `uid` int(11) NOT NULL,
  `droid_uid` int(11) NOT NULL,
  `comment` longtext NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `added_by` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `event_uid` int(11) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL,
  `charity_raised` float NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
CREATE TABLE `members` (
  `member_uid` int(11) NOT NULL,
  `forename` text,
  `surname` text,
  `county` varchar(50) DEFAULT NULL,
  `postcode` varchar(50) DEFAULT NULL,
  `latitude` varchar(16) DEFAULT NULL,
  `longitude` varchar(16) DEFAULT NULL,
  `email` text NOT NULL,
  `username` varchar(25) DEFAULT NULL,
  `pli_date` date DEFAULT NULL,
  `pli_active` varchar(5) DEFAULT NULL,
  `active` varchar(3) NOT NULL DEFAULT 'on',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL DEFAULT '0',
  `mug_shot` mediumblob,
  `mug_thumb` blob,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `password` varchar(60) DEFAULT NULL,
  `force_password` int(11) NOT NULL DEFAULT '0',
  `gdpr_accepted` int(11) NOT NULL DEFAULT '0',
  `last_login` timestamp NULL DEFAULT NULL,
  `last_login_from` varchar(16) DEFAULT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `role` varchar(10) NOT NULL DEFAULT 'user',
  `badge_id` text,
  `qr_code` blob
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `members_achievements`
--

DROP TABLE IF EXISTS `members_achievements`;
CREATE TABLE `members_achievements` (
  `uid` int(11) NOT NULL,
  `member_uid` int(11) NOT NULL,
  `achievement_uid` int(11) NOT NULL,
  `notes` text,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `added_by` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `members_events`
--

DROP TABLE IF EXISTS `members_events`;
CREATE TABLE `members_events` (
  `uid` int(11) NOT NULL,
  `event_uid` int(11) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `added_by` int(11) NOT NULL,
  `member_uid` int(11) NOT NULL,
  `details` text NOT NULL,
  `spotter` varchar(3) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mot`
--

DROP TABLE IF EXISTS `mot`;
CREATE TABLE `mot` (
  `mot_uid` int(11) NOT NULL,
  `droid_uid` int(11) NOT NULL,
  `date` date NOT NULL,
  `location` text NOT NULL,
  `approval` varchar(5) NOT NULL,
  `annual_mot` varchar(5) NOT NULL,
  `mot_type` varchar(10) NOT NULL DEFAULT 'Initial',
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
  `approved` varchar(15) NOT NULL,
  `user` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mot_comments`
--

DROP TABLE IF EXISTS `mot_comments`;
CREATE TABLE `mot_comments` (
  `uid` int(11) NOT NULL,
  `mot_uid` int(11) NOT NULL,
  `comment` longtext NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `added_by` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset`
--

DROP TABLE IF EXISTS `password_reset`;
CREATE TABLE `password_reset` (
  `id` int(11) NOT NULL,
  `member_uid` int(11) NOT NULL,
  `hash` text NOT NULL,
  `expire` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pli_cover_details`
--

DROP TABLE IF EXISTS `pli_cover_details`;
CREATE TABLE `pli_cover_details` (
  `uid` int(11) NOT NULL,
  `details` text NOT NULL,
  `body` text NOT NULL,
  `contact1` varchar(50) NOT NULL,
  `contact2` varchar(50) NOT NULL,
  `logo` blob NOT NULL,
  `footer_text` text NOT NULL,
  `header_text` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_uid` int(11) NOT NULL,
  `username` varchar(25) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `email` text NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `admin` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login_from` varchar(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `achievements`
--
ALTER TABLE `achievements`
  ADD UNIQUE KEY `achievement_uid` (`achievement_uid`);

--
-- Indexes for table `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`config_uid`);

--
-- Indexes for table `course_runs`
--
ALTER TABLE `course_runs`
  ADD PRIMARY KEY (`run_uid`);

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
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_uid`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`member_uid`);

--
-- Indexes for table `members_achievements`
--
ALTER TABLE `members_achievements`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `members_events`
--
ALTER TABLE `members_events`
  ADD UNIQUE KEY `uid` (`uid`);

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
-- Indexes for table `password_reset`
--
ALTER TABLE `password_reset`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `member_uid` (`member_uid`);

--
-- Indexes for table `pli_cover_details`
--
ALTER TABLE `pli_cover_details`
  ADD UNIQUE KEY `uid` (`uid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `achievements`
--
ALTER TABLE `achievements`
  MODIFY `achievement_uid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `config`
--
ALTER TABLE `config`
  MODIFY `config_uid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_runs`
--
ALTER TABLE `course_runs`
  MODIFY `run_uid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `droids`
--
ALTER TABLE `droids`
  MODIFY `droid_uid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `droid_comments`
--
ALTER TABLE `droid_comments`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_uid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `member_uid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `members_achievements`
--
ALTER TABLE `members_achievements`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `members_events`
--
ALTER TABLE `members_events`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mot`
--
ALTER TABLE `mot`
  MODIFY `mot_uid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mot_comments`
--
ALTER TABLE `mot_comments`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_reset`
--
ALTER TABLE `password_reset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pli_cover_details`
--
ALTER TABLE `pli_cover_details`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_uid` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
