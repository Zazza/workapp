-- phpMyAdmin SQL Dump
-- version 3.4.9deb1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Окт 23 2012 г., 18:53
-- Версия сервера: 5.5.24
-- Версия PHP: 5.3.10-1ubuntu3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `workapp`
--

-- --------------------------------------------------------

--
-- Структура таблицы `chat`
--

CREATE TABLE IF NOT EXISTS `chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `who` int(11) NOT NULL,
  `text` varchar(2048) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `who` (`who`),
  KEY `cid` (`cid`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `chat_room`
--

CREATE TABLE IF NOT EXISTS `chat_room` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `parts` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `chat_room_part`
--

CREATE TABLE IF NOT EXISTS `chat_room_part` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cid` (`cid`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `comments_status`
--

CREATE TABLE IF NOT EXISTS `comments_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `comments_status`
--

INSERT INTO `comments_status` (`id`, `status`) VALUES
(1, 'Готово'),
(2, 'Уточнить'),
(3, 'Отправить в другой отдел'),
(4, 'Отложено');

-- --------------------------------------------------------

--
-- Структура таблицы `contacts`
--

CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cont_type` varchar(32) NOT NULL,
  `client` varchar(512) NOT NULL,
  `contact_person` varchar(512) NOT NULL,
  `phone` varchar(32) NOT NULL,
  `adres` varchar(512) NOT NULL,
  `timestamp` date NOT NULL,
  `result` varchar(32) NOT NULL,
  `survey` date NOT NULL,
  `info` varchar(1024) NOT NULL,
  `manager` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `draft`
--

CREATE TABLE IF NOT EXISTS `draft` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `oid` int(11) NOT NULL,
  `who` int(11) NOT NULL,
  `imp` tinyint(4) NOT NULL,
  `secure` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL,
  `text` text NOT NULL,
  `opening` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ending` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `gid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `draft_attach`
--

CREATE TABLE IF NOT EXISTS `draft_attach` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `md5` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tid` (`tid`,`md5`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `draft_composite`
--

CREATE TABLE IF NOT EXISTS `draft_composite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `draft_deadline`
--

CREATE TABLE IF NOT EXISTS `draft_deadline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `opening` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deadline` int(10) unsigned NOT NULL DEFAULT '0',
  `iteration` int(10) unsigned NOT NULL DEFAULT '0',
  `timetype_iteration` varchar(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `draft_objects`
--

CREATE TABLE IF NOT EXISTS `draft_objects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `oid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`,`oid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `draft_responsible`
--

CREATE TABLE IF NOT EXISTS `draft_responsible` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `gid` int(11) NOT NULL DEFAULT '0',
  `all` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq` (`tid`,`uid`,`gid`,`all`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `draft_route`
--

CREATE TABLE IF NOT EXISTS `draft_route` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `draft_route_action`
--

CREATE TABLE IF NOT EXISTS `draft_route_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `step_id` int(11) NOT NULL,
  `ifdata` int(11) NOT NULL,
  `ifcon` varchar(4) NOT NULL,
  `ifval` varchar(64) NOT NULL,
  `goto` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `draft_route_route_tasks`
--

CREATE TABLE IF NOT EXISTS `draft_route_route_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `step_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `draft_route_step`
--

CREATE TABLE IF NOT EXISTS `draft_route_step` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `draft_route_tasks`
--

CREATE TABLE IF NOT EXISTS `draft_route_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `json` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `draft_route_tasks_results`
--

CREATE TABLE IF NOT EXISTS `draft_route_tasks_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `datatype` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_dirs`
--

CREATE TABLE IF NOT EXISTS `fm_dirs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL,
  `close` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_dirs_chmod`
--

CREATE TABLE IF NOT EXISTS `fm_dirs_chmod` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `did` int(11) NOT NULL,
  `right` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_fs`
--

CREATE TABLE IF NOT EXISTS `fm_fs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `md5` varchar(64) NOT NULL,
  `filename` varchar(256) NOT NULL,
  `pdirid` int(11) NOT NULL DEFAULT '0',
  `size` int(11) NOT NULL,
  `close` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_fs_chmod`
--

CREATE TABLE IF NOT EXISTS `fm_fs_chmod` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `right` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_fs_history`
--

CREATE TABLE IF NOT EXISTS `fm_fs_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_share`
--

CREATE TABLE IF NOT EXISTS `fm_share` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `md5` varchar(64) NOT NULL,
  `desc` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_text`
--

CREATE TABLE IF NOT EXISTS `fm_text` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `text` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `group_tt`
--

CREATE TABLE IF NOT EXISTS `group_tt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(8) NOT NULL,
  `event` text NOT NULL,
  `uid` int(11) NOT NULL,
  `oid` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `logs_closed`
--

CREATE TABLE IF NOT EXISTS `logs_closed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `logs_dashajax`
--

CREATE TABLE IF NOT EXISTS `logs_dashajax` (
  `uid` int(11) NOT NULL,
  `lid` int(11) NOT NULL,
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `logs_object`
--

CREATE TABLE IF NOT EXISTS `logs_object` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log_oid` int(11) NOT NULL,
  `key` varchar(64) NOT NULL,
  `val` text NOT NULL,
  KEY `id` (`id`),
  KEY `log_oid` (`log_oid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
PARTITION BY KEY (id)
PARTITIONS 10 ;

-- --------------------------------------------------------

--
-- Структура таблицы `mail`
--

CREATE TABLE IF NOT EXISTS `mail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uidl` varchar(64) NOT NULL,
  `read` tinyint(4) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `to` varchar(128) NOT NULL,
  `subject` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `personal` varchar(256) NOT NULL DEFAULT '0',
  `email` varchar(128) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `mail_attach`
--

CREATE TABLE IF NOT EXISTS `mail_attach` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `tdid` int(11) NOT NULL,
  `md5` varchar(64) NOT NULL,
  `filename` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `mail_attach_out`
--

CREATE TABLE IF NOT EXISTS `mail_attach_out` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mid` int(11) NOT NULL,
  `md5` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `mail_contacts`
--

CREATE TABLE IF NOT EXISTS `mail_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `oid` int(11) NOT NULL,
  `email` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oid` (`oid`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `mail_folders`
--

CREATE TABLE IF NOT EXISTS `mail_folders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `folder` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `mail_out`
--

CREATE TABLE IF NOT EXISTS `mail_out` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to` varchar(128) NOT NULL,
  `subject` varchar(256) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `email` varchar(128) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `mail_signature`
--

CREATE TABLE IF NOT EXISTS `mail_signature` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bid` int(11) NOT NULL,
  `signature` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `mail_sort`
--

CREATE TABLE IF NOT EXISTS `mail_sort` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sort_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `type` varchar(16) NOT NULL,
  `val` varchar(128) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `task` text NOT NULL,
  `action` varchar(8) NOT NULL DEFAULT 'move',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`,`type`,`val`,`folder_id`),
  KEY `action` (`action`),
  KEY `sort_id` (`sort_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `mail_text`
--

CREATE TABLE IF NOT EXISTS `mail_text` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mid` int(11) NOT NULL,
  `type` varchar(16) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
PARTITION BY KEY (id)
PARTITIONS 10 ;

-- --------------------------------------------------------

--
-- Структура таблицы `mail_text_out`
--

CREATE TABLE IF NOT EXISTS `mail_text_out` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mid` int(11) NOT NULL,
  `type` varchar(16) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
PARTITION BY KEY (id)
PARTITIONS 10 ;

-- --------------------------------------------------------

--
-- Структура таблицы `note`
--

CREATE TABLE IF NOT EXISTS `note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `content` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `objects`
--

CREATE TABLE IF NOT EXISTS `objects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trash` tinyint(4) NOT NULL DEFAULT '0',
  `template` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `template` (`template`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `objects_advanced`
--

CREATE TABLE IF NOT EXISTS `objects_advanced` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `oid` int(11) NOT NULL,
  `fid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(128) NOT NULL,
  `val` text NOT NULL,
  `who` int(11) NOT NULL,
  `euid` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `edittime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `who` (`who`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `objects_forms`
--

CREATE TABLE IF NOT EXISTS `objects_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `objects_forms_fields`
--

CREATE TABLE IF NOT EXISTS `objects_forms_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `field` varchar(128) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `datatype` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `objects_forms_view`
--

CREATE TABLE IF NOT EXISTS `objects_forms_view` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `w` int(11) NOT NULL,
  `h` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `objects_tags`
--

CREATE TABLE IF NOT EXISTS `objects_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `oaid` int(11) NOT NULL,
  `tag` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oaid` (`oaid`,`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `objects_vals`
--

CREATE TABLE IF NOT EXISTS `objects_vals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `oid` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `val` text NOT NULL,
  `uid` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oid` (`oid`,`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `otms_mail`
--

CREATE TABLE IF NOT EXISTS `otms_mail` (
  `email` varchar(128) NOT NULL,
  `server` varchar(128) NOT NULL,
  `protocol` varchar(8) NOT NULL,
  `port` int(11) NOT NULL,
  `auth` tinyint(4) NOT NULL,
  `login` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `ssl` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `photo_dirs`
--

CREATE TABLE IF NOT EXISTS `photo_dirs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL,
  `close` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `photo_dirs_chmod`
--

CREATE TABLE IF NOT EXISTS `photo_dirs_chmod` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `did` int(11) NOT NULL,
  `right` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `photo_favorite`
--

CREATE TABLE IF NOT EXISTS `photo_favorite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fid` (`fid`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `photo_fs`
--

CREATE TABLE IF NOT EXISTS `photo_fs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `md5` varchar(64) NOT NULL,
  `filename` varchar(256) NOT NULL,
  `pdirid` int(11) NOT NULL DEFAULT '0',
  `size` int(11) NOT NULL,
  `close` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `photo_fs_chmod`
--

CREATE TABLE IF NOT EXISTS `photo_fs_chmod` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `right` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `photo_fs_history`
--

CREATE TABLE IF NOT EXISTS `photo_fs_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `photo_photo_desc`
--

CREATE TABLE IF NOT EXISTS `photo_photo_desc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `desc` varchar(256) NOT NULL,
  `x1` int(11) NOT NULL,
  `y1` int(11) NOT NULL,
  `x2` int(11) NOT NULL,
  `y2` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `photo_photo_tags`
--

CREATE TABLE IF NOT EXISTS `photo_photo_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `tag` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `photo_text`
--

CREATE TABLE IF NOT EXISTS `photo_text` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `text` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `process`
--

CREATE TABLE IF NOT EXISTS `process` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rid` int(11) NOT NULL,
  `start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `process_tasks`
--

CREATE TABLE IF NOT EXISTS `process_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `close` tinyint(1) NOT NULL DEFAULT '0',
  `rid` int(11) NOT NULL,
  `step_id` int(11) NOT NULL,
  `route_tid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `process_tasks_results`
--

CREATE TABLE IF NOT EXISTS `process_tasks_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `key` int(11) NOT NULL,
  `val` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `reserv`
--

CREATE TABLE IF NOT EXISTS `reserv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `oid` int(11) NOT NULL,
  `start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `route`
--

CREATE TABLE IF NOT EXISTS `route` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `route_action`
--

CREATE TABLE IF NOT EXISTS `route_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `step_id` int(11) NOT NULL,
  `ifdata` int(11) NOT NULL,
  `ifcon` varchar(4) NOT NULL,
  `ifval` varchar(64) NOT NULL,
  `goto` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `route_route_tasks`
--

CREATE TABLE IF NOT EXISTS `route_route_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `step_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `route_step`
--

CREATE TABLE IF NOT EXISTS `route_step` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `route_tasks`
--

CREATE TABLE IF NOT EXISTS `route_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `json` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `route_tasks_results`
--

CREATE TABLE IF NOT EXISTS `route_tasks_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `datatype` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `templates`
--

CREATE TABLE IF NOT EXISTS `templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `templates_datatypes`
--

CREATE TABLE IF NOT EXISTS `templates_datatypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `templates_datavals`
--

CREATE TABLE IF NOT EXISTS `templates_datavals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `did` int(11) NOT NULL,
  `val` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `templates_fields`
--

CREATE TABLE IF NOT EXISTS `templates_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `field` varchar(128) NOT NULL,
  `main` tinyint(4) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `datatype` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `templates_view`
--

CREATE TABLE IF NOT EXISTS `templates_view` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `w` int(11) NOT NULL,
  `h` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `troubles`
--

CREATE TABLE IF NOT EXISTS `troubles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `route` tinyint(1) NOT NULL DEFAULT '0',
  `remote_id` int(11) NOT NULL DEFAULT '0',
  `mail_id` int(11) NOT NULL DEFAULT '0',
  `oid` int(11) NOT NULL,
  `who` int(11) NOT NULL,
  `imp` tinyint(4) NOT NULL,
  `secure` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(256) NOT NULL,
  `text` text NOT NULL,
  `opening` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `edittime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ending` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `gid` int(11) NOT NULL DEFAULT '0',
  `close` tinyint(4) NOT NULL DEFAULT '0',
  `cuid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `text` (`text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `troubles_attach`
--

CREATE TABLE IF NOT EXISTS `troubles_attach` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `md5` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tid` (`tid`,`md5`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `troubles_composite`
--

CREATE TABLE IF NOT EXISTS `troubles_composite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`,`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `troubles_deadline`
--

CREATE TABLE IF NOT EXISTS `troubles_deadline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `opening` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deadline` int(10) unsigned NOT NULL DEFAULT '0',
  `iteration` int(10) unsigned NOT NULL DEFAULT '0',
  `timetype_iteration` varchar(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `troubles_discussion`
--

CREATE TABLE IF NOT EXISTS `troubles_discussion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `remote` tinyint(4) NOT NULL DEFAULT '0',
  `mail_id` int(11) NOT NULL DEFAULT '0',
  `object` int(11) NOT NULL DEFAULT '0',
  `sendmail` tinyint(4) NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `troubles_discussion_attach`
--

CREATE TABLE IF NOT EXISTS `troubles_discussion_attach` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tdid` int(11) NOT NULL,
  `md5` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tid` (`tdid`,`md5`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `troubles_objects`
--

CREATE TABLE IF NOT EXISTS `troubles_objects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `oid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`,`oid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `troubles_remote_contact`
--

CREATE TABLE IF NOT EXISTS `troubles_remote_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `soname` varchar(64) NOT NULL,
  `avatar` varchar(64) NOT NULL,
  `group` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `troubles_responsible`
--

CREATE TABLE IF NOT EXISTS `troubles_responsible` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `gid` int(11) NOT NULL DEFAULT '0',
  `all` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq` (`tid`,`uid`,`gid`,`all`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `troubles_spam`
--

CREATE TABLE IF NOT EXISTS `troubles_spam` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `troubles_view`
--

CREATE TABLE IF NOT EXISTS `troubles_view` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`,`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(32) NOT NULL,
  `pass` varchar(32) NOT NULL,
  `quota` int(10) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `soname` varchar(64) NOT NULL,
  `signature` varchar(64) NOT NULL,
  `icq` varchar(64) NOT NULL,
  `skype` varchar(64) NOT NULL,
  `adres` varchar(256) NOT NULL,
  `phone` varchar(16) NOT NULL,
  `avatar` varchar(32) NOT NULL,
  `email` varchar(64) NOT NULL,
  `notify` tinyint(4) NOT NULL,
  `time_notify` time NOT NULL DEFAULT '08:00:00',
  `last_notify` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email_for_task` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `pass`, `quota`, `name`, `soname`, `signature`, `icq`, `skype`, `adres`, `phone`, `avatar`, `email`, `notify`, `time_notify`, `last_notify`, `email_for_task`) VALUES
(1, 'otmsadmin', '038d38e540dc0d88d29f8b406ea04b97', 104857600, 'Дмитрий', 'Самотой', '', '', '', '', '', '', 'example@domen.ru', 1, '08:00:00', '2012-01-14 09:01:01', 0),
(2, 'user100', '1d44769f1bfc8177e07780d6e0a62f14', 0, 'Леонид', 'Иванов', 'секретарь оп', '', '', '', '', 'ava2_3.jpg', 'leonid.ivanov@domen.com', 0, '08:00:00', '2012-03-11 16:16:11', 0),
(3, 'user101', 'b4be890e0bd4befccc2da33a7a140133', 0, 'Надежда', 'Петрова', 'страший менеджер', '', '', '', '', 'ava3_2.jpg', 'nadezhda.petrova@domen.com', 0, '08:00:00', '2012-03-11 16:17:28', 0),
(4, 'user107', 'ded0cd5c9d9ce701ab386d6c8afc75e4', 104857600, 'Леонид', 'Капустин', 'директор по договорам', '', '', '', '', '', 'boss@gigcompany.ru', 0, '08:00:00', '2012-05-24 15:51:48', 0),
(5, 'dfgfd', 'b2da368a77e530e71a73bcf2cdd229d3', 104857600, 'sdfds', 'tretr', 'ertre', '', '', '', '', '', 'ertre@fdsf.ru', 0, '08:00:00', '2012-10-02 09:20:25', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `users_auth`
--

CREATE TABLE IF NOT EXISTS `users_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `auth` tinyint(4) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=538 ;

--
-- Дамп данных таблицы `users_auth`
--

INSERT INTO `users_auth` (`id`, `uid`, `auth`, `timestamp`) VALUES
(1, 1, 0, '2011-12-22 16:03:32'),
(2, 1, 1, '2011-12-27 10:50:31'),
(3, 1, 1, '2011-12-28 17:04:39'),
(4, 1, 0, '2011-12-28 18:04:45'),
(5, 1, 1, '2012-01-08 10:08:21'),
(6, 1, 0, '2012-01-08 11:12:28'),
(7, 1, 1, '2012-01-08 13:18:21'),
(8, 1, 0, '2012-01-08 18:42:59'),
(9, 1, 1, '2012-01-09 04:38:22'),
(10, 1, 0, '2012-01-09 18:17:25'),
(11, 1, 1, '2012-01-10 07:24:25'),
(12, 1, 0, '2012-01-10 18:33:28'),
(13, 1, 1, '2012-01-11 04:00:59'),
(14, 1, 0, '2012-01-11 04:01:36'),
(15, 1, 1, '2012-01-11 07:08:48'),
(16, 1, 0, '2012-01-11 17:41:33'),
(17, 1, 1, '2012-01-12 04:16:41'),
(18, 1, 1, '2012-01-13 05:09:50'),
(19, 1, 0, '2012-01-13 05:17:22'),
(20, 1, 0, '2012-01-13 05:19:51'),
(21, 1, 1, '2012-01-13 05:37:16'),
(22, 1, 1, '2012-01-14 04:21:36'),
(23, 1, 0, '2012-01-14 09:19:39'),
(24, 1, 1, '2012-01-14 10:39:44'),
(25, 1, 0, '2012-01-14 10:50:28'),
(26, 1, 1, '2012-01-15 05:13:56'),
(27, 1, 0, '2012-01-15 05:14:23'),
(28, 1, 1, '2012-01-15 13:35:42'),
(29, 1, 0, '2012-01-15 14:44:21'),
(30, 1, 1, '2012-01-16 04:33:34'),
(31, 1, 0, '2012-01-16 04:33:58'),
(32, 1, 1, '2012-01-16 06:48:33'),
(33, 1, 1, '2012-01-16 10:48:37'),
(34, 1, 0, '2012-01-16 10:52:33'),
(35, 1, 1, '2012-01-17 13:43:42'),
(36, 1, 0, '2012-01-17 13:44:16'),
(37, 6, 0, '2012-01-19 12:27:55'),
(38, 1, 1, '2012-01-19 12:29:11'),
(39, 1, 1, '2012-01-19 16:13:19'),
(40, 1, 0, '2012-01-19 16:14:19'),
(41, 1, 1, '2012-01-20 04:20:29'),
(42, 29, 0, '2012-01-20 08:32:01'),
(43, 1, 1, '2012-01-20 08:32:34'),
(44, 29, 0, '2012-01-20 08:35:53'),
(45, 1, 1, '2012-01-20 08:36:18'),
(46, 29, 0, '2012-01-20 08:38:14'),
(47, 29, 0, '2012-01-20 08:38:34'),
(48, 29, 0, '2012-01-20 08:43:46'),
(49, 1, 1, '2012-01-20 08:44:05'),
(50, 1, 0, '2012-01-20 08:47:29'),
(51, 29, 0, '2012-01-20 08:48:24'),
(52, 29, 0, '2012-01-20 08:51:12'),
(53, 1, 1, '2012-01-20 08:55:01'),
(54, 1, 0, '2012-01-20 08:55:27'),
(55, 1, 1, '2012-01-20 08:56:36'),
(56, 30, 0, '2012-01-20 09:00:06'),
(57, 1, 1, '2012-01-20 09:00:41'),
(58, 30, 0, '2012-01-20 11:57:41'),
(59, 30, 0, '2012-01-20 12:04:15'),
(60, 30, 0, '2012-01-20 16:10:55'),
(61, 30, 0, '2012-01-20 16:11:15'),
(62, 30, 0, '2012-01-21 07:10:06'),
(63, 30, 0, '2012-01-21 07:15:13'),
(64, 29, 0, '2012-01-21 07:24:52'),
(65, 29, 0, '2012-01-21 07:27:46'),
(66, 1, 1, '2012-01-22 18:09:22'),
(67, 1, 0, '2012-01-22 18:10:12'),
(68, 1, 1, '2012-01-23 04:05:05'),
(69, 30, 0, '2012-01-23 10:46:28'),
(70, 1, 1, '2012-01-23 10:47:28'),
(71, 35, 0, '2012-01-23 12:58:27'),
(72, 37, 0, '2012-01-23 13:05:02'),
(73, 38, 0, '2012-01-23 13:10:43'),
(74, 1, 0, '2012-01-23 17:34:25'),
(75, 1, 1, '2012-01-23 17:37:15'),
(76, 1, 0, '2012-01-23 18:20:52'),
(77, 1, 1, '2012-01-23 18:26:54'),
(78, 39, 0, '2012-01-23 18:27:39'),
(79, 1, 1, '2012-01-23 18:29:02'),
(80, 1, 0, '2012-01-23 18:43:28'),
(81, 1, 1, '2012-01-24 04:32:27'),
(82, 1, 0, '2012-01-24 14:14:36'),
(83, 1, 1, '2012-01-25 04:25:49'),
(84, 1, 0, '2012-01-25 15:29:50'),
(85, 1, 1, '2012-01-26 04:05:09'),
(86, 29, 0, '2012-01-26 07:18:58'),
(87, 29, 0, '2012-01-26 07:21:58'),
(88, 29, 0, '2012-01-26 07:30:18'),
(89, 29, 0, '2012-01-26 09:12:31'),
(90, 29, 0, '2012-01-26 09:14:10'),
(91, 29, 0, '2012-01-26 09:38:59'),
(92, 1, 1, '2012-01-26 10:15:50'),
(93, 1, 0, '2012-01-26 15:22:29'),
(94, 1, 1, '2012-01-27 04:17:01'),
(95, 1, 1, '2012-01-27 15:19:42'),
(96, 1, 0, '2012-01-27 16:02:12'),
(97, 1, 1, '2012-01-27 16:56:57'),
(98, 1, 0, '2012-01-27 16:57:28'),
(99, 1, 1, '2012-01-28 04:54:04'),
(100, 1, 0, '2012-01-28 04:58:36'),
(101, 1, 1, '2012-01-28 06:48:16'),
(102, 1, 0, '2012-01-29 12:00:57'),
(103, 1, 1, '2012-01-29 13:40:39'),
(104, 1, 0, '2012-01-29 13:41:07'),
(105, 1, 1, '2012-01-29 16:32:38'),
(106, 1, 0, '2012-01-29 16:33:09'),
(107, 1, 1, '2012-01-30 04:11:51'),
(108, 29, 0, '2012-01-30 06:18:55'),
(109, 1, 1, '2012-01-30 06:20:04'),
(110, 1, 0, '2012-01-30 19:16:53'),
(111, 1, 1, '2012-01-31 03:55:20'),
(112, 1, 0, '2012-01-31 03:55:47'),
(113, 1, 1, '2012-01-31 14:18:17'),
(114, 1, 0, '2012-01-31 14:18:58'),
(115, 1, 1, '2012-02-01 14:29:03'),
(116, 1, 0, '2012-02-01 14:30:03'),
(117, 1, 1, '2012-02-07 12:26:35'),
(118, 1, 1, '2012-02-07 13:43:56'),
(119, 1, 1, '2012-02-08 03:56:39'),
(120, 1, 1, '2012-02-27 08:51:39'),
(121, 1, 0, '2012-02-27 13:33:33'),
(122, 1, 1, '2012-02-28 01:43:55'),
(123, 1, 0, '2012-02-28 02:16:50'),
(124, 1, 1, '2012-03-02 08:50:45'),
(125, 1, 0, '2012-03-02 08:51:01'),
(126, 1, 1, '2012-03-07 04:33:04'),
(127, 1, 0, '2012-03-07 04:33:17'),
(128, 1, 1, '2012-03-07 04:33:27'),
(129, 1, 0, '2012-03-07 04:33:42'),
(130, 1, 1, '2012-03-07 08:40:25'),
(131, 1, 0, '2012-03-07 10:29:27'),
(132, 1, 1, '2012-03-07 10:30:42'),
(133, 1, 0, '2012-03-07 11:18:09'),
(134, 1, 1, '2012-03-10 13:51:26'),
(135, 1, 0, '2012-03-10 17:32:41'),
(136, 1, 1, '2012-03-10 17:39:51'),
(137, 1, 0, '2012-03-10 17:44:21'),
(138, 1, 1, '2012-03-11 11:12:45'),
(139, 1, 1, '2012-03-11 12:46:31'),
(140, 1, 0, '2012-03-11 14:20:32'),
(141, 1, 1, '2012-03-11 14:21:12'),
(142, 1, 0, '2012-03-11 16:17:50'),
(143, 2, 1, '2012-03-11 16:18:01'),
(144, 2, 0, '2012-03-11 16:21:32'),
(145, 3, 1, '2012-03-11 16:21:46'),
(146, 3, 0, '2012-03-11 16:22:18'),
(147, 1, 1, '2012-03-11 16:22:38'),
(148, 1, 0, '2012-03-11 16:34:13'),
(149, 1, 1, '2012-03-11 16:35:06'),
(150, 1, 0, '2012-03-11 16:39:16'),
(151, 1, 1, '2012-03-12 04:48:30'),
(152, 1, 0, '2012-03-12 04:50:01'),
(153, 1, 1, '2012-03-12 15:05:33'),
(154, 1, 0, '2012-03-12 17:47:34'),
(155, 1, 1, '2012-03-12 18:27:37'),
(156, 1, 0, '2012-03-12 18:33:31'),
(157, 1, 1, '2012-03-13 07:24:14'),
(158, 1, 1, '2012-03-13 07:57:10'),
(159, 1, 0, '2012-03-13 07:57:30'),
(160, 2, 1, '2012-03-13 11:08:47'),
(161, 2, 0, '2012-03-13 11:09:42'),
(162, 1, 0, '2012-03-13 11:40:18'),
(163, 1, 1, '2012-03-13 12:47:43'),
(164, 1, 0, '2012-03-13 13:44:37'),
(165, 1, 1, '2012-03-14 14:27:09'),
(166, 1, 0, '2012-03-14 17:22:13'),
(167, 1, 1, '2012-03-14 17:43:07'),
(168, 1, 0, '2012-03-14 18:40:31'),
(169, 1, 1, '2012-03-15 15:19:35'),
(170, 1, 1, '2012-03-15 15:34:20'),
(171, 1, 0, '2012-03-15 15:36:19'),
(172, 1, 1, '2012-03-15 16:12:25'),
(173, 1, 1, '2012-03-15 16:21:56'),
(174, 1, 0, '2012-03-15 16:22:34'),
(175, 1, 1, '2012-03-15 17:54:06'),
(176, 1, 0, '2012-03-15 18:35:15'),
(177, 1, 1, '2012-03-16 05:53:55'),
(178, 1, 0, '2012-03-16 10:24:44'),
(179, 1, 1, '2012-03-18 12:45:33'),
(180, 1, 0, '2012-03-18 14:12:13'),
(181, 1, 1, '2012-03-18 14:12:53'),
(182, 1, 0, '2012-03-18 14:31:31'),
(183, 1, 1, '2012-03-19 15:21:56'),
(184, 1, 0, '2012-03-19 15:22:05'),
(185, 1, 1, '2012-03-19 15:22:20'),
(186, 1, 0, '2012-03-19 17:44:30'),
(187, 1, 1, '2012-03-20 05:54:21'),
(188, 3, 1, '2012-03-20 08:59:18'),
(189, 3, 0, '2012-03-20 10:32:38'),
(190, 1, 1, '2012-03-20 10:49:44'),
(191, 1, 0, '2012-03-20 10:51:01'),
(192, 3, 1, '2012-03-20 10:51:09'),
(193, 3, 0, '2012-03-20 11:57:15'),
(194, 1, 1, '2012-03-22 05:46:20'),
(195, 3, 1, '2012-03-22 07:15:02'),
(196, 3, 0, '2012-03-22 09:26:03'),
(197, 1, 0, '2012-03-22 09:39:36'),
(198, 1, 1, '2012-03-22 09:39:44'),
(199, 1, 0, '2012-03-22 10:12:08'),
(200, 1, 1, '2012-03-23 09:40:26'),
(201, 1, 0, '2012-03-23 09:41:50'),
(202, 1, 1, '2012-04-08 15:20:40'),
(203, 1, 0, '2012-04-08 15:52:58'),
(204, 1, 1, '2012-04-09 06:13:23'),
(205, 1, 0, '2012-04-09 10:10:36'),
(206, 1, 1, '2012-04-11 07:01:21'),
(207, 1, 0, '2012-04-11 07:06:52'),
(208, 1, 1, '2012-04-19 07:08:40'),
(209, 1, 1, '2012-04-28 17:58:58'),
(210, 1, 0, '2012-04-28 18:00:03'),
(211, 1, 1, '2012-04-29 04:38:24'),
(212, 1, 0, '2012-04-29 07:43:43'),
(213, 1, 1, '2012-05-03 07:57:57'),
(214, 1, 0, '2012-05-03 18:35:33'),
(215, 1, 1, '2012-05-06 04:00:36'),
(216, 1, 0, '2012-05-09 09:14:01'),
(217, 1, 1, '2012-05-09 09:14:14'),
(218, 1, 1, '2012-05-15 06:38:17'),
(219, 1, 1, '2012-05-15 06:38:23'),
(220, 1, 1, '2012-05-15 06:39:16'),
(221, 1, 1, '2012-05-15 06:40:12'),
(222, 1, 1, '2012-05-17 10:48:32'),
(223, 1, 1, '2012-05-17 10:48:36'),
(224, 1, 1, '2012-05-17 10:50:01'),
(225, 1, 0, '2012-05-17 13:17:48'),
(226, 1, 1, '2012-05-19 04:29:22'),
(227, 1, 0, '2012-05-19 04:33:43'),
(228, 1, 1, '2012-05-20 16:35:58'),
(229, 1, 0, '2012-05-20 16:51:31'),
(230, 1, 1, '2012-05-21 03:59:46'),
(231, 1, 0, '2012-05-21 11:21:37'),
(232, 1, 1, '2012-05-21 17:02:04'),
(233, 1, 0, '2012-05-21 19:23:39'),
(234, 2, 0, '2012-05-22 06:27:51'),
(235, 1, 1, '2012-05-22 06:28:04'),
(236, 1, 0, '2012-05-22 12:17:54'),
(237, 1, 1, '2012-05-23 06:35:43'),
(238, 1, 0, '2012-05-23 12:07:09'),
(239, 1, 1, '2012-05-23 17:24:40'),
(240, 1, 0, '2012-05-23 17:26:28'),
(241, 1, 1, '2012-05-24 05:16:13'),
(242, 1, 0, '2012-05-24 09:21:09'),
(243, 1, 1, '2012-05-24 12:15:18'),
(244, 1, 0, '2012-05-24 18:29:35'),
(245, 1, 1, '2012-05-25 04:30:54'),
(246, 1, 1, '2012-05-29 04:41:57'),
(247, 1, 0, '2012-05-29 13:42:32'),
(248, 1, 1, '2012-05-29 14:14:07'),
(249, 1, 0, '2012-05-29 18:37:18'),
(250, 1, 1, '2012-05-30 05:26:50'),
(251, 1, 0, '2012-05-30 10:36:04'),
(252, 1, 1, '2012-05-30 13:30:38'),
(253, 1, 0, '2012-05-30 18:50:00'),
(254, 1, 1, '2012-05-31 04:31:24'),
(255, 1, 0, '2012-05-31 14:36:08'),
(256, 1, 1, '2012-05-31 16:56:40'),
(257, 1, 0, '2012-05-31 18:27:04'),
(258, 1, 1, '2012-06-05 05:24:39'),
(259, 1, 0, '2012-06-05 09:33:39'),
(260, 1, 1, '2012-06-05 10:45:27'),
(261, 1, 1, '2012-06-07 08:43:12'),
(262, 1, 0, '2012-06-07 09:55:56'),
(263, 1, 1, '2012-06-19 11:07:12'),
(264, 1, 0, '2012-06-19 15:51:26'),
(265, 1, 1, '2012-06-19 18:13:05'),
(266, 1, 0, '2012-06-19 18:16:01'),
(267, 1, 1, '2012-06-19 18:51:29'),
(268, 1, 0, '2012-06-19 20:12:34'),
(269, 1, 1, '2012-06-20 06:17:05'),
(270, 1, 0, '2012-06-20 06:17:47'),
(271, 1, 1, '2012-06-21 11:18:31'),
(272, 1, 0, '2012-06-21 11:18:56'),
(273, 1, 1, '2012-06-22 06:39:26'),
(274, 1, 1, '2012-06-25 04:40:11'),
(275, 1, 0, '2012-06-25 20:12:27'),
(276, 1, 1, '2012-06-26 05:16:52'),
(277, 1, 0, '2012-06-26 19:25:50'),
(278, 1, 1, '2012-06-27 04:46:22'),
(279, 1, 0, '2012-06-27 08:19:16'),
(280, 1, 1, '2012-06-28 04:47:20'),
(281, 1, 0, '2012-06-28 19:49:12'),
(282, 1, 1, '2012-06-29 05:15:15'),
(283, 1, 1, '2012-07-02 09:37:26'),
(284, 1, 0, '2012-07-02 19:31:28'),
(285, 1, 1, '2012-07-04 04:41:44'),
(286, 1, 0, '2012-07-04 18:55:49'),
(287, 1, 1, '2012-07-05 04:40:21'),
(288, 1, 1, '2012-07-05 14:12:26'),
(289, 1, 0, '2012-07-05 15:13:51'),
(290, 1, 1, '2012-07-16 11:16:05'),
(291, 1, 1, '2012-07-17 14:42:13'),
(292, 1, 1, '2012-07-18 06:24:37'),
(293, 1, 0, '2012-07-18 06:27:58'),
(294, 1, 1, '2012-07-18 11:13:17'),
(295, 1, 1, '2012-07-18 12:12:36'),
(296, 1, 0, '2012-07-18 13:15:14'),
(297, 1, 1, '2012-07-19 10:52:35'),
(298, 1, 1, '2012-07-21 16:16:09'),
(299, 1, 0, '2012-07-21 19:07:24'),
(300, 1, 1, '2012-07-22 04:54:18'),
(301, 1, 1, '2012-07-23 04:51:42'),
(302, 1, 1, '2012-07-23 18:39:10'),
(303, 1, 1, '2012-07-24 06:31:32'),
(304, 1, 1, '2012-07-24 07:41:45'),
(305, 1, 1, '2012-07-24 16:36:04'),
(306, 1, 1, '2012-07-25 04:32:06'),
(307, 1, 1, '2012-07-25 04:46:49'),
(308, 1, 0, '2012-07-25 04:58:17'),
(309, 1, 0, '2012-07-25 05:03:57'),
(310, 1, 1, '2012-07-25 05:04:29'),
(311, 1, 1, '2012-07-25 05:48:34'),
(312, 1, 1, '2012-07-25 06:46:25'),
(313, 1, 0, '2012-07-25 08:55:20'),
(314, 1, 1, '2012-07-25 08:56:59'),
(315, 1, 0, '2012-07-25 08:57:00'),
(316, 1, 1, '2012-07-25 08:57:14'),
(317, 1, 0, '2012-07-25 08:57:16'),
(318, 1, 1, '2012-07-25 08:57:39'),
(319, 1, 0, '2012-07-25 08:57:40'),
(320, 1, 1, '2012-07-25 08:58:12'),
(321, 1, 0, '2012-07-25 08:58:13'),
(322, 1, 1, '2012-07-25 09:00:07'),
(323, 1, 0, '2012-07-25 09:00:08'),
(324, 1, 1, '2012-07-25 09:00:58'),
(325, 1, 0, '2012-07-25 09:00:59'),
(326, 1, 1, '2012-07-25 09:05:16'),
(327, 1, 0, '2012-07-25 09:05:18'),
(328, 1, 1, '2012-07-25 09:06:15'),
(329, 1, 0, '2012-07-25 09:06:59'),
(330, 1, 1, '2012-07-25 09:07:05'),
(331, 1, 0, '2012-07-25 09:07:18'),
(332, 1, 1, '2012-07-25 09:07:23'),
(333, 1, 0, '2012-07-25 09:07:54'),
(334, 1, 1, '2012-07-25 09:08:03'),
(335, 1, 0, '2012-07-25 09:08:10'),
(336, 1, 1, '2012-07-25 09:08:45'),
(337, 1, 0, '2012-07-25 09:11:54'),
(338, 1, 1, '2012-07-25 09:12:11'),
(339, 1, 0, '2012-07-25 09:12:25'),
(340, 1, 1, '2012-07-25 09:12:53'),
(341, 1, 0, '2012-07-25 09:15:49'),
(342, 1, 1, '2012-07-25 09:15:55'),
(343, 1, 0, '2012-07-25 09:15:56'),
(344, 1, 1, '2012-07-25 09:17:10'),
(345, 1, 0, '2012-07-25 09:17:11'),
(346, 1, 1, '2012-07-25 09:18:03'),
(347, 1, 0, '2012-07-25 09:18:04'),
(348, 1, 1, '2012-07-25 09:24:38'),
(349, 1, 0, '2012-07-25 09:25:04'),
(350, 1, 1, '2012-07-25 09:25:09'),
(351, 1, 0, '2012-07-25 09:25:28'),
(352, 1, 1, '2012-07-25 09:25:33'),
(353, 1, 0, '2012-07-25 09:25:51'),
(354, 1, 1, '2012-07-25 09:25:57'),
(355, 1, 0, '2012-07-25 09:26:29'),
(356, 1, 1, '2012-07-25 09:26:34'),
(357, 1, 0, '2012-07-25 09:27:31'),
(358, 1, 1, '2012-07-25 09:27:36'),
(359, 1, 0, '2012-07-25 09:27:59'),
(360, 1, 1, '2012-07-25 09:28:04'),
(361, 1, 0, '2012-07-25 09:28:16'),
(362, 1, 1, '2012-07-25 09:28:22'),
(363, 1, 0, '2012-07-25 09:28:23'),
(364, 1, 1, '2012-07-25 09:35:27'),
(365, 1, 0, '2012-07-25 09:35:28'),
(366, 1, 1, '2012-07-25 09:47:19'),
(367, 1, 1, '2012-07-25 10:09:45'),
(368, 1, 0, '2012-07-25 10:26:13'),
(369, 1, 1, '2012-07-25 10:27:02'),
(370, 1, 1, '2012-07-25 12:15:59'),
(371, 0, 0, '2012-07-25 13:10:29'),
(372, 1, 1, '2012-07-25 13:10:40'),
(373, 1, 1, '2012-07-26 04:51:49'),
(374, 1, 1, '2012-07-26 09:45:42'),
(375, 1, 0, '2012-07-26 14:44:16'),
(376, 1, 1, '2012-07-26 14:44:27'),
(377, 1, 0, '2012-07-26 14:45:11'),
(378, 1, 1, '2012-07-26 14:45:19'),
(379, 1, 0, '2012-07-26 16:32:46'),
(380, 1, 1, '2012-07-26 18:53:21'),
(381, 1, 1, '2012-07-29 12:52:37'),
(382, 1, 1, '2012-07-29 19:59:12'),
(383, 1, 1, '2012-07-30 04:05:57'),
(384, 1, 1, '2012-07-30 10:26:56'),
(385, 1, 1, '2012-08-01 04:13:05'),
(386, 1, 0, '2012-08-01 04:15:12'),
(387, 1, 1, '2012-08-23 05:38:30'),
(388, 1, 1, '2012-08-23 18:34:52'),
(389, 1, 0, '2012-08-23 18:56:25'),
(390, 1, 1, '2012-08-23 18:56:44'),
(391, 1, 1, '2012-08-24 05:15:15'),
(392, 1, 1, '2012-08-24 05:21:02'),
(393, 1, 1, '2012-08-24 07:13:40'),
(394, 1, 1, '2012-08-25 04:34:48'),
(395, 1, 1, '2012-08-26 04:14:17'),
(396, 3, 1, '2012-08-26 06:01:56'),
(397, 3, 0, '2012-08-26 06:06:24'),
(398, 3, 1, '2012-08-26 09:29:10'),
(399, 3, 0, '2012-08-26 10:18:31'),
(400, 1, 0, '2012-08-26 11:56:41'),
(401, 1, 1, '2012-08-27 04:52:52'),
(402, 1, 1, '2012-08-27 06:18:36'),
(403, 1, 1, '2012-08-28 09:40:48'),
(404, 1, 0, '2012-08-28 14:49:11'),
(405, 1, 1, '2012-08-30 04:42:33'),
(406, 1, 1, '2012-08-31 06:58:20'),
(407, 1, 0, '2012-08-31 18:49:29'),
(408, 1, 1, '2012-09-01 07:13:22'),
(409, 1, 0, '2012-09-02 17:39:12'),
(410, 1, 1, '2012-09-03 04:03:35'),
(411, 1, 0, '2012-09-04 14:27:35'),
(412, 1, 1, '2012-09-04 15:47:32'),
(413, 1, 0, '2012-09-04 19:18:06'),
(414, 1, 1, '2012-09-05 04:07:51'),
(415, 1, 0, '2012-09-05 12:49:51'),
(416, 1, 1, '2012-09-06 05:12:31'),
(417, 1, 0, '2012-09-06 06:13:41'),
(418, 1, 1, '2012-09-06 10:33:12'),
(419, 1, 0, '2012-09-06 13:11:41'),
(420, 1, 1, '2012-09-07 06:29:20'),
(421, 1, 0, '2012-09-07 10:11:42'),
(422, 1, 1, '2012-09-07 10:11:50'),
(423, 1, 0, '2012-09-07 10:22:25'),
(424, 1, 1, '2012-09-07 10:22:34'),
(425, 1, 0, '2012-09-07 10:39:55'),
(426, 1, 1, '2012-09-07 10:40:02'),
(427, 1, 0, '2012-09-07 12:27:50'),
(428, 1, 1, '2012-09-07 12:28:00'),
(429, 1, 0, '2012-09-07 12:46:52'),
(430, 1, 1, '2012-09-07 12:46:59'),
(431, 1, 0, '2012-09-07 12:49:39'),
(432, 1, 1, '2012-09-07 12:49:46'),
(433, 1, 0, '2012-09-07 13:20:56'),
(434, 1, 1, '2012-09-07 13:21:02'),
(435, 1, 0, '2012-09-07 13:23:19'),
(436, 1, 1, '2012-09-07 13:23:25'),
(437, 1, 0, '2012-09-07 15:20:47'),
(438, 1, 1, '2012-09-08 04:14:28'),
(439, 1, 0, '2012-09-08 04:16:30'),
(440, 1, 1, '2012-09-08 04:16:42'),
(441, 1, 0, '2012-09-08 04:21:07'),
(442, 1, 1, '2012-09-08 04:21:27'),
(443, 1, 0, '2012-09-08 04:24:02'),
(444, 1, 1, '2012-09-08 04:24:40'),
(445, 1, 0, '2012-09-08 06:07:16'),
(446, 1, 1, '2012-09-08 09:13:14'),
(447, 1, 0, '2012-09-08 09:16:49'),
(448, 1, 1, '2012-09-08 09:17:09'),
(449, 1, 0, '2012-09-08 09:17:54'),
(450, 1, 1, '2012-09-08 17:38:08'),
(451, 1, 0, '2012-09-08 19:45:38'),
(452, 1, 1, '2012-09-09 04:41:47'),
(453, 1, 0, '2012-09-09 10:32:16'),
(454, 1, 1, '2012-09-10 13:05:16'),
(455, 1, 0, '2012-09-10 13:05:33'),
(456, 1, 1, '2012-09-10 17:05:12'),
(457, 1, 0, '2012-09-10 18:57:35'),
(458, 1, 1, '2012-09-11 04:59:46'),
(459, 1, 0, '2012-09-11 08:30:00'),
(460, 1, 1, '2012-09-12 06:06:52'),
(461, 1, 0, '2012-09-12 10:48:08'),
(462, 1, 1, '2012-09-12 19:02:58'),
(463, 1, 0, '2012-09-12 19:04:23'),
(464, 1, 1, '2012-09-13 05:21:25'),
(465, 1, 1, '2012-09-13 07:17:49'),
(466, 1, 1, '2012-09-13 18:36:27'),
(467, 1, 0, '2012-09-13 19:08:23'),
(468, 1, 1, '2012-09-14 04:24:37'),
(469, 1, 0, '2012-09-14 20:16:52'),
(470, 1, 1, '2012-09-15 06:02:02'),
(471, 1, 0, '2012-09-15 09:51:28'),
(472, 1, 1, '2012-09-15 12:33:59'),
(473, 1, 0, '2012-09-15 12:37:57'),
(474, 1, 1, '2012-09-16 11:36:33'),
(475, 1, 0, '2012-09-16 11:36:50'),
(476, 1, 1, '2012-09-17 07:03:37'),
(477, 1, 0, '2012-09-17 11:13:37'),
(478, 1, 1, '2012-09-17 11:15:42'),
(479, 1, 0, '2012-09-17 13:20:15'),
(480, 1, 1, '2012-09-18 14:41:21'),
(481, 1, 0, '2012-09-18 14:43:02'),
(482, 1, 1, '2012-09-19 11:22:52'),
(483, 1, 0, '2012-09-19 11:24:20'),
(484, 1, 1, '2012-09-24 12:15:14'),
(485, 1, 0, '2012-09-24 12:44:52'),
(486, 1, 1, '2012-09-25 09:56:52'),
(487, 1, 0, '2012-09-25 10:04:58'),
(488, 1, 1, '2012-09-29 05:56:28'),
(489, 1, 1, '2012-09-29 11:03:31'),
(490, 1, 1, '2012-09-29 14:54:27'),
(491, 1, 1, '2012-09-29 16:44:56'),
(492, 1, 1, '2012-09-30 07:12:32'),
(493, 1, 0, '2012-10-01 05:48:01'),
(494, 1, 1, '2012-10-01 05:48:08'),
(495, 1, 1, '2012-10-01 06:19:26'),
(496, 1, 0, '2012-10-01 17:49:42'),
(497, 1, 1, '2012-10-01 17:49:52'),
(498, 1, 0, '2012-10-01 19:24:40'),
(499, 1, 1, '2012-10-02 04:44:03'),
(500, 1, 1, '2012-10-02 07:46:25'),
(501, 1, 1, '2012-10-03 04:12:48'),
(502, 1, 0, '2012-10-03 17:47:59'),
(503, 1, 1, '2012-10-03 18:14:25'),
(504, 1, 0, '2012-10-03 18:25:01'),
(505, 1, 1, '2012-10-04 05:06:15'),
(506, 1, 1, '2012-10-04 07:17:14'),
(507, 1, 0, '2012-10-04 07:17:37'),
(508, 1, 0, '2012-10-04 08:44:04'),
(509, 1, 1, '2012-10-05 13:39:33'),
(510, 1, 0, '2012-10-05 19:42:59'),
(511, 1, 1, '2012-10-08 04:00:02'),
(512, 1, 0, '2012-10-08 19:02:00'),
(513, 1, 1, '2012-10-09 03:58:45'),
(514, 1, 1, '2012-10-09 17:34:35'),
(515, 1, 0, '2012-10-09 19:21:28'),
(516, 1, 0, '2012-10-10 06:12:41'),
(517, 1, 1, '2012-10-10 06:12:48'),
(518, 1, 1, '2012-10-10 07:46:59'),
(519, 1, 0, '2012-10-10 08:03:23'),
(520, 1, 1, '2012-10-10 08:13:53'),
(521, 1, 0, '2012-10-10 15:11:59'),
(522, 1, 1, '2012-10-10 15:14:13'),
(523, 1, 1, '2012-10-10 16:13:58'),
(524, 1, 0, '2012-10-10 17:31:43'),
(525, 1, 1, '2012-10-10 17:43:50'),
(526, 1, 0, '2012-10-10 19:20:42'),
(527, 1, 1, '2012-10-11 08:15:19'),
(528, 1, 1, '2012-10-11 15:56:13'),
(529, 1, 0, '2012-10-11 15:59:05'),
(530, 1, 1, '2012-10-12 05:30:47'),
(531, 1, 0, '2012-10-12 09:35:50'),
(532, 1, 1, '2012-10-15 07:47:30'),
(533, 1, 1, '2012-10-15 10:42:15'),
(534, 1, 1, '2012-10-16 04:35:36'),
(535, 1, 1, '2012-10-18 05:40:35'),
(536, 1, 1, '2012-10-20 11:44:32'),
(537, 1, 1, '2012-10-23 14:50:39');

-- --------------------------------------------------------

--
-- Структура таблицы `users_group`
--

CREATE TABLE IF NOT EXISTS `users_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `users_group`
--

INSERT INTO `users_group` (`id`, `name`) VALUES
(1, 'OTMS'),
(2, 'Тест');

-- --------------------------------------------------------

--
-- Структура таблицы `users_mail`
--

CREATE TABLE IF NOT EXISTS `users_mail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(4) NOT NULL,
  `uid` int(11) NOT NULL,
  `email` varchar(64) NOT NULL,
  `server` varchar(128) NOT NULL,
  `protocol` varchar(8) NOT NULL,
  `port` int(11) NOT NULL,
  `login` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `ssl` varchar(16) NOT NULL,
  `default` tinyint(4) NOT NULL DEFAULT '0',
  `clear` tinyint(4) NOT NULL DEFAULT '1',
  `clear_days` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`,`uid`,`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Дамп данных таблицы `users_mail`
--

INSERT INTO `users_mail` (`id`, `type`, `uid`, `email`, `server`, `protocol`, `port`, `login`, `password`, `ssl`, `default`, `clear`, `clear_days`) VALUES
(1, 'in', 1, 'info@tushkan.com', 'tushkan.com', 'IMAP', 143, 'info@tushkan.com', 'bPSsb32L3PvRvyjw', 'notls', 1, 1, 0),
(2, 'out', 1, 'info@tushkan.com', 'tushkan.com', 'SMTP', 25, 'info@tushkan.com', 'bPSsb32L3PvRvyjw', 'notls', 1, 1, 0),
(3, 'in', 1, 'bugs@otms-project.ru', 'otms-project.ru', 'IMAP', 143, 'bugs@otms-project.ru', 'Jcbj8G7vJcsUEKrZ', 'notls', 0, 1, 0),
(4, 'out', 1, 'bugs@otms-project.ru', 'otms-project.ru', 'SMTP', 25, 'bugs@otms-project.ru', 'Jcbj8G7vJcsUEKrZ', 'notls', 0, 1, 0),
(5, 'in', 1, 'info@otms-project.ru', 'otms-project.ru', 'IMAP', 143, 'info@otms-project.ru', 'qLNtxHq3pt9AXp9H', 'notls', 0, 1, 0),
(6, 'out', 1, 'info@otms-project.ru', 'otms-project.ru', 'SMTP', 25, 'info@otms-project.ru', 'qLNtxHq3pt9AXp9H', 'notls', 0, 1, 0),
(7, 'in', 1, 'pay@otms-project.ru', 'otms-project.ru', 'IMAP', 143, 'pay@otms-project.ru', 'VDvAJUHKJVFJp4Ft', 'notls', 0, 1, 0),
(8, 'out', 1, 'pay@otms-project.ru', 'otms-project.ru', 'SMTP', 25, 'pay@otms-project.ru', 'VDvAJUHKJVFJp4Ft', 'notls', 0, 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `users_priv`
--

CREATE TABLE IF NOT EXISTS `users_priv` (
  `id` int(11) NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `group` smallint(6) NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users_priv`
--

INSERT INTO `users_priv` (`id`, `admin`, `group`) VALUES
(1, 1, 1),
(2, 0, 2),
(3, 0, 3),
(4, 0, 4),
(5, 0, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `users_sets`
--

CREATE TABLE IF NOT EXISTS `users_sets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `key` varchar(32) NOT NULL,
  `val` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

--
-- Дамп данных таблицы `users_sets`
--

INSERT INTO `users_sets` (`id`, `uid`, `key`, `val`) VALUES
(31, 1, 'ajax_notice', '{"task":"0","com":"0","mail":"0","obj":"0","info":"0"}'),
(32, 1, 'bu', '{"gr":{"\\u0422\\u0435\\u0441\\u0442":"1"},"sub":{"\\u041c\\u0435\\u043d\\u0435\\u0434\\u0436\\u0435\\u0440\\u044b":"0","\\u0414\\u0438\\u0440\\u0435\\u043a\\u0442\\u043e\\u0440\\u0430":"1"}}');

-- --------------------------------------------------------

--
-- Структура таблицы `users_subgroup`
--

CREATE TABLE IF NOT EXISTS `users_subgroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `users_subgroup`
--

INSERT INTO `users_subgroup` (`id`, `pid`, `name`) VALUES
(1, 1, 'Администратор'),
(2, 2, 'Секретари'),
(3, 2, 'Менеджеры'),
(4, 2, 'Директора');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
