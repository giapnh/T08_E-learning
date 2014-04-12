-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 12, 2014 at 07:20 AM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `elearning`
--
CREATE DATABASE IF NOT EXISTS `elearning` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `elearning`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `create_admin` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `fail_login` int(11) NOT NULL,
  `log_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `create_admin`, `status`, `fail_login`, `log_time`) VALUES
(1, 'superadmin', 'd30616e1ddd84300435908a971c2bff116229a18', 1, 1, 0, '2014-04-09 03:48:59');

-- --------------------------------------------------------

--
-- Table structure for table `admin_ip`
--

CREATE TABLE IF NOT EXISTS `admin_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `admin_ip`
--

INSERT INTO `admin_ip` (`id`, `admin_id`, `ip`) VALUES
(2, 1, '127.0.0.1');

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `comment` varchar(512) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table `copyright_report`
--

CREATE TABLE IF NOT EXISTS `copyright_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `file_comment`
--

CREATE TABLE IF NOT EXISTS `file_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` varchar(512) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `learn`
--

CREATE TABLE IF NOT EXISTS `learn` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `register_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(11) NOT NULL COMMENT '1: Active, 0:Lock',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `lesson`
--

CREATE TABLE IF NOT EXISTS `lesson` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `teacher_id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` varchar(512) NOT NULL,
  `view` int(11) NOT NULL DEFAULT '0',
  `like` int(11) DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '1: Active; 0: inactive',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=47 ;

--
-- Dumping data for table `lesson`
--

INSERT INTO `lesson` (`id`, `teacher_id`, `title`, `description`, `view`, `like`, `create_time`, `status`) VALUES
(43, 18, 'IT 日本語第5週', 'IT 日本語', 1, 0, '2014-04-12 06:30:34', 1),
(44, 18, 'IT 日本語第6週', 'IT 日本語', 0, 0, '2014-04-11 07:20:48', 1),
(45, 18, 'IT 日本語第7週', 'IT 日本語', 0, 0, '2014-04-11 07:21:13', 1),
(46, 18, 'IT 日本語第8週', 'IT 日本語', 0, 0, '2014-04-11 07:22:14', 1);

-- --------------------------------------------------------

--
-- Table structure for table `lesson_file`
--

CREATE TABLE IF NOT EXISTS `lesson_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `description` varchar(512) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `subtitle` varchar(200) DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=74 ;

--
-- Dumping data for table `lesson_file`
--

INSERT INTO `lesson_file` (`id`, `lesson_id`, `filename`, `description`, `title`, `subtitle`, `location`) VALUES
(65, 43, '05W-01-内部設計についての所感.pdf', '', '', '', 'files\\43\\13972007920.pdf'),
(66, 43, '05W-02-プログラミングについて.pdf', '', '', '', 'files\\43\\13972007921.pdf'),
(67, 43, '05W-03-第２回プロジェクト報告会について.pdf', '', '', '', 'files\\43\\13972007922.pdf'),
(68, 44, '06W-01-反省について.pdf', '', '', '', 'files\\44\\13972008480.pdf'),
(69, 45, '07W-01-第２回プロジェクト報告会についての所感.pdf', '', '', '', 'files\\45\\13972008730.pdf'),
(70, 45, '07W-02-今後のスケジュールについて.pdf', '', '', '', 'files\\45\\13972008820.pdf'),
(71, 45, '07W-04-結合試験仕様書について.pdf', '', '', '', 'files\\45\\13972008890.pdf'),
(72, 46, '08W-01-試験工程について.pdf', '', '', '', 'files\\46\\13972009340.pdf'),
(73, 46, '08W-02-第３回プロジェクト報告会について.pdf', '', '', '', 'files\\46\\13972009341.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `lesson_like`
--

CREATE TABLE IF NOT EXISTS `lesson_like` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `lesson_tag`
--

CREATE TABLE IF NOT EXISTS `lesson_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;

--
-- Dumping data for table `lesson_tag`
--

INSERT INTO `lesson_tag` (`id`, `lesson_id`, `tag_id`) VALUES
(26, 43, 12),
(27, 44, 12),
(28, 45, 12),
(29, 46, 12);

-- --------------------------------------------------------

--
-- Table structure for table `master`
--

CREATE TABLE IF NOT EXISTS `master` (
  `master_key` varchar(100) CHARACTER SET latin1 NOT NULL,
  `master_value` varchar(100) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`master_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `master`
--

INSERT INTO `master` (`master_key`, `master_value`) VALUES
('BACKUP_TIME', '30'),
('COMA_PRICE', '20000'),
('FILE_LOCATION', 'backupfolder&'),
('LESSON_DEADLINE', '7'),
('LOCK_COUNT', '5'),
('LOGIN_FAIL_LOCK_TIME', '3600'),
('PASSWORD_CONST', '100'),
('SESSION_TIME', '86400'),
('TEACHER_FEE_RATE', '60'),
('VIOLATION_TIME', '20');

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE IF NOT EXISTS `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) NOT NULL,
  `title` varchar(11) NOT NULL,
  `answer` varchar(11) NOT NULL,
  `point` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=51 ;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`id`, `file_id`, `title`, `answer`, `point`) VALUES
(31, 56, 'Q2', 'S4', 10),
(32, 56, 'Q3', 'S3', 9),
(33, 56, 'Q4', 'S1', 16),
(34, 57, 'Q1', 'S1', 10),
(35, 57, 'Q2', 'S4', 5),
(36, 57, 'Q3', 'S3', 5),
(37, 57, 'Q4', 'S1', 20),
(38, 57, 'Q5', 'S2', 10),
(39, 58, 'Q1', 'S1', 10),
(40, 58, 'Q2', 'S4', 5),
(41, 58, 'Q3', 'S3', 5),
(42, 58, 'Q4', 'S1', 20),
(43, 63, 'Q1', 'S1', 20),
(44, 63, 'Q2', 'S4', 10),
(45, 63, 'Q3', 'S3', 9),
(46, 63, 'Q4', 'S1', 16),
(47, 64, 'Q1', 'S1', 20),
(48, 64, 'Q2', 'S4', 10),
(49, 64, 'Q3', 'S3', 9),
(50, 64, 'Q4', 'S1', 16);

-- --------------------------------------------------------

--
-- Table structure for table `result`
--

CREATE TABLE IF NOT EXISTS `result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `learn_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `tag`
--

INSERT INTO `tag` (`id`, `tag_name`) VALUES
(12, 'IT 日本語');

-- --------------------------------------------------------

--
-- Table structure for table `test_result`
--

CREATE TABLE IF NOT EXISTS `test_result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `learn_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `result` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `first_password` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sex` int(1) NOT NULL DEFAULT '0' COMMENT '0: boy, 1:girl',
  `email` varchar(100) DEFAULT NULL,
  `birthday` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `bank_account` varchar(30) NOT NULL,
  `first_secret_question` varchar(255) NOT NULL,
  `first_secret_answer` varchar(255) NOT NULL,
  `secret_question` varchar(255) NOT NULL,
  `secret_answer` varchar(255) NOT NULL,
  `role` int(11) NOT NULL DEFAULT '1' COMMENT '1: student,2:teacher',
  `status` int(11) NOT NULL DEFAULT '2' COMMENT '1: avaiable; 2:wait for confirm by admin; 3:block',
  `last_login_ip` varchar(255) DEFAULT NULL,
  `fail_login_count` int(11) NOT NULL DEFAULT '0',
  `last_login_time` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `first_password`, `password`, `name`, `sex`, `email`, `birthday`, `address`, `phone`, `bank_account`, `first_secret_question`, `first_secret_answer`, `secret_question`, `secret_answer`, `role`, `status`, `last_login_ip`, `fail_login_count`, `last_login_time`) VALUES
(18, 'thayminh', 'ee3b2589291406d311f4e147f77d5eed231376be', 'ee3b2589291406d311f4e147f77d5eed231376be', 'Minh Tran', 0, 'tminhhp@gmail.com', '13-10-2001', '45 Trần Đại Nghĩa', '0987654321', '711A12334534', 'nick gg', 'hnimnart', 'nick gg', 'hnimnart', 2, 1, '127.0.0.1', 1, ''),
(19, 'minhtq', 'ef5734619870d64cfee9b0abda57fea2b60c7e59', 'ef5734619870d64cfee9b0abda57fea2b60c7e59', 'Trần Quang Minh', 0, 'tminh_1234@yahoo.com', '17-6-2001', '42 Lê Thanh Nghị', '0987654321', '711A12334534', 'nick gg', 'hnimnart', 'nick gg', 'hnimnart', 1, 1, '127.0.0.1', 0, '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
