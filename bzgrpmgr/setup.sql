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
-- Table structure for table `groups_orgs`
--

DROP TABLE IF EXISTS `groups_orgs`;
CREATE TABLE `groups_orgs` (
  `orgid` int(2) NOT NULL auto_increment,
  `orgname` varchar(25) NOT NULL,
  `contact` int(2) NOT NULL,
  PRIMARY KEY  (`orgid`),
  UNIQUE KEY `orgname` (`orgname`)
) ENGINE=MyISAM AUTO_INCREMENT=1534 DEFAULT CHARSET=latin1;

--
-- Table structure for table `groups_groups`
--

DROP TABLE IF EXISTS `groups_groups`;
CREATE TABLE `groups_groups` (
  `groupid` int(2) unsigned NOT NULL auto_increment,
  `groupname` varchar(25) NOT NULL,
  `orgid` int(2) unsigned NOT NULL,
  `state` enum('open','closed','hidden','inactive') NOT NULL default 'inactive',
  `freeleave` tinyint(1) NOT NULL default '0',
  `description` varchar(128) default NULL,
  PRIMARY KEY  (`groupid`)
) ENGINE=MyISAM AUTO_INCREMENT=12244 DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM AUTO_INCREMENT=93684 DEFAULT CHARSET=latin1;

--
-- Table structure for table `groups_permissions`
--

DROP TABLE IF EXISTS `groups_permissions`;
CREATE TABLE `groups_permissions` (
  `id` int(2) NOT NULL auto_increment,
  `groupid` int(2) NOT NULL,
  `groupadmin` tinyint(1) NOT NULL default '0',
  `grouptarget` int(2) default NULL,
  `orgadmin` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=951 DEFAULT CHARSET=latin1;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2008-01-06  9:20:14
