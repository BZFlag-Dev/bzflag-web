-- MySQL dump 10.11
--
-- Host: localhost    Database: bzstats
-- ------------------------------------------------------
-- Server version	5.0.75

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
-- Table structure for table `current_players`
--

DROP TABLE IF EXISTS `current_players`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `current_players` (
  `ID` bigint(20) NOT NULL auto_increment,
  `ServerID` bigint(20) default NULL,
  `BZID` varchar(32) default NULL,
  `callsign` varchar(32) default NULL,
  `last_update` datetime default NULL,
  `client` tinytext,
  `team` tinyint(4) default NULL,
  `token` int(11) default NULL,
  `wins` int(11) default NULL,
  `losses` int(11) default NULL,
  `teamkills` int(11) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `current_players`
--

LOCK TABLES `current_players` WRITE;
/*!40000 ALTER TABLE `current_players` DISABLE KEYS */;
/*!40000 ALTER TABLE `current_players` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `current_servers`
--

DROP TABLE IF EXISTS `current_servers`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `current_servers` (
  `ID` bigint(20) NOT NULL auto_increment,
  `host` varchar(32) default NULL,
  `last_update` datetime default NULL,
  `mode` tinyint(4) default NULL,
  `red_wins` int(11) default NULL,
  `red_losses` int(11) default NULL,
  `red_score` int(11) default NULL,
  `green_wins` int(11) default NULL,
  `green_losses` int(11) default NULL,
  `green_score` int(11) default NULL,
  `blue_wins` int(11) default NULL,
  `blue_losses` int(11) default NULL,
  `blue_score` int(11) default NULL,
  `purple_wins` int(11) default NULL,
  `purple_losses` int(11) default NULL,
  `purple_score` int(11) default NULL,
  `map` varchar(80) default NULL,
  `description` varchar(128) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `current_servers`
--

LOCK TABLES `current_servers` WRITE;
/*!40000 ALTER TABLE `current_servers` DISABLE KEYS */;
/*!40000 ALTER TABLE `current_servers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `log` (
  `ID` bigint(20) NOT NULL auto_increment,
  `host` varchar(32) NOT NULL,
  `name` varchar(100) NOT NULL,
  `hash` varchar(50) NOT NULL,
  `action` varchar(10) NOT NULL,
  `gameinfo` varchar(255) NOT NULL,
  `playerinfo` varchar(512) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
/*!40000 ALTER TABLE `log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'bzstats'
--
DELIMITER ;;
DELIMITER ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-07-15  5:42:12
