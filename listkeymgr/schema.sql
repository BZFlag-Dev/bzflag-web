-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 19, 2009 at 05:15 AM
-- Server version: 5.1.36
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `listauth`
--

-- --------------------------------------------------------

--
-- Table structure for table `authkeys`
--

CREATE TABLE IF NOT EXISTS `authkeys` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `key_string` tinytext NOT NULL,
  `owner` tinytext NOT NULL,
  `host` tinytext NOT NULL,
  `edit_date` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;
