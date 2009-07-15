-- phpMyAdmin SQL Dump
-- version 3.1.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 15, 2009 at 01:44 PM
-- Server version: 5.0.27
-- PHP Version: 5.2.9-2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `bzstats`
--

-- --------------------------------------------------------

--
-- Table structure for table `current_players`
--

CREATE TABLE IF NOT EXISTS `current_players` (
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `current_servers`
--

CREATE TABLE IF NOT EXISTS `current_servers` (
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
  `hash` varchar(50) default NULL,
  `lastHeartbeat` datetime default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `ID` bigint(20) NOT NULL auto_increment,
  `host` varchar(32) NOT NULL,
  `name` varchar(100) NOT NULL,
  `hash` varchar(50) NOT NULL,
  `action` varchar(10) NOT NULL,
  `gameinfo` varchar(255) NOT NULL,
  `playerinfo` varchar(512) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;
