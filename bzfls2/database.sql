-- 
-- Table structure for table `players`
-- 

CREATE TABLE `players` (
  `playerid` mediumint(8) unsigned NOT NULL auto_increment,
  `username` varchar(25) collate latin1_general_ci NOT NULL,
  `password` varchar(32) collate latin1_general_ci NOT NULL,
  `email` varchar(255) collate latin1_general_ci NOT NULL,
  `created` datetime NOT NULL,
  `createdipaddress` varchar(15) collate latin1_general_ci NOT NULL,
  `lastaccess` datetime NOT NULL,
  `lastaccessipaddress` varchar(15) collate latin1_general_ci NOT NULL,
  `newpassword` varchar(32) collate latin1_general_ci NOT NULL,
  `newemail` varchar(255) collate latin1_general_ci NOT NULL,
  `activationkey` varchar(32) collate latin1_general_ci NOT NULL,
  `activated` tinyint(1) NOT NULL,
  `token` varchar(40) collate latin1_general_ci NOT NULL,
  `tokendate` datetime NOT NULL,
  `tokenipaddress` varchar(15) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`playerid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ;

-- --------------------------------------------------------

-- 
-- Table structure for table `servers`
-- 

CREATE TABLE `servers` (
  `serverid` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(64) collate latin1_general_ci NOT NULL,
  `port` smallint(5) unsigned NOT NULL,
  `ipaddress` varchar(15) collate latin1_general_ci NOT NULL,
  `title` varchar(80) collate latin1_general_ci NOT NULL,
  `owner` mediumint(8) unsigned NOT NULL,
  `build` varchar(64) collate latin1_general_ci NOT NULL,
  `version` varchar(9) collate latin1_general_ci NOT NULL,
  `gameinfo` varchar(128) collate latin1_general_ci NOT NULL,
  `lastmodified` datetime NOT NULL,
  PRIMARY KEY  (`serverid`),
  KEY `version` (`version`),
  KEY `lastmodified` (`lastmodified`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ;
