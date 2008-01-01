-- MySQL dump 10.11
--
-- Host: localhost    Database: bzgrpmgr
-- ------------------------------------------------------
-- Server version	5.0.45-Debian_1ubuntu3.1-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `groups_groups`
--

DROP TABLE IF EXISTS `groups_groups`;
CREATE TABLE `groups_groups` (
  `groupid` int(2) unsigned NOT NULL auto_increment,
  `groupname` varchar(25) NOT NULL,
  `orgid` int(2) unsigned NOT NULL,
  `state` enum('open','closed','hidden','inactive') NOT NULL default 'inactive',
  `adminlevel` tinyint(1) NOT NULL default '0',
  `can_manage` int(2) default NULL,
  `freeleave` tinyint(1) NOT NULL default '0',
  `description` varchar(128) default NULL,
  PRIMARY KEY  (`groupid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Table structure for table `groups_memberships`
--

DROP TABLE IF EXISTS `groups_memberships`;
CREATE TABLE `groups_memberships` (
  `id` int(2) NOT NULL auto_increment,
  `userid` int(2) NOT NULL,
  `groupid` int(2) NOT NULL,
  `pending` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `groups_orgs`
--

DROP TABLE IF EXISTS `groups_orgs`;
CREATE TABLE `groups_orgs` (
  `orgid` int(2) NOT NULL auto_increment,
  `orgname` varchar(25) NOT NULL,
  `contact` int(2) NOT NULL,
  PRIMARY KEY  (`orgid`),
  UNIQUE KEY `orgname` (`orgname`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Table structure for table `phpbb2_users`
--

DROP TABLE IF EXISTS `phpbb2_users`;
CREATE TABLE `phpbb2_users` (
  `user_id` mediumint(8) NOT NULL default '0',
  `user_active` tinyint(1) default '1',
  `username` varchar(25) NOT NULL default '',
  `user_password` varchar(32) NOT NULL default '',
  `user_session_time` int(11) NOT NULL default '0',
  `user_session_page` smallint(5) NOT NULL default '0',
  `user_lastvisit` int(11) NOT NULL default '0',
  `user_regdate` int(11) NOT NULL default '0',
  `user_level` tinyint(4) default NULL,
  `user_posts` mediumint(8) unsigned NOT NULL default '0',
  `user_timezone` decimal(5,2) NOT NULL default '0.00',
  `user_style` tinyint(4) default NULL,
  `user_lang` varchar(255) default NULL,
  `user_dateformat` varchar(14) NOT NULL default 'd M Y H:i',
  `user_new_privmsg` smallint(5) unsigned NOT NULL default '0',
  `user_unread_privmsg` smallint(5) unsigned NOT NULL default '0',
  `user_last_privmsg` int(11) NOT NULL default '0',
  `user_emailtime` int(11) default NULL,
  `user_viewemail` tinyint(1) default NULL,
  `user_attachsig` tinyint(1) default NULL,
  `user_allowhtml` tinyint(1) default '1',
  `user_allowbbcode` tinyint(1) default '1',
  `user_allowsmile` tinyint(1) default '1',
  `user_allowavatar` tinyint(1) NOT NULL default '1',
  `user_allow_pm` tinyint(1) NOT NULL default '1',
  `user_allow_posting` tinyint(1) NOT NULL default '1',
  `user_allow_viewonline` tinyint(1) NOT NULL default '1',
  `user_notify` tinyint(1) NOT NULL default '0',
  `user_notify_pm` tinyint(1) NOT NULL default '1',
  `user_popup_pm` tinyint(1) NOT NULL default '0',
  `user_rank` int(11) default NULL,
  `user_avatar` varchar(100) default NULL,
  `user_avatar_type` tinyint(4) NOT NULL default '0',
  `user_email` varchar(255) default NULL,
  `user_icq` varchar(15) default NULL,
  `user_website` varchar(100) default NULL,
  `user_from` varchar(100) default NULL,
  `user_sig` text,
  `user_sig_bbcode_uid` varchar(10) default NULL,
  `user_aim` varchar(255) default NULL,
  `user_yim` varchar(255) default NULL,
  `user_msnm` varchar(255) default NULL,
  `user_occ` varchar(100) default NULL,
  `user_interests` varchar(255) default NULL,
  `user_actkey` varchar(32) default NULL,
  `user_newpasswd` varchar(32) default NULL,
  `user_quickreply` tinyint(1) NOT NULL default '1',
  `user_token` int(10) unsigned NOT NULL default '0',
  `user_tokendate` int(10) unsigned NOT NULL default '0',
  `user_tokenip` varchar(40) default NULL,
  `user_login_tries` smallint(5) unsigned NOT NULL default '0',
  `user_last_login_try` int(11) NOT NULL default '0',
  `user_show_quickreply` tinyint(1) NOT NULL default '1',
  `user_quickreply_mode` tinyint(1) NOT NULL default '1',
  `user_open_quickreply` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_email` (`user_email`),
  KEY `user_session_time` (`user_session_time`),
  KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2007-12-30  1:39:36
