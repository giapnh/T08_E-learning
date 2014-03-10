-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 10, 2014 at 01:42 PM
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `create_admin`, `status`, `fail_login`, `log_time`) VALUES
(1, 'superadmin', 'acf7d0837ef13c218906f4875462d734', 1, 1, 0, '2014-03-04 07:36:58'),
(3, 'admin2', 'asdfasdf', 1, 1, 0, '2014-03-09 17:35:28'),
(4, 'admin3', 'admin3', 1, 1, 0, '2014-03-09 18:16:37');

-- --------------------------------------------------------

--
-- Table structure for table `admin_ip`
--

CREATE TABLE IF NOT EXISTS `admin_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `admin_ip`
--

INSERT INTO `admin_ip` (`id`, `admin_id`, `ip`) VALUES
(2, 1, '127.0.0.1'),
(3, 2, '127.0.0.1'),
(6, 1, '192.168.1.2'),
(7, 3, '192.168.1.10'),
(8, 3, '192.168.1.11'),
(9, 4, '127.0.0.1'),
(10, 4, '127.0.0.2'),
(11, 1, '1.1.1.1');

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
  `status` int(11) NOT NULL COMMENT '1: Active, 0:Lock',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `learn`
--

INSERT INTO `learn` (`id`, `student_id`, `lesson_id`, `register_time`, `status`) VALUES
(1, 7, 1, '2014-03-07 06:51:14', 1),
(2, 7, 1, '2014-03-07 06:51:33', 1),
(3, 8, 1, '2014-03-07 06:51:43', 1),
(4, 16, 1, '2014-03-08 18:22:21', 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39 ;

--
-- Dumping data for table `lesson`
--

INSERT INTO `lesson` (`id`, `teacher_id`, `title`, `description`, `view`, `like`, `create_time`, `status`) VALUES
(1, 9, 'IT日本語', '英: Information technology、IT（あいてぃー））は、情報処理特にコンピュータなどの基礎あるいは応用技術の総称。通信 (communication) を含める場合はICTと言う。\n米国のITAAの定義では「コンピュータをベースとした情報システム、特にアプリケーションソフトウェアやコンピュータのハードウェアなどの研究、デザイン、開発、インプリメンテーション、サポート (Technical support) あるいはマネジメント」である[1]。ITは電子的なコンピュータやコンピュータソフトウェアを使用して、情報に対するセキュリティ、変換、保管、処理、転送、入出力、検索などを取り扱う。', 2, 0, '2014-03-10 04:36:04', 1),
(2, 9, 'C プログラミング言語', '第1回「プログラミング言語Cについて知ろう」では、Cの成り立ちから特徴、その用途までを説明しました。なぜ、Cを学ぶことが重要なのかが理解できたと思います。', 0, 0, '2014-03-06 09:42:24', 1),
(3, 9, '英語', 'えいごであそぼ. こどもたちに英語に親しんで興味を持ってもらうことを目的とした番組です。 番組キャラクターたちと一緒に絵本の世界を楽しみながら、こどもたちが「英語を話してみたい」と思えるような構成を目指しています。', 0, 0, '2014-03-06 09:43:06', 1),
(4, 9, '数学', '出典を追加して記事の信頼性向上にご協力ください。（2012年1月）. 本記事では数学（すうがく、ギリシア語: μαθηματικά, ラテン語: mathematica, 英語: mathematics）について解説する。 目次. 1 概要; 2 歴史; 3 研究; 4 分野; 5 数学に関する賞; 6 脚注; 7 参考 ...', 0, 0, '2014-03-06 09:43:28', 0),
(5, 11, '数学', '出典を追加して記事の信頼性向上にご協力ください。（2012年1月）. 本記事では数学（すうがく、ギリシア語: μαθηματικά, ラテン語: mathematica, 英語: mathematics）について解説する。 目次. 1 概要; 2 歴史; 3 研究; 4 分野; 5 数学に関する賞; 6 脚注; 7 参考 ...', 0, 0, '2014-03-07 01:04:23', 0),
(6, 11, '数学', '出典を追加して記事の信頼性向上にご協力ください。（2012年1月）. 本記事では数学（すうがく、ギリシア語: μαθηματικά, ラテン語: mathematica, 英語: mathematics）について解説する。 目次. 1 概要; 2 歴史; 3 研究; 4 分野; 5 数学に関する賞; 6 脚注; 7 参考 ...', 0, 0, '2014-03-07 01:04:26', 0),
(7, 11, '数学', '出典を追加して記事の信頼性向上にご協力ください。（2012年1月）. 本記事では数学（すうがく、ギリシア語: μαθηματικά, ラテン語: mathematica, 英語: mathematics）について解説する。 目次. 1 概要; 2 歴史; 3 研究; 4 分野; 5 数学に関する賞; 6 脚注; 7 参考 ...', 0, 0, '2014-03-07 01:04:29', 0),
(8, 12, '数学', '出典を追加して記事の信頼性向上にご協力ください。（2012年1月）. 本記事では数学（すうがく、ギリシア語: μαθηματικά, ラテン語: mathematica, 英語: mathematics）について解説する。 目次. 1 概要; 2 歴史; 3 研究; 4 分野; 5 数学に関する賞; 6 脚注; 7 参考 ...', 0, 0, '2014-03-07 01:04:35', 0),
(31, 15, 'asdf', 'asdf', 0, 0, '2014-03-10 08:12:59', 1),
(32, 15, 'Test title', 'これは簡単な授業です', 0, 0, '2014-03-10 08:29:19', 1),
(33, 15, 'Test title', 'あべせ', 0, 0, '2014-03-10 08:30:49', 1),
(34, 15, 'Test save Question', 'asdf', 0, 0, '2014-03-10 13:32:48', 1),
(35, 15, 'Test title', 'asdf', 0, 0, '2014-03-10 13:34:41', 1),
(36, 15, 'Test title', 'asdf', 0, 0, '2014-03-10 13:35:27', 1),
(37, 15, 'Test title', 'asdf', 0, 0, '2014-03-10 13:35:53', 1),
(38, 15, 'Test title', 'asdf', 0, 0, '2014-03-10 13:36:40', 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45 ;

--
-- Dumping data for table `lesson_file`
--

INSERT INTO `lesson_file` (`id`, `lesson_id`, `filename`, `description`, `title`, `subtitle`, `location`) VALUES
(22, 26, '02W-02-E-Learningシステムテスト用TSVファイルサンプル.tsv', '', 'This is test title', 'This is test subtitle', 'files\\26\\13943692700.html'),
(23, 27, 'img_file.png', '', '', '', 'files\\27\\13943693190.png'),
(24, 27, '02W-02-E-Learningシステムテスト用TSVファイルサンプル.tsv', '', 'This is test title', 'This is test subtitle', 'files\\27\\13943693191.html'),
(25, 27, 'Trà My Idol,Hoàng Rapper – Cần Lắm.mp3', '', '', '', 'files\\27\\13943693192.mp3'),
(26, 28, '02W-02-E-Learningシステムテスト用TSVファイルサンプル.tsv', 'asdf', 'This is test title', 'This is test subtitle', 'files\\28\\13943734120.html'),
(27, 29, '02W-02-E-Learningシステムテスト用TSVファイルサンプル.tsv', 'asdf', 'This is test title', 'This is test subtitle', 'files\\29\\13943884490.html'),
(28, 30, 'testLesson.tsv', 'ファイル１', 'This is test title', 'This is test subtitle', 'files\\30\\13944375440.html'),
(29, 30, 'img_file.png', 'ファイル２', '', '', 'files\\30\\13944375441.png'),
(30, 30, 'Blue –  U Make Me Wanna (Radio Edit).mp3', 'ファイル３', '', '', 'files\\30\\13944375442.mp3'),
(31, 30, '10W-03-要件定義書について.pdf', 'ファイル４', '', '', 'files\\30\\13944375443.pdf'),
(32, 30, '10W-01-開発計画書についての所感K54.pdf', 'ファイル５', '', '', 'files\\30\\13944375444.pdf'),
(33, 31, 'Blue –  All Rise.mp3', '', '', '', 'files\\31\\13944391790.mp3'),
(34, 32, 'Adele - Rolling in the Deep (Piano_Cello Cover) - ThePianoGuys.mp4', 'ファイル１', '', '', 'files\\32\\13944401590.mp4'),
(35, 32, 'Blue –  All Rise.mp3', 'ファイル２', '', '', 'files\\32\\13944401591.mp3'),
(36, 32, 'testLesson.tsv', 'ファイル３', 'This is test title', 'This is test subtitle', 'files\\32\\13944401592.html'),
(37, 32, '10W-03-要件定義書について.pdf', 'ファイル４', '', '', 'files\\32\\13944401593.pdf'),
(38, 33, '10W-02-議事録、報告書について.pdf', '', '', '', 'files\\33\\13944402490.pdf'),
(39, 33, '10W-03-要件定義書について.pdf', '', '', '', 'files\\33\\13944402491.pdf'),
(40, 34, 'testLesson.tsv', '', 'This is test title', 'This is test subtitle', 'files\\34\\13944583680.html'),
(41, 35, 'testLesson.tsv', '', 'This is test title', 'This is test subtitle', 'files\\35\\13944584810.html'),
(42, 36, 'testLesson.tsv', '', 'This is test title', 'This is test subtitle', 'files\\36\\13944585270.html'),
(43, 37, 'testLesson.tsv', '', 'This is test title', 'This is test subtitle', 'files\\37\\13944585530.html'),
(44, 38, 'testLesson.tsv', '', 'This is test title', 'This is test subtitle', 'files\\38\\13944586000.html');

-- --------------------------------------------------------

--
-- Table structure for table `lesson_tag`
--

CREATE TABLE IF NOT EXISTS `lesson_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `lesson_tag`
--

INSERT INTO `lesson_tag` (`id`, `lesson_id`, `tag_id`) VALUES
(1, 1, 4),
(2, 1, 1),
(3, 1, 2),
(4, 2, 1),
(5, 2, 2),
(6, 32, 1),
(7, 32, 2),
(8, 33, 6),
(9, 33, 2),
(10, 34, 7),
(11, 35, 8),
(12, 36, 6),
(13, 37, 6),
(14, 38, 6);

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
('FILE_LOCATION', 'E:Documents'),
('LESSON_DEADLINE', '7'),
('LOCK_COUNT', '5'),
('LOGIN_FAIL_LOCK_TIME', '3600'),
('PASSWORD_CONST', '100'),
('SESSION_TIME', '86400'),
('TEACHER_FEE_RATE', '60'),
('VIOLATION_TIME', '10');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`id`, `file_id`, `title`, `answer`, `point`) VALUES
(1, 43, '0', '0', 10),
(2, 43, '0', '0', 5),
(3, 43, '0', '0', 5),
(4, 43, '0', '0', 20),
(5, 44, 'Q1', 'S1', 10),
(6, 44, 'Q2', 'S4', 5),
(7, 44, 'Q3', 'S3', 5),
(8, 44, 'Q4', 'S1', 20);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `tag`
--

INSERT INTO `tag` (`id`, `tag_name`) VALUES
(1, 'C　言語'),
(2, '英語'),
(3, 'PHP'),
(4, 'IT　日本語'),
(5, '数学'),
(6, 'あ'),
(7, 'asdf'),
(8, 'adsf');

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
  `last_login_time` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `first_password`, `password`, `name`, `sex`, `email`, `birthday`, `address`, `phone`, `bank_account`, `first_secret_question`, `first_secret_answer`, `secret_question`, `secret_answer`, `role`, `status`, `last_login_ip`, `fail_login_count`, `last_login_time`) VALUES
(7, 'giap_huu', '58aee8e76dfdf89c2d5c41de957767e8', '58aee8e76dfdf89c2d5c41de957767e8', 'いちのせ', 0, 'hgbk.it@gmail.com', '17-4-1998', 'Hoang Mai', '01664643234', '711A948594898', '1+1=', '2', '1+1=', '2', 1, 1, NULL, 0, NULL),
(8, 'giapnh', '521fa11ddef0190b157ce6f5aa602659', 'c4ec9911d93f93e36dcb77bd18c3e3d7', 'Nguyễn Hữu Giáp', 0, 'hgbk.it@gmail.com', '29-2-1995', 'Hoang Mai', '01664643234', '711A948594898', '1+1=', '2', '1+1=', '23', 1, 1, NULL, 0, NULL),
(9, 'giap2000', 'd12f981e91d84b699f4d15b9de6bb3f3', 'd12f981e91d84b699f4d15b9de6bb3f3', 'ごんだい', 0, 'hgbk.it@gmail.com', '19-2-1996', 'Quận Hoàng Mai, Hà Nội', '01664643234', '711A948594898', '1+1=', '2', '1+1=', '2', 2, 1, NULL, 0, NULL),
(11, 'giap2001', 'd12f981e91d84b699f4d15b9de6bb3f3', 'd12f981e91d84b699f4d15b9de6bb3f3', '作間', 0, 'hgbk.it@gmail.com', '19-2-1996', 'Quận Hoàng Mai, Hà Nội', '01664643234', '711A948594898', '1+1=', '2', '1+1=', '2', 2, 1, NULL, 0, NULL),
(12, 'giap2002', 'd12f981e91d84b699f4d15b9de6bb3f3', 'd12f981e91d84b699f4d15b9de6bb3f3', '竹本', 0, 'hgbk.it@gmail.com', '19-2-1996', 'Quận Hoàng Mai, Hà Nội', '01664643234', '711A948594898', '1+1=', '2', '1+1=', '2', 2, 1, NULL, 0, NULL),
(14, 'giapnh2', '521fa11ddef0190b157ce6f5aa602659', 'c4ec9911d93f93e36dcb77bd18c3e3d7', 'Nguyễn Hữu Giáp', 0, 'hgbk.it@gmail.com', '29-2-1995', 'Hoang Mai', '01664643234', '711A948594898', '1+1=', '2', '1+1=', '23', 1, 1, NULL, 0, NULL),
(15, 'thayminh', '8a24e37323013b964ea7191be423fc19', '8a24e37323013b964ea7191be423fc19', 'Tran Quang Minh', 0, 'tminh_1234@yahoo.com', '16-8-2000', '45 Trần Đại Nghĩa', '09887654321', '711A12334534', 'abc', 'xyz', 'abc', 'xyz', 2, 1, NULL, 2, NULL),
(16, 'minhtq', '071998693c1568527a785191acb1491c', '071998693c1568527a785191acb1491c', 'Tran Quang Minh', 0, 'tminh_1234@yahoo.com', '27-8-1998', '45 Trần Đại Nghĩa', '09887654321', '711A12334534', 'abc', 'xyz', 'abc', 'xyz', 1, 1, NULL, 1, NULL),
(17, 'superadmin', 'acf7d0837ef13c218906f4875462d734', 'acf7d0837ef13c218906f4875462d734', 'Tran Quang Minh', 0, 'tminh_1234@yahoo.com', '30-9-1997', '45 Trần Đại Nghĩa', '09887654321', '711A12334534', 'abc', 'xyz', 'abc', 'xyz', 1, 2, NULL, 0, NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
