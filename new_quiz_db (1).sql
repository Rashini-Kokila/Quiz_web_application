-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: Mar 13, 2023 at 10:20 AM
-- Server version: 5.7.28
-- PHP Version: 7.4.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `new_quiz_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `last_update` int(11) NOT NULL,
  `is_expired` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `user_name`, `email`, `password`, `last_update`, `is_expired`) VALUES
(1, 'tharanga', 'tharanga@gmail.com', '0e98336af521200f30abe20cbc82da51', 0, 0),
(2, 'rashini', 'rashini@gmail.com', '11885176144492e302f9eb2d475e792d', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

DROP TABLE IF EXISTS `answers`;
CREATE TABLE IF NOT EXISTS `answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `answer` varchar(250) NOT NULL,
  `is_correct` int(3) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `last_update` datetime(6) NOT NULL,
  `is_expired` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`id`, `question_id`, `answer`, `is_correct`, `modified_by`, `last_update`, `is_expired`) VALUES
(1, 1, 'Information and Communication Technology', 1, 2, '2023-03-02 09:48:39.000000', 0),
(2, 1, 'Information Technology', 0, 2, '2023-03-02 09:48:39.000000', 0),
(3, 1, 'Introduction Communication Technology', 0, 2, '2023-03-02 09:48:39.000000', 0),
(8, 4, 'encryption', 0, 2, '2023-03-02 05:03:37.000000', 0),
(9, 4, 'modifiability', 1, 2, '2023-03-02 05:03:37.000000', 0),
(10, 4, 'integrity', 0, 2, '2023-03-02 05:03:37.000000', 0),
(11, 4, 'scalability', 1, 2, '2023-03-02 05:03:37.000000', 0),
(17, 3, 'all colors', 1, 2, '2023-03-02 09:47:58.000000', 0),
(18, 3, 'red', 1, 2, '2023-03-02 09:47:58.000000', 0),
(19, 3, 'white', 1, 2, '2023-03-02 09:47:58.000000', 0),
(23, 5, 'model view computer', 0, 2, '2023-03-02 11:23:15.000000', 0),
(24, 5, 'model view and Controller', 1, 2, '2023-03-02 11:23:15.000000', 0),
(25, 5, 'model view Controller', 1, 2, '2023-03-02 11:23:15.000000', 0);

-- --------------------------------------------------------

--
-- Table structure for table `answer_type`
--

DROP TABLE IF EXISTS `answer_type`;
CREATE TABLE IF NOT EXISTS `answer_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(250) NOT NULL,
  `value` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `answer_type`
--

INSERT INTO `answer_type` (`id`, `type`, `value`) VALUES
(1, 'Text Answer', '3'),
(2, 'Single Answer', '1'),
(3, 'Multiple Answer', '2');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(250) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `last_update` datetime(6) NOT NULL,
  `is_expired` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `category_name`, `modified_by`, `last_update`, `is_expired`) VALUES
(18, 'category-last test', 1, '2023-02-10 08:47:33.000000', 0),
(19, 'category-2', 0, '2023-01-13 09:33:39.000000', 0),
(20, 'category-3', 0, '2023-01-13 09:33:47.000000', 0),
(21, 'blll', 0, '2023-01-13 09:35:08.000000', 1),
(22, 'vgsfhdgd', 0, '2023-01-13 09:37:31.000000', 0),
(23, 'hfadljasa', 0, '2023-01-13 09:40:51.000000', 1);

-- --------------------------------------------------------

--
-- Table structure for table `cookies_data`
--

DROP TABLE IF EXISTS `cookies_data`;
CREATE TABLE IF NOT EXISTS `cookies_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cookie_det` varchar(250) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

DROP TABLE IF EXISTS `question`;
CREATE TABLE IF NOT EXISTS `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quiz_id` int(11) NOT NULL,
  `sequence` int(11) NOT NULL,
  `question` varchar(250) NOT NULL,
  `image` varchar(250) NOT NULL,
  `answer_type` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `last_update` datetime(6) NOT NULL,
  `is_expired` int(3) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_id` (`quiz_id`),
  KEY `answer_type` (`answer_type`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`id`, `quiz_id`, `sequence`, `question`, `image`, `answer_type`, `modified_by`, `last_update`, `is_expired`) VALUES
(1, 1, 1, 'what is the standard of ICT', 'images (11).jpg ', 2, 2, '2023-03-02 09:48:39.000000', 0),
(2, 1, 2, 'what is the standard of ict', 'images.png', 1, 2, '2023-03-02 04:21:49.000000', 0),
(3, 2, 1, 'color of birds', 'download (3).jpg ', 2, 2, '2023-03-02 09:47:58.000000', 0),
(4, 2, 2, 'General Software quality Attributes', '', 3, 2, '2023-03-02 05:03:37.000000', 0),
(5, 3, 1, 'MVC mean?', 'images (12).jpg ', 3, 2, '2023-03-02 11:23:15.000000', 0);

-- --------------------------------------------------------

--
-- Table structure for table `quiz`
--

DROP TABLE IF EXISTS `quiz`;
CREATE TABLE IF NOT EXISTS `quiz` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `description` varchar(250) NOT NULL,
  `time` int(11) NOT NULL,
  `image` varchar(256) NOT NULL,
  `category_id` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `last_update` datetime NOT NULL,
  `is_expired` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `quiz`
--

INSERT INTO `quiz` (`id`, `title`, `description`, `time`, `image`, `category_id`, `modified_by`, `last_update`, `is_expired`) VALUES
(1, 'ICT', 'Information and Communication Technology', 20, 'images (7).jpg', 19, 2, '2023-03-02 04:06:24', 0),
(2, 'CS', 'Computer Science', 30, 'images (11).jpg', 20, 2, '2023-03-02 05:08:47', 0),
(3, 'software', 'software architecture', 3000, 'images (14).jpg', 18, 2, '2023-03-02 11:20:21', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `last_modified` int(11) NOT NULL,
  `is_expired` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `user_name`, `email`, `password`, `last_modified`, `is_expired`) VALUES
(1, 'kokila', 'kokila@gmail.com', '7f63baceb6592561696c164ea0e0090a', 0, 0),
(2, 'wijayananda', 'wijaya@gmail.com', '052b3f850f108d96399490c306b578da', 0, 0),
(3, 'saman', 'saman@gmail.com', '51ee742f19a38a4aa56ae06891974475', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_answers`
--

DROP TABLE IF EXISTS `user_answers`;
CREATE TABLE IF NOT EXISTS `user_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL,
  `answer` varchar(250) NOT NULL,
  `last_update` datetime(6) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `is_correct` int(3) NOT NULL,
  `expired` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_quiz`
--

DROP TABLE IF EXISTS `user_quiz`;
CREATE TABLE IF NOT EXISTS `user_quiz` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `quiz_start_time` datetime NOT NULL,
  `quiz_end_time` datetime DEFAULT NULL,
  `modified_by` int(11) NOT NULL,
  `last_update` datetime NOT NULL,
  `expired` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_quiz`
--

INSERT INTO `user_quiz` (`id`, `user_id`, `quiz_id`, `quiz_start_time`, `quiz_end_time`, `modified_by`, `last_update`, `expired`) VALUES
(1, 1, 3, '2023-03-13 06:16:05', '2023-03-13 07:06:05', 0, '2023-03-13 07:06:05', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
