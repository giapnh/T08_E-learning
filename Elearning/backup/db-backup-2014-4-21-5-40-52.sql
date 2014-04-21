DROP TABLE admin;

CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `create_admin` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `fail_login` int(11) NOT NULL,
  `log_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO admin VALUES("2","A00","9febefbc569f0a9334811ffcca6f3146368d1fae","2","1","0","2014-04-21 10:16:56");
INSERT INTO admin VALUES("3","A01","a7969a688d33ae4e42cdc00cf796553cc9bb1ab6","2","1","0","2014-04-21 10:27:30");



DROP TABLE admin_ip;

CREATE TABLE `admin_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

INSERT INTO admin_ip VALUES("2","1","127.0.0.1");
INSERT INTO admin_ip VALUES("3","2","127.0.0.1");
INSERT INTO admin_ip VALUES("4","3","127.0.0.2");
INSERT INTO admin_ip VALUES("5","3","127.0.0.1");
INSERT INTO admin_ip VALUES("6","3","127.0.0.3");
INSERT INTO admin_ip VALUES("7","4","127.0.0.1");
INSERT INTO admin_ip VALUES("8","4","126.0.0.1");



DROP TABLE comment;

CREATE TABLE `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `comment` varchar(512) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;




DROP TABLE copyright_report;

CREATE TABLE `copyright_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;




DROP TABLE file_comment;

CREATE TABLE `file_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` varchar(512) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;




DROP TABLE learn;

CREATE TABLE `learn` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `register_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(11) NOT NULL COMMENT '1: Active, 0:Lock',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;




DROP TABLE lesson;

CREATE TABLE `lesson` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `teacher_id` int(11) NOT NULL,
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `view` int(11) NOT NULL DEFAULT '0',
  `num_like` int(11) DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '1: Active; 0: inactive',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;




DROP TABLE lesson_file;

CREATE TABLE `lesson_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `description` varchar(512) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `subtitle` varchar(200) DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '1: available; 2: locked; 3: deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8;




DROP TABLE lesson_like;

CREATE TABLE `lesson_like` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




DROP TABLE lesson_tag;

CREATE TABLE `lesson_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;




DROP TABLE master;

CREATE TABLE `master` (
  `master_key` varchar(100) CHARACTER SET latin1 NOT NULL,
  `master_value` varchar(100) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`master_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO master VALUES("BACKUP_TIME","3630");
INSERT INTO master VALUES("COMA_PRICE","20000");
INSERT INTO master VALUES("FILE_LOCATION","files");
INSERT INTO master VALUES("LESSON_DEADLINE","7");
INSERT INTO master VALUES("LOCK_COUNT","3");
INSERT INTO master VALUES("LOGIN_FAIL_LOCK_TIME","10");
INSERT INTO master VALUES("PASSWORD_CONST","100");
INSERT INTO master VALUES("SESSION_TIME","86400");
INSERT INTO master VALUES("TEACHER_FEE_RATE","60");
INSERT INTO master VALUES("VIOLATION_TIME","20");



DROP TABLE question;

CREATE TABLE `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) NOT NULL,
  `title` varchar(11) NOT NULL,
  `answer` varchar(11) NOT NULL,
  `point` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;




DROP TABLE result;

CREATE TABLE `result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `learn_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




DROP TABLE tag;

CREATE TABLE `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;




DROP TABLE test_result;

CREATE TABLE `test_result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `learn_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `result` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




DROP TABLE user;

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `first_password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `sex` int(1) NOT NULL DEFAULT '0' COMMENT '0: boy, 1:girl',
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `birthday` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;




