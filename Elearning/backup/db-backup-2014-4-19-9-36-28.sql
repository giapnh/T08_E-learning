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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO admin VALUES("1","superadmin","732a25fa821c3228e4e58274a5a5703d7c27cdc3","1","1","0","2014-04-16 20:37:00");



DROP TABLE admin_ip;

CREATE TABLE `admin_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO admin_ip VALUES("2","1","127.0.0.1");



DROP TABLE comment;

CREATE TABLE `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `comment` varchar(512) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

INSERT INTO comment VALUES("1","23","49","楽しみにしてください","2014-04-14 14:48:28");
INSERT INTO comment VALUES("2","1","43","OK, thanks :D","2014-04-16 08:49:49");
INSERT INTO comment VALUES("3","1","43","OK, thanks :D","2014-04-16 08:49:51");
INSERT INTO comment VALUES("4","1","43","OK, thanks :D","2014-04-16 08:49:59");
INSERT INTO comment VALUES("5","1","43","OK, thanks","2014-04-16 08:51:49");
INSERT INTO comment VALUES("6","1","43","OK, thanks","2014-04-16 08:53:47");



DROP TABLE copyright_report;

CREATE TABLE `copyright_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO copyright_report VALUES("2","19","65","deo biet","0");
INSERT INTO copyright_report VALUES("3","19","65","abc","1");
INSERT INTO copyright_report VALUES("4","19","65","hahaha","0");



DROP TABLE file_comment;

CREATE TABLE `file_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` varchar(512) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

INSERT INTO file_comment VALUES("1","65","19","Hi all","2014-04-16 08:26:57");
INSERT INTO file_comment VALUES("2","65","19","Are you ok?","2014-04-16 08:33:02");
INSERT INTO file_comment VALUES("3","65","1","asdf","2014-04-16 09:00:45");
INSERT INTO file_comment VALUES("4","65","1","asdf","2014-04-16 09:01:46");
INSERT INTO file_comment VALUES("5","65","1","asdf","2014-04-16 09:02:26");
INSERT INTO file_comment VALUES("6","65","1","asdf","2014-04-16 09:03:42");
INSERT INTO file_comment VALUES("7","65","1","asdf","2014-04-16 09:03:51");
INSERT INTO file_comment VALUES("8","65","1","asdf","2014-04-16 09:03:53");
INSERT INTO file_comment VALUES("9","65","1","asdf","2014-04-16 09:04:02");



DROP TABLE learn;

CREATE TABLE `learn` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `register_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(11) NOT NULL COMMENT '1: Active, 0:Lock',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

INSERT INTO learn VALUES("11","19","43","2014-04-12 14:46:13","1");
INSERT INTO learn VALUES("12","19","50","2014-04-18 17:58:19","1");



DROP TABLE lesson;

CREATE TABLE `lesson` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `teacher_id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` varchar(512) NOT NULL,
  `view` int(11) NOT NULL DEFAULT '0',
  `like` int(11) DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '1: Active; 0: inactive',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;

INSERT INTO lesson VALUES("43","18","IT 日本語第5週","IT 日本語","2","0","2014-04-16 14:29:20","0");
INSERT INTO lesson VALUES("44","18","IT 日本語第6週","IT 日本語","0","0","2014-04-11 14:20:48","1");
INSERT INTO lesson VALUES("45","18","IT 日本語第7週","IT 日本語","0","0","2014-04-11 14:21:13","1");
INSERT INTO lesson VALUES("46","18","IT 日本語第8週","IT 日本語","0","0","2014-04-11 14:22:14","1");
INSERT INTO lesson VALUES("47","18","クラウド化でシステム運用担当者は業務量が減る","前回までは、システム運用における「5つの間違い」について述べてきた。それでは、このような間違いに陥らないためにはどのようにしたらよいだろうか。そのためには、しっかりとしたシステムの運用設計が必要である。それらは体制や仕組みなど、トータルの設計だ","0","0","2014-04-14 14:16:23","1");
INSERT INTO lesson VALUES("48","22","世界各地から開発者が集結！日本初のDrupal Camp、京都で開催","4月12日、春の京都は祇園、しかも神社という絶好のロケーションで、日本で初めてのDrupal Camp、「Drupal Camp Japan Kyoto 2014」が開催された。Drupalは世界で最も普及しているオープンソースのCMSであるが、これまで日本での知名度は低いままだった。しかし、昨年来から採用事例も大幅に増え、コミュニティの活動も活発になってきている。","0","0","2014-04-14 14:43:23","1");
INSERT INTO lesson VALUES("49","23","キャラクターが声で天気予報を教えてくれるアプリを作る","今回は、指定した地域の天気予報を、キャラクターが音声で案内してくれるアプリを作ります。最近は萌えキャラやゆるキャラなど、さまざまなキャラクターを目にする機会が多いので、独自のキャラクターを使ってアプリを作りたい方に向いているのではないでしょうか。","0","0","2014-04-14 14:48:01","1");
INSERT INTO lesson VALUES("50","18","地球によく似た惑星 ＮＡＳＡが発見","アメリカのＮＡＳＡ＝航空宇宙局は、地球とほぼ同じ大きさで、水が液体の状態で存在する可能性がある、地球によく似た惑星を発見したと発表し、生命が存在しうる惑星の探査につながる成果として注目されています","1","0","2014-04-18 17:58:12","1");



DROP TABLE lesson_file;

CREATE TABLE `lesson_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `description` varchar(512) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `subtitle` varchar(200) DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8;

INSERT INTO lesson_file VALUES("65","43","05W-01-内部設計についての所感.pdf","","","","\\43\\13972007920.pdf");
INSERT INTO lesson_file VALUES("66","43","05W-02-プログラミングについて.pdf","","","","\\43\\13972007921.pdf");
INSERT INTO lesson_file VALUES("67","43","05W-03-第２回プロジェクト報告会について.pdf","","","","\\43\\13972007922.pdf");
INSERT INTO lesson_file VALUES("68","44","06W-01-反省について.pdf","","","","\\44\\13972008480.pdf");
INSERT INTO lesson_file VALUES("69","45","07W-01-第２回プロジェクト報告会についての所感.pdf","","","","\\45\\13972008730.pdf");
INSERT INTO lesson_file VALUES("70","45","07W-02-今後のスケジュールについて.pdf","","","","\\45\\13972008820.pdf");
INSERT INTO lesson_file VALUES("71","45","07W-04-結合試験仕様書について.pdf","","","","\\45\\13972008890.pdf");
INSERT INTO lesson_file VALUES("72","46","08W-01-試験工程について.pdf","","","","\\46\\13972009340.pdf");
INSERT INTO lesson_file VALUES("73","46","08W-02-第３回プロジェクト報告会について.pdf","","","","\\46\\13972009341.pdf");
INSERT INTO lesson_file VALUES("74","47","database.png","イメージファイル","","","\\47\\13974597830.png");
INSERT INTO lesson_file VALUES("75","47","Andrea Bocelli & Hayley Westenra -- Vivo Per Lei.mp4","ビデオファイル","","","\\47\\13974597831.mp4");
INSERT INTO lesson_file VALUES("76","47","test3.tsv","テスト","2006年度2級文字・語彙","次の56から60の言葉の使い方として最も適当なものを、1、2、3、4から一つ選びなさい。","\\47\\13974597832.html");
INSERT INTO lesson_file VALUES("77","48","test2.tsv","テストファイル","2006年度2級","文字・語彙","\\48\\13974614030.html");
INSERT INTO lesson_file VALUES("78","49","Avril Lavigne - What The Hell.mp4","ビデオファイル１","","","\\49\\13974616800.mp4");
INSERT INTO lesson_file VALUES("79","49","Eminem - Love The Way You Lie ft. Rihanna.mp4","Rihana","","","\\49\\13974616811.mp4");
INSERT INTO lesson_file VALUES("80","50","Tricoloring.png","","","","\\50\\13978066710.png");
INSERT INTO lesson_file VALUES("81","50","1280px-Vector_Field.gif","","","","\\50\\13978066711.gif");
INSERT INTO lesson_file VALUES("82","50","Christmas-Gifs_02.gif","","","","\\50\\13978066712.gif");
INSERT INTO lesson_file VALUES("83","50","Enternal Flame - Atomic Kitten.mp3","","","","\\50\\13978066713.mp3");
INSERT INTO lesson_file VALUES("84","50","guitar-fire.jpg","","","","\\50\\13978066714.jpg");
INSERT INTO lesson_file VALUES("85","50","Avril Lavigne - What The Hell.mp4","","","","\\50\\13978066715.mp4");
INSERT INTO lesson_file VALUES("86","50","M2M_2004_Many-to-Many Communication_A New Approach for Collaboration in MANET.pdf","","","","\\50\\13978066716.pdf");
INSERT INTO lesson_file VALUES("87","50","Tricoloring.png","","","","\\50\\13978066717.png");
INSERT INTO lesson_file VALUES("88","50","test3.tsv","","2006年度2級文字・語彙","次の56から60の言葉の使い方として最も適当なものを、1、2、3、4から一つ選びなさい。","\\50\\13978066718.html");
INSERT INTO lesson_file VALUES("89","50","Justin Bieber - -As Long As You Love Me ft. Big Sean- (Cover by Tiffany Alvord).wav","","","","\\50\\13978066719.wav");
INSERT INTO lesson_file VALUES("91","43","mario.png","asdf","","","\\43\\13978776580.png");
INSERT INTO lesson_file VALUES("92","43","Andrea Bocelli & Hayley Westenra -- Vivo Per Lei.mp4","","","","\\43\\13978788710.mp4");
INSERT INTO lesson_file VALUES("93","43","Avril Lavigne - Wish You Were Here.mp4","asf","","","\\43\\13978815440.mp4");
INSERT INTO lesson_file VALUES("94","43","Avril Lavigne - What The Hell.mp4","asdf","","","\\43\\13978816000.mp4");



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
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

INSERT INTO lesson_tag VALUES("26","43","12");
INSERT INTO lesson_tag VALUES("27","44","12");
INSERT INTO lesson_tag VALUES("28","45","12");
INSERT INTO lesson_tag VALUES("29","46","12");
INSERT INTO lesson_tag VALUES("30","47","12");
INSERT INTO lesson_tag VALUES("31","47","13");
INSERT INTO lesson_tag VALUES("32","47","14");
INSERT INTO lesson_tag VALUES("33","48","12");
INSERT INTO lesson_tag VALUES("34","48","14");
INSERT INTO lesson_tag VALUES("35","49","13");
INSERT INTO lesson_tag VALUES("36","49","15");
INSERT INTO lesson_tag VALUES("37","49","16");
INSERT INTO lesson_tag VALUES("38","50","17");



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
INSERT INTO master VALUES("LOCK_COUNT","5");
INSERT INTO master VALUES("LOGIN_FAIL_LOCK_TIME","3600");
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

INSERT INTO question VALUES("31","56","Q2","S4","10");
INSERT INTO question VALUES("32","56","Q3","S3","9");
INSERT INTO question VALUES("33","56","Q4","S1","16");
INSERT INTO question VALUES("34","57","Q1","S1","10");
INSERT INTO question VALUES("35","57","Q2","S4","5");
INSERT INTO question VALUES("36","57","Q3","S3","5");
INSERT INTO question VALUES("37","57","Q4","S1","20");
INSERT INTO question VALUES("38","57","Q5","S2","10");
INSERT INTO question VALUES("39","58","Q1","S1","10");
INSERT INTO question VALUES("40","58","Q2","S4","5");
INSERT INTO question VALUES("41","58","Q3","S3","5");
INSERT INTO question VALUES("42","58","Q4","S1","20");
INSERT INTO question VALUES("43","63","Q1","S1","20");
INSERT INTO question VALUES("44","63","Q2","S4","10");
INSERT INTO question VALUES("45","63","Q3","S3","9");
INSERT INTO question VALUES("46","63","Q4","S1","16");
INSERT INTO question VALUES("47","64","Q1","S1","20");
INSERT INTO question VALUES("48","64","Q2","S4","10");
INSERT INTO question VALUES("49","64","Q3","S3","9");
INSERT INTO question VALUES("50","64","Q4","S1","16");
INSERT INTO question VALUES("51","76","Q1","S1","20");
INSERT INTO question VALUES("52","76","Q2","S4","10");
INSERT INTO question VALUES("53","76","Q3","S3","9");
INSERT INTO question VALUES("54","76","Q4","S1","16");
INSERT INTO question VALUES("55","77","Q1","S1","10");
INSERT INTO question VALUES("56","77","Q2","S4","5");
INSERT INTO question VALUES("57","77","Q3","S3","5");
INSERT INTO question VALUES("58","77","Q4","S1","20");
INSERT INTO question VALUES("59","77","Q5","S2","10");
INSERT INTO question VALUES("60","88","Q1","S1","20");
INSERT INTO question VALUES("61","88","Q2","S4","10");
INSERT INTO question VALUES("62","88","Q3","S3","9");
INSERT INTO question VALUES("63","88","Q4","S1","16");
INSERT INTO question VALUES("64","90","Q1","S1","20");
INSERT INTO question VALUES("65","90","Q2","S4","10");
INSERT INTO question VALUES("66","90","Q3","S3","9");
INSERT INTO question VALUES("67","90","Q4","S1","16");



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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

INSERT INTO tag VALUES("12","IT 日本語");
INSERT INTO tag VALUES("13","ソフトウエア");
INSERT INTO tag VALUES("14","ハードウエア");
INSERT INTO tag VALUES("15","英語");
INSERT INTO tag VALUES("16","プロジェクト管理");
INSERT INTO tag VALUES("17","地球");



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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

INSERT INTO user VALUES("18","thayminh","87683ef352817ffc68a1bcddf45f4e18930a617f","87683ef352817ffc68a1bcddf45f4e18930a617f","Minh Tran","0","tminhhp@gmail.com","13-10-2001","45 Trần Đại Nghĩa","0987654321","711A12334534","nick gg","hnimnart","nick gg","hnimnart","2","1","127.0.0.1","1","");
INSERT INTO user VALUES("19","minhtq","976450ee760f6dace9a481bdf6406728019ce7ca","976450ee760f6dace9a481bdf6406728019ce7ca","Trần Quang Minh","0","tminh_1234@yahoo.com","17-6-2001","42 Lê Thanh Nghị","0987654321","711A12334534","nick gg","hnimnart","nick gg","hnimnart","1","1","127.0.0.1","4","");
INSERT INTO user VALUES("22","thayminh2","11ed51e5fbac4770f5db86f67db563c4273cb747","11ed51e5fbac4770f5db86f67db563c4273cb747","Giao Su Minh","0","tminh.hp@hotmail.com","17-9-2006","69 Trần Đại Nghĩa","0987654321","711A12334534","nick gg","c978ce09940e3c299827392480c655cd596b32ee","nick gg","c978ce09940e3c299827392480c655cd596b32ee","2","1","127.0.0.1","0","");
INSERT INTO user VALUES("23","thayminh3","8d3ae284cd69f79feb06b861297b3db631f60e92","8d3ae284cd69f79feb06b861297b3db631f60e92","Tiến Sĩ Minh","0","minhtq@rikkei.com","15-7-2002","45 Trần Đại Nghĩa","0987654321","711A12334534","nick gg","c978ce09940e3c299827392480c655cd596b32ee","nick gg","c978ce09940e3c299827392480c655cd596b32ee","2","1","127.0.0.1","0","");



