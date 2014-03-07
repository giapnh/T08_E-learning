-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 07, 2014 at 08:14 AM
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
CREATE DATABASE IF NOT EXISTS `elearning` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
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
(1, 'superadmin', 'acf7d0837ef13c218906f4875462d734', 1, 1, 0, '2014-03-04 07:36:58');

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
  `status` int(11) NOT NULL COMMENT '1: Active, 0:Lock',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `learn`
--

INSERT INTO `learn` (`id`, `student_id`, `lesson_id`, `register_time`, `status`) VALUES
(1, 7, 1, '2014-03-07 06:51:14', 1),
(2, 7, 1, '2014-03-07 06:51:33', 1),
(3, 8, 1, '2014-03-07 06:51:43', 1);

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
(1, 9, 'IT日本語', '英: Information technology、IT（あいてぃー））は、情報処理特にコンピュータなどの基礎あるいは応用技術の総称。通信 (communication) を含める場合はICTと言う。\n米国のITAAの定義では「コンピュータをベースとした情報システム、特にアプリケーションソフトウェアやコンピュータのハードウェアなどの研究、デザイン、開発、インプリメンテーション、サポート (Technical support) あるいはマネジメント」である[1]。ITは電子的なコンピュータやコンピュータソフトウェアを使用して、情報に対するセキュリティ、変換、保管、処理、転送、入出力、検索などを取り扱う。', 0, 0, '2014-03-06 09:07:03', 1),
(2, 9, 'C プログラミング言語', '第1回「プログラミング言語Cについて知ろう」では、Cの成り立ちから特徴、その用途までを説明しました。なぜ、Cを学ぶことが重要なのかが理解できたと思います。', 0, 0, '2014-03-06 09:42:24', 1),
(3, 9, '英語', 'えいごであそぼ. こどもたちに英語に親しんで興味を持ってもらうことを目的とした番組です。 番組キャラクターたちと一緒に絵本の世界を楽しみながら、こどもたちが「英語を話してみたい」と思えるような構成を目指しています。', 0, 0, '2014-03-06 09:43:06', 1),
(4, 9, '数学', '出典を追加して記事の信頼性向上にご協力ください。（2012年1月）. 本記事では数学（すうがく、ギリシア語: μαθηματικά, ラテン語: mathematica, 英語: mathematics）について解説する。 目次. 1 概要; 2 歴史; 3 研究; 4 分野; 5 数学に関する賞; 6 脚注; 7 参考 ...', 0, 0, '2014-03-06 09:43:28', 0),
(5, 11, '数学', '出典を追加して記事の信頼性向上にご協力ください。（2012年1月）. 本記事では数学（すうがく、ギリシア語: μαθηματικά, ラテン語: mathematica, 英語: mathematics）について解説する。 目次. 1 概要; 2 歴史; 3 研究; 4 分野; 5 数学に関する賞; 6 脚注; 7 参考 ...', 0, 0, '2014-03-07 01:04:23', 0),
(6, 11, '数学', '出典を追加して記事の信頼性向上にご協力ください。（2012年1月）. 本記事では数学（すうがく、ギリシア語: μαθηματικά, ラテン語: mathematica, 英語: mathematics）について解説する。 目次. 1 概要; 2 歴史; 3 研究; 4 分野; 5 数学に関する賞; 6 脚注; 7 参考 ...', 0, 0, '2014-03-07 01:04:26', 0),
(7, 11, '数学', '出典を追加して記事の信頼性向上にご協力ください。（2012年1月）. 本記事では数学（すうがく、ギリシア語: μαθηματικά, ラテン語: mathematica, 英語: mathematics）について解説する。 目次. 1 概要; 2 歴史; 3 研究; 4 分野; 5 数学に関する賞; 6 脚注; 7 参考 ...', 0, 0, '2014-03-07 01:04:29', 0),
(8, 12, '数学', '出典を追加して記事の信頼性向上にご協力ください。（2012年1月）. 本記事では数学（すうがく、ギリシア語: μαθηματικά, ラテン語: mathematica, 英語: mathematics）について解説する。 目次. 1 概要; 2 歴史; 3 研究; 4 分野; 5 数学に関する賞; 6 脚注; 7 参考 ...', 0, 0, '2014-03-07 01:04:35', 0);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `lesson_file`
--

INSERT INTO `lesson_file` (`id`, `lesson_id`, `filename`, `description`, `location`) VALUES
(1, 1, 'File PDF', 'Bai hoc chuong 1', '/public/file/lesson/1/'),
(2, 1, 'File MP3', 'Bai nghe chuong 1', '/public/file/lesson/1/'),
(3, 1, 'File TSV', 'Bai Test chuong 1', '/public/file/lesson/1/');

-- --------------------------------------------------------

--
-- Table structure for table `lesson_tag`
--

CREATE TABLE IF NOT EXISTS `lesson_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `lesson_tag`
--

INSERT INTO `lesson_tag` (`id`, `lesson_id`, `tag_id`) VALUES
(1, 1, 4),
(2, 1, 1),
(3, 1, 2),
(4, 2, 1),
(5, 2, 2);

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
  `master_key` varchar(100) NOT NULL,
  `master_value` varchar(100) NOT NULL,
  PRIMARY KEY (`master_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `first_password`, `password`, `name`, `sex`, `email`, `birthday`, `address`, `phone`, `bank_account`, `first_secret_question`, `first_secret_answer`, `secret_question`, `secret_answer`, `role`, `status`, `last_login_ip`, `fail_login_count`, `last_login_time`) VALUES
(7, 'giap_huu', '58aee8e76dfdf89c2d5c41de957767e8', '58aee8e76dfdf89c2d5c41de957767e8', 'いちのせ', 0, 'hgbk.it@gmail.com', '17-4-1998', 'Hoang Mai', '01664643234', '711A948594898', '1+1=', '2', '1+1=', '2', 1, 1, NULL, 0, NULL),
(8, 'giapnh', '521fa11ddef0190b157ce6f5aa602659', 'c4ec9911d93f93e36dcb77bd18c3e3d7', 'Nguyễn Hữu Giáp', 0, 'hgbk.it@gmail.com', '29-2-1995', 'Hoang Mai', '01664643234', '711A948594898', '1+1=', '2', '1+1=', '23', 1, 1, NULL, 0, NULL),
(9, 'giap2000', 'd12f981e91d84b699f4d15b9de6bb3f3', 'd12f981e91d84b699f4d15b9de6bb3f3', 'ごんだい', 0, 'hgbk.it@gmail.com', '19-2-1996', 'Quận Hoàng Mai, Hà Nội', '01664643234', '711A948594898', '1+1=', '2', '1+1=', '2', 2, 1, NULL, 0, NULL),
(11, 'giap2001', 'd12f981e91d84b699f4d15b9de6bb3f3', 'd12f981e91d84b699f4d15b9de6bb3f3', '作間', 0, 'hgbk.it@gmail.com', '19-2-1996', 'Quận Hoàng Mai, Hà Nội', '01664643234', '711A948594898', '1+1=', '2', '1+1=', '2', 2, 1, NULL, 0, NULL),
(12, 'giap2002', 'd12f981e91d84b699f4d15b9de6bb3f3', 'd12f981e91d84b699f4d15b9de6bb3f3', '竹本', 0, 'hgbk.it@gmail.com', '19-2-1996', 'Quận Hoàng Mai, Hà Nội', '01664643234', '711A948594898', '1+1=', '2', '1+1=', '2', 2, 1, NULL, 0, NULL),
(13, 'giapnh1', '521fa11ddef0190b157ce6f5aa602659', 'c4ec9911d93f93e36dcb77bd18c3e3d7', '山本', 0, 'hgbk.it@gmail.com', '29-2-1995', 'Hoang Mai', '01664643234', '711A948594898', '1+1=', '2', '1+1=', '23', 1, 1, NULL, 0, NULL),
(14, 'giapnh2', '521fa11ddef0190b157ce6f5aa602659', 'c4ec9911d93f93e36dcb77bd18c3e3d7', 'Nguyễn Hữu Giáp', 0, 'hgbk.it@gmail.com', '29-2-1995', 'Hoang Mai', '01664643234', '711A948594898', '1+1=', '2', '1+1=', '23', 1, 1, NULL, 0, NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
