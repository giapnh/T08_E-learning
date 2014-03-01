-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 01, 2014 at 12:15 PM
-- Server version: 5.5.32
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `elearning`
--
CREATE DATABASE IF NOT EXISTS `elearning` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `admin_ip`
--

CREATE TABLE IF NOT EXISTS `admin_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `learn`
--

CREATE TABLE IF NOT EXISTS `learn` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `register_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `lesson`
--

INSERT INTO `lesson` (`id`, `teacher_id`, `title`, `description`, `view`, `like`, `create_time`, `status`) VALUES
(1, 9, 'IT日本語', '用件定義、外部設計、内部設計、コーディング、テスト', 0, 0, '2014-03-01 11:02:36', 1),
(2, 9, 'C プログラミング言語', '基本的なC言語プログラミング。', 0, 0, '2014-03-01 10:57:28', 1),
(3, 9, '英語', '簡単な文法、漢字、会話、読解', 0, 0, '2014-03-01 11:02:33', 1),
(4, 9, '数学', '数学', 0, 0, '2014-03-01 11:03:26', 0),
(5, 9, '数学', '数学', 0, 0, '2014-03-01 11:03:44', 0),
(6, 9, '数学', '数学', 0, 0, '2014-03-01 11:03:49', 0),
(7, 9, '数学', '数学', 0, 0, '2014-03-01 11:03:54', 0),
(8, 9, '数学', '数学', 0, 0, '2014-03-01 11:03:58', 0);

-- --------------------------------------------------------

--
-- Table structure for table `lesson_file`
--

CREATE TABLE IF NOT EXISTS `lesson_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `description` varchar(512) NOT NULL,
  `location` varchar(255) NOT NULL,
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `lesson_tag`
--

INSERT INTO `lesson_tag` (`id`, `lesson_id`, `tag_id`) VALUES
(1, 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `like`
--

CREATE TABLE IF NOT EXISTS `like` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `question_answer_list`
--

CREATE TABLE IF NOT EXISTS `question_answer_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `answer` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `tag`
--

INSERT INTO `tag` (`id`, `tag_name`) VALUES
(1, 'C　言語'),
(2, '英語'),
(3, 'PHP'),
(4, 'IT　日本語'),
(5, '数学');

-- --------------------------------------------------------

--
-- Table structure for table `test_detail`
--

CREATE TABLE IF NOT EXISTS `test_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) NOT NULL,
  `comment` varchar(512) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `test_question`
--

CREATE TABLE IF NOT EXISTS `test_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) NOT NULL,
  `question` varchar(512) NOT NULL,
  `true` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `test_result`
--

CREATE TABLE IF NOT EXISTS `test_result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `learn_id` int(11) NOT NULL,
  `test_file_id` int(11) NOT NULL,
  `point` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `test_user_answer`
--

CREATE TABLE IF NOT EXISTS `test_user_answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `test_result_id` int(11) NOT NULL,
  `test_question_id` int(11) NOT NULL,
  `selected` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `first_password`, `password`, `name`, `sex`, `email`, `birthday`, `address`, `phone`, `bank_account`, `first_secret_question`, `first_secret_answer`, `secret_question`, `secret_answer`, `role`, `status`, `last_login_ip`, `fail_login_count`, `last_login_time`) VALUES
(7, 'giap_huu', '58aee8e76dfdf89c2d5c41de957767e8', '58aee8e76dfdf89c2d5c41de957767e8', 'Nguyá»…n Há»¯u GiÃ¡p', 0, 'hgbk.it@gmail.com', '17-4-1998', 'Hoang Mai', '01664643234', '711A948594898', '1+1=', '2', '1+1=', '2', 1, 2, NULL, 0, NULL),
(8, 'giapnh', '521fa11ddef0190b157ce6f5aa602659', 'c4ec9911d93f93e36dcb77bd18c3e3d7', 'Nguyá»…n Há»¯u GiÃ¡p', 0, 'hgbk.it@gmail.com', '29-2-1995', 'Hoang Mai', '01664643234', '711A948594898', '1+1=', '2', '1+1=', '23', 1, 1, NULL, 0, NULL),
(9, 'kachimasu', '32b3c555e5eeb9e9f7e3b41cf62a2771', '32b3c555e5eeb9e9f7e3b41cf62a2771', 'Nguyá»…n Há»¯u GiÃ¡p', 0, 'hgbk.it@gmail.com', '1-3-2012', 'HoÃ ng Mai', '01664643234', '711A948594898', '1+1=', '2', '1+1=', '2', 2, 1, NULL, 0, NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
