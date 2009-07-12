-- phpMyAdmin SQL Dump
-- version 3.1.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 12, 2009 at 11:52 AM
-- Server version: 5.1.33
-- PHP Version: 5.2.9-2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `bzstats`
--

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `host` varchar(32) NOT NULL,
  `name` varchar(100) NOT NULL,
  `hash` varchar(50) NOT NULL,
  `action` varchar(10) NOT NULL,
  `gameinfo` varchar(255) NOT NULL,
  `playerinfo` varchar(512) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;
