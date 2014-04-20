-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2014 at 07:35 PM
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `create_admin`, `status`, `fail_login`, `log_time`) VALUES
(1, 'superadmin', '732a25fa821c3228e4e58274a5a5703d7c27cdc3', 1, 1, 0, '2014-04-16 13:37:00');

-- --------------------------------------------------------

--
-- Table structure for table `admin_ip`
--

CREATE TABLE IF NOT EXISTS `admin_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`id`, `user_id`, `lesson_id`, `comment`, `time`) VALUES
(1, 23, 49, '楽しみにしてください', '2014-04-14 07:48:28'),
(2, 1, 43, 'OK, thanks :D', '2014-04-16 01:49:49'),
(3, 1, 43, 'OK, thanks :D', '2014-04-16 01:49:51'),
(4, 1, 43, 'OK, thanks :D', '2014-04-16 01:49:59'),
(5, 1, 43, 'OK, thanks', '2014-04-16 01:51:49'),
(6, 1, 43, 'OK, thanks', '2014-04-16 01:53:47');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `copyright_report`
--

INSERT INTO `copyright_report` (`id`, `user_id`, `file_id`, `reason`, `status`) VALUES
(2, 19, 65, 'deo biet', 0),
(3, 19, 65, 'abc', 1),
(4, 19, 65, 'hahaha', 0);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `file_comment`
--

INSERT INTO `file_comment` (`id`, `file_id`, `user_id`, `comment`, `time`) VALUES
(1, 65, 19, 'Hi all', '2014-04-16 01:26:57'),
(2, 65, 19, 'Are you ok?', '2014-04-16 01:33:02'),
(3, 65, 1, 'asdf', '2014-04-16 02:00:45'),
(4, 65, 1, 'asdf', '2014-04-16 02:01:46'),
(5, 65, 1, 'asdf', '2014-04-16 02:02:26'),
(6, 65, 1, 'asdf', '2014-04-16 02:03:42'),
(7, 65, 1, 'asdf', '2014-04-16 02:03:51'),
(8, 65, 1, 'asdf', '2014-04-16 02:03:53'),
(9, 65, 1, 'asdf', '2014-04-16 02:04:02');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `learn`
--

INSERT INTO `learn` (`id`, `student_id`, `lesson_id`, `register_time`, `status`) VALUES
(11, 19, 43, '2014-04-12 07:46:13', 1),
(12, 19, 50, '2014-04-18 10:58:19', 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=51 ;

--
-- Dumping data for table `lesson`
--

INSERT INTO `lesson` (`id`, `teacher_id`, `title`, `description`, `view`, `like`, `create_time`, `status`) VALUES
(43, 18, 'IT 日本語第5週', 'IT 日本語', 2, 0, '2014-04-16 07:29:20', 0),
(44, 18, 'IT 日本語第6週', 'IT 日本語', 0, 0, '2014-04-11 07:20:48', 1),
(45, 18, 'IT 日本語第7週', 'IT 日本語', 0, 0, '2014-04-11 07:21:13', 1),
(46, 18, 'IT 日本語第8週', 'IT 日本語', 0, 0, '2014-04-11 07:22:14', 1),
(47, 18, 'クラウド化でシステム運用担当者は業務量が減る', '前回までは、システム運用における「5つの間違い」について述べてきた。それでは、このような間違いに陥らないためにはどのようにしたらよいだろうか。そのためには、しっかりとしたシステムの運用設計が必要である。それらは体制や仕組みなど、トータルの設計だ', 0, 0, '2014-04-14 07:16:23', 1),
(48, 22, '世界各地から開発者が集結！日本初のDrupal Camp、京都で開催', '4月12日、春の京都は祇園、しかも神社という絶好のロケーションで、日本で初めてのDrupal Camp、「Drupal Camp Japan Kyoto 2014」が開催された。Drupalは世界で最も普及しているオープンソースのCMSであるが、これまで日本での知名度は低いままだった。しかし、昨年来から採用事例も大幅に増え、コミュニティの活動も活発になってきている。', 0, 0, '2014-04-14 07:43:23', 1),
(49, 23, 'キャラクターが声で天気予報を教えてくれるアプリを作る', '今回は、指定した地域の天気予報を、キャラクターが音声で案内してくれるアプリを作ります。最近は萌えキャラやゆるキャラなど、さまざまなキャラクターを目にする機会が多いので、独自のキャラクターを使ってアプリを作りたい方に向いているのではないでしょうか。', 0, 0, '2014-04-14 07:48:01', 1),
(50, 18, '地球によく似た惑星 ＮＡＳＡが発見', 'アメリカのＮＡＳＡ＝航空宇宙局は、地球とほぼ同じ大きさで、水が液体の状態で存在する可能性がある、地球によく似た惑星を発見したと発表し、生命が存在しうる惑星の探査につながる成果として注目されています', 1, 0, '2014-04-18 10:58:12', 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=95 ;

--
-- Dumping data for table `lesson_file`
--

INSERT INTO `lesson_file` (`id`, `lesson_id`, `filename`, `description`, `title`, `subtitle`, `location`) VALUES
(65, 43, '05W-01-内部設計についての所感.pdf', '', '', '', '\\43\\13972007920.pdf'),
(66, 43, '05W-02-プログラミングについて.pdf', '', '', '', '\\43\\13972007921.pdf'),
(67, 43, '05W-03-第２回プロジェクト報告会について.pdf', '', '', '', '\\43\\13972007922.pdf'),
(68, 44, '06W-01-反省について.pdf', '', '', '', '\\44\\13972008480.pdf'),
(69, 45, '07W-01-第２回プロジェクト報告会についての所感.pdf', '', '', '', '\\45\\13972008730.pdf'),
(70, 45, '07W-02-今後のスケジュールについて.pdf', '', '', '', '\\45\\13972008820.pdf'),
(71, 45, '07W-04-結合試験仕様書について.pdf', '', '', '', '\\45\\13972008890.pdf'),
(72, 46, '08W-01-試験工程について.pdf', '', '', '', '\\46\\13972009340.pdf'),
(73, 46, '08W-02-第３回プロジェクト報告会について.pdf', '', '', '', '\\46\\13972009341.pdf'),
(74, 47, 'database.png', 'イメージファイル', '', '', '\\47\\13974597830.png'),
(75, 47, 'Andrea Bocelli & Hayley Westenra -- Vivo Per Lei.mp4', 'ビデオファイル', '', '', '\\47\\13974597831.mp4'),
(76, 47, 'test3.tsv', 'テスト', '2006年度2級文字・語彙', '次の56から60の言葉の使い方として最も適当なものを、1、2、3、4から一つ選びなさい。', '\\47\\13974597832.html'),
(77, 48, 'test2.tsv', 'テストファイル', '2006年度2級', '文字・語彙', '\\48\\13974614030.html'),
(78, 49, 'Avril Lavigne - What The Hell.mp4', 'ビデオファイル１', '', '', '\\49\\13974616800.mp4'),
(79, 49, 'Eminem - Love The Way You Lie ft. Rihanna.mp4', 'Rihana', '', '', '\\49\\13974616811.mp4'),
(80, 50, 'Tricoloring.png', '', '', '', '\\50\\13978066710.png'),
(81, 50, '1280px-Vector_Field.gif', '', '', '', '\\50\\13978066711.gif'),
(82, 50, 'Christmas-Gifs_02.gif', '', '', '', '\\50\\13978066712.gif'),
(83, 50, 'Enternal Flame - Atomic Kitten.mp3', '', '', '', '\\50\\13978066713.mp3'),
(84, 50, 'guitar-fire.jpg', '', '', '', '\\50\\13978066714.jpg'),
(85, 50, 'Avril Lavigne - What The Hell.mp4', '', '', '', '\\50\\13978066715.mp4'),
(86, 50, 'M2M_2004_Many-to-Many Communication_A New Approach for Collaboration in MANET.pdf', '', '', '', '\\50\\13978066716.pdf'),
(87, 50, 'Tricoloring.png', '', '', '', '\\50\\13978066717.png'),
(88, 50, 'test3.tsv', '', '2006年度2級文字・語彙', '次の56から60の言葉の使い方として最も適当なものを、1、2、3、4から一つ選びなさい。', '\\50\\13978066718.html'),
(89, 50, 'Justin Bieber - -As Long As You Love Me ft. Big Sean- (Cover by Tiffany Alvord).wav', '', '', '', '\\50\\13978066719.wav'),
(91, 43, 'mario.png', 'asdf', '', '', '\\43\\13978776580.png'),
(92, 43, 'Andrea Bocelli & Hayley Westenra -- Vivo Per Lei.mp4', '', '', '', '\\43\\13978788710.mp4'),
(93, 43, 'Avril Lavigne - Wish You Were Here.mp4', 'asf', '', '', '\\43\\13978815440.mp4'),
(94, 43, 'Avril Lavigne - What The Hell.mp4', 'asdf', '', '', '\\43\\13978816000.mp4');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39 ;

--
-- Dumping data for table `lesson_tag`
--

INSERT INTO `lesson_tag` (`id`, `lesson_id`, `tag_id`) VALUES
(26, 43, 12),
(27, 44, 12),
(28, 45, 12),
(29, 46, 12),
(30, 47, 12),
(31, 47, 13),
(32, 47, 14),
(33, 48, 12),
(34, 48, 14),
(35, 49, 13),
(36, 49, 15),
(37, 49, 16),
(38, 50, 17);

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
('BACKUP_TIME', '3630'),
('COMA_PRICE', '20000'),
('FILE_LOCATION', 'files'),
('LESSON_DEADLINE', '7'),
('LOCK_COUNT', '3'),
('LOGIN_FAIL_LOCK_TIME', '10'),
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=68 ;

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
(50, 64, 'Q4', 'S1', 16),
(51, 76, 'Q1', 'S1', 20),
(52, 76, 'Q2', 'S4', 10),
(53, 76, 'Q3', 'S3', 9),
(54, 76, 'Q4', 'S1', 16),
(55, 77, 'Q1', 'S1', 10),
(56, 77, 'Q2', 'S4', 5),
(57, 77, 'Q3', 'S3', 5),
(58, 77, 'Q4', 'S1', 20),
(59, 77, 'Q5', 'S2', 10),
(60, 88, 'Q1', 'S1', 20),
(61, 88, 'Q2', 'S4', 10),
(62, 88, 'Q3', 'S3', 9),
(63, 88, 'Q4', 'S1', 16),
(64, 90, 'Q1', 'S1', 20),
(65, 90, 'Q2', 'S4', 10),
(66, 90, 'Q3', 'S3', 9),
(67, 90, 'Q4', 'S1', 16);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `tag`
--

INSERT INTO `tag` (`id`, `tag_name`) VALUES
(12, 'IT 日本語'),
(13, 'ソフトウエア'),
(14, 'ハードウエア'),
(15, '英語'),
(16, 'プロジェクト管理'),
(17, '地球');

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
  `last_login_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `first_password`, `password`, `name`, `sex`, `email`, `birthday`, `address`, `phone`, `bank_account`, `first_secret_question`, `first_secret_answer`, `secret_question`, `secret_answer`, `role`, `status`, `last_login_ip`, `fail_login_count`, `last_login_time`) VALUES
(18, 'thayminh', '87683ef352817ffc68a1bcddf45f4e18930a617f', '87683ef352817ffc68a1bcddf45f4e18930a617f', 'Minh Tran', 0, 'tminhhp@gmail.com', '13-10-2001', '45 Trần Đại Nghĩa', '0987654321', '711A12334534', 'nick gg', 'hnimnart', 'nick gg', 'hnimnart', 2, 1, '127.0.0.1', 1, 0),
(19, 'minhtq', '976450ee760f6dace9a481bdf6406728019ce7ca', '976450ee760f6dace9a481bdf6406728019ce7ca', 'Trần Quang Minh', 0, 'tminh_1234@yahoo.com', '17-6-2001', '42 Lê Thanh Nghị', '0987654321', '711A12334534', 'nick gg', 'hnimnart', 'nick gg', 'hnimnart', 1, 1, '127.0.0.1', 4, 0),
(22, 'thayminh2', '11ed51e5fbac4770f5db86f67db563c4273cb747', '11ed51e5fbac4770f5db86f67db563c4273cb747', 'Giao Su Minh', 0, 'tminh.hp@hotmail.com', '17-9-2006', '69 Trần Đại Nghĩa', '0987654321', '711A12334534', 'nick gg', 'c978ce09940e3c299827392480c655cd596b32ee', 'nick gg', 'c978ce09940e3c299827392480c655cd596b32ee', 2, 1, '127.0.0.1', 0, 0),
(23, 'thayminh3', '8d3ae284cd69f79feb06b861297b3db631f60e92', '8d3ae284cd69f79feb06b861297b3db631f60e92', 'Tiến Sĩ Minh', 0, 'minhtq@rikkei.com', '15-7-2002', '45 Trần Đại Nghĩa', '0987654321', '711A12334534', 'nick gg', 'c978ce09940e3c299827392480c655cd596b32ee', 'nick gg', 'c978ce09940e3c299827392480c655cd596b32ee', 2, 1, '127.0.0.1', 0, 0),
(24, 'giapnh', '4b85e82bcd41747e0a2ee60b8f7ef26fc6bb2ce7', '4b85e82bcd41747e0a2ee60b8f7ef26fc6bb2ce7', 'Nguyễn Hữu Giáp', 0, '', '2-2-2011', '151 Nguyễn Đức Cảnh', '01664643234', '711A948594898', '', '', '', '', 1, 3, NULL, 3, 1397928434),
(25, 'thaygiap', 'ff118180fdde6ac7127376143342336f59e81091', 'ff118180fdde6ac7127376143342336f59e81091', 'Nguyễn Hữu Giáp', 0, 'hgbk.it@gmail.com', '16-3-1996', '151 Nguyễn Đức Cảnh', '01664643234', '711A948594898', '1', '0937afa17f4dc08f3c0e5dc908158370ce64df86', '1', '0937afa17f4dc08f3c0e5dc908158370ce64df86', 2, 1, '127.0.0.1', 0, 1397928787);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
