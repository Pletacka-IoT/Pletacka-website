-- Adminer 4.7.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `A1`;
CREATE TABLE `A1` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `A10`;
CREATE TABLE `A10` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `A11`;
CREATE TABLE `A11` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `A12`;
CREATE TABLE `A12` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `A13`;
CREATE TABLE `A13` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `A14`;
CREATE TABLE `A14` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `A15`;
CREATE TABLE `A15` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `A16`;
CREATE TABLE `A16` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `A17`;
CREATE TABLE `A17` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `A18`;
CREATE TABLE `A18` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `A19`;
CREATE TABLE `A19` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `A2`;
CREATE TABLE `A2` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `A20`;
CREATE TABLE `A20` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `A21`;
CREATE TABLE `A21` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `A22`;
CREATE TABLE `A22` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `A23`;
CREATE TABLE `A23` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `A24`;
CREATE TABLE `A24` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `A3`;
CREATE TABLE `A3` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `A30`;
CREATE TABLE `A30` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `A30` (`id`, `state`, `time`) VALUES
(1,	'FINISHED',	'2020-08-22 20:19:26'),
(2,	'FINISHED',	'2020-08-22 20:19:32'),
(3,	'FINISHED',	'2020-08-22 20:19:37'),
(4,	'FINISHED',	'2020-08-22 20:47:04'),
(32,	'OFF',	'2020-09-13 13:21:44'),
(41,	'ON',	'2020-09-13 14:52:11'),
(42,	'OFF',	'2020-09-13 14:52:47'),
(43,	'ON',	'2020-09-13 14:53:10'),
(44,	'OFF',	'2020-09-13 14:55:17'),
(45,	'ON',	'2020-09-13 14:55:55'),
(46,	'OFF',	'2020-09-13 15:17:55'),
(47,	'ON',	'2020-09-13 15:23:07'),
(48,	'OFF',	'2020-09-13 15:23:23');

DROP TABLE IF EXISTS `A33`;
CREATE TABLE `A33` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `A33` (`id`, `state`, `time`) VALUES
(1,	'FINISHED',	'2020-04-24 20:00:00'),
(2,	'STOP',	'2020-04-24 20:03:00'),
(3,	'REWORK',	'2020-04-24 20:06:00'),
(4,	'ON',	'2020-04-24 20:08:00'),
(5,	'FINISHED',	'2020-04-24 20:09:00'),
(6,	'FINISHED',	'2020-04-24 20:12:00'),
(7,	'STOP',	'2020-04-24 20:13:00'),
(8,	'REWORK',	'2020-04-24 20:14:30'),
(9,	'FINISHED',	'2020-04-24 20:16:00'),
(10,	'OFF',	'2020-04-24 20:17:00'),
(14,	'ON',	'2020-04-24 20:20:00'),
(15,	'FINISHED',	'2020-04-24 20:22:00'),
(16,	'OFF',	'2020-09-16 19:30:57');

DROP TABLE IF EXISTS `A4`;
CREATE TABLE `A4` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `A5`;
CREATE TABLE `A5` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `A6`;
CREATE TABLE `A6` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `A7`;
CREATE TABLE `A7` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `A8`;
CREATE TABLE `A8` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `A9`;
CREATE TABLE `A9` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('FINISHED','STOP','REWORK','ON','OFF') COLLATE utf8_czech_ci NOT NULL DEFAULT 'FINISHED',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `sensors`;
CREATE TABLE `sensors` (
  `number` int(11) NOT NULL,
  `description` text COLLATE utf8_czech_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('on','off') COLLATE utf8_czech_ci NOT NULL DEFAULT 'off',
  PRIMARY KEY (`number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `sensors` (`number`, `description`, `date`, `status`) VALUES
(1,	'',	'2020-07-30 19:23:15',	'off'),
(2,	'',	'2020-07-30 19:23:53',	'off'),
(3,	'',	'2020-07-30 19:41:56',	'off'),
(4,	'',	'0000-00-00 00:00:00',	'off'),
(5,	'',	'0000-00-00 00:00:00',	'off'),
(6,	'',	'2020-08-16 12:13:21',	'off'),
(7,	'',	'0000-00-00 00:00:00',	'off'),
(8,	'',	'0000-00-00 00:00:00',	'off'),
(9,	'',	'0000-00-00 00:00:00',	'off'),
(10,	'',	'0000-00-00 00:00:00',	'off'),
(11,	'',	'0000-00-00 00:00:00',	'off'),
(12,	'',	'0000-00-00 00:00:00',	'off'),
(13,	'',	'0000-00-00 00:00:00',	'off'),
(14,	'',	'0000-00-00 00:00:00',	'off'),
(15,	'',	'0000-00-00 00:00:00',	'off'),
(16,	'',	'0000-00-00 00:00:00',	'off'),
(17,	'',	'0000-00-00 00:00:00',	'off'),
(18,	'',	'0000-00-00 00:00:00',	'off'),
(19,	'',	'0000-00-00 00:00:00',	'off'),
(20,	'',	'0000-00-00 00:00:00',	'off'),
(21,	'',	'0000-00-00 00:00:00',	'off'),
(22,	'',	'0000-00-00 00:00:00',	'off'),
(23,	'',	'0000-00-00 00:00:00',	'off'),
(24,	'',	'0000-00-00 00:00:00',	'off'),
(30,	'',	'0000-00-00 00:00:00',	'off'),
(33,	'',	'0000-00-00 00:00:00',	'on');

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `web_name` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  `web_description` text COLLATE utf8_czech_ci NOT NULL,
  `title_footer` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  `work_shift_A` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  `work_shift_B` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  `title_pair_count` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  `title_error_count` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  `title_succes_rate` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  `title_stop_time` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `settings` (`ID`, `web_name`, `web_description`, `title_footer`, `work_shift_A`, `work_shift_B`, `title_pair_count`, `title_error_count`, `title_succes_rate`, `title_stop_time`) VALUES
(1,	'Pletačka',	'By Kuba Andrýsek',	'Pletačka',	'Cahovi',	'Vaňkovi',	'Páry za směnu',	'Poruchy za směnu',	'Úspěšnost',	'Průměrná doba stání');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `role` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1,	'root',	'$2y$10$zCiX9PNm.i/UCGZHMGB3cutde0SsD1F6ZRBR6dcLoGDPXuWDY..ku',	'admin'),
(18,	'kuba',	'$2y$10$SzgVdAHSFgDqROPFp/qfU.o.hiDO8.UwXBxW4Ts6Df307L6y0svoa',	'member'),
(19,	'pletacka',	'$2y$10$d.7waWlDbmI.JMgUtHQMjeQSDIXh/9OSR3Zu5LGENdq3c.v6IhWEy',	'admin');

DROP TABLE IF EXISTS `workShift`;
CREATE TABLE `workShift` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `year` year(4) NOT NULL,
  `week` int(11) NOT NULL,
  `wsA` enum('Cahovi','Vaňkovi') COLLATE utf8_czech_ci NOT NULL,
  `wsB` enum('Cahovi','Vaňkovi') COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `workShift` (`id`, `year`, `week`, `wsA`, `wsB`) VALUES
(239,	'2020',	1,	'Vaňkovi',	'Cahovi'),
(240,	'2020',	2,	'Cahovi',	'Vaňkovi'),
(241,	'2020',	3,	'Vaňkovi',	'Cahovi'),
(242,	'2020',	4,	'Cahovi',	'Vaňkovi'),
(243,	'2020',	5,	'Vaňkovi',	'Cahovi'),
(244,	'2020',	6,	'Cahovi',	'Vaňkovi'),
(245,	'2020',	7,	'Vaňkovi',	'Cahovi'),
(246,	'2020',	8,	'Cahovi',	'Vaňkovi'),
(247,	'2020',	9,	'Vaňkovi',	'Cahovi'),
(248,	'2020',	10,	'Cahovi',	'Vaňkovi'),
(249,	'2020',	11,	'Vaňkovi',	'Cahovi'),
(250,	'2020',	12,	'Cahovi',	'Vaňkovi'),
(251,	'2020',	13,	'Vaňkovi',	'Cahovi'),
(252,	'2020',	14,	'Cahovi',	'Vaňkovi'),
(253,	'2020',	15,	'Vaňkovi',	'Cahovi'),
(254,	'2020',	16,	'Cahovi',	'Vaňkovi'),
(255,	'2020',	17,	'Vaňkovi',	'Cahovi'),
(256,	'2020',	18,	'Cahovi',	'Vaňkovi'),
(257,	'2020',	19,	'Vaňkovi',	'Cahovi'),
(258,	'2020',	20,	'Cahovi',	'Vaňkovi'),
(259,	'2020',	21,	'Vaňkovi',	'Cahovi'),
(260,	'2020',	22,	'Cahovi',	'Vaňkovi'),
(261,	'2020',	23,	'Vaňkovi',	'Cahovi'),
(262,	'2020',	24,	'Cahovi',	'Vaňkovi'),
(263,	'2020',	25,	'Vaňkovi',	'Cahovi'),
(264,	'2020',	26,	'Cahovi',	'Vaňkovi'),
(265,	'2020',	27,	'Vaňkovi',	'Cahovi'),
(266,	'2020',	28,	'Cahovi',	'Vaňkovi'),
(267,	'2020',	29,	'Vaňkovi',	'Cahovi'),
(268,	'2020',	30,	'Cahovi',	'Vaňkovi'),
(269,	'2020',	31,	'Vaňkovi',	'Cahovi'),
(270,	'2020',	32,	'Cahovi',	'Vaňkovi'),
(271,	'2020',	33,	'Vaňkovi',	'Cahovi'),
(272,	'2020',	34,	'Cahovi',	'Vaňkovi'),
(273,	'2020',	35,	'Vaňkovi',	'Cahovi'),
(274,	'2020',	36,	'Cahovi',	'Vaňkovi'),
(275,	'2020',	37,	'Vaňkovi',	'Cahovi'),
(276,	'2020',	38,	'Cahovi',	'Vaňkovi'),
(277,	'2020',	39,	'Vaňkovi',	'Cahovi'),
(278,	'2020',	40,	'Cahovi',	'Vaňkovi'),
(279,	'2020',	41,	'Vaňkovi',	'Cahovi'),
(280,	'2020',	42,	'Cahovi',	'Vaňkovi'),
(281,	'2020',	43,	'Vaňkovi',	'Cahovi'),
(282,	'2020',	44,	'Cahovi',	'Vaňkovi'),
(283,	'2020',	45,	'Vaňkovi',	'Cahovi'),
(284,	'2020',	46,	'Cahovi',	'Vaňkovi'),
(285,	'2020',	47,	'Vaňkovi',	'Cahovi'),
(286,	'2020',	48,	'Cahovi',	'Vaňkovi'),
(287,	'2020',	49,	'Vaňkovi',	'Cahovi'),
(288,	'2020',	50,	'Cahovi',	'Vaňkovi'),
(289,	'2020',	51,	'Vaňkovi',	'Cahovi'),
(290,	'2020',	52,	'Cahovi',	'Vaňkovi');

-- 2020-10-03 18:52:16