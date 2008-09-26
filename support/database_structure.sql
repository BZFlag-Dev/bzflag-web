-- MySQL dump 9.11
--
-- Host: localhost    Database: bzflaglsdb
-- ------------------------------------------------------
-- Server version	4.0.23_Debian-4-log

--
-- Table structure for table `currentplayers`
--

CREATE TABLE `currentplayers` (
  `id` int(11) NOT NULL auto_increment,
  `callsign` varchar(255) NOT NULL default '',
  `email` varchar(128) NOT NULL default '',
  `server` varchar(255) NOT NULL default '',
  `score` int(11) NOT NULL default '0',
  `tks` int(11) NOT NULL default '0',
  `strengthindex` float NOT NULL default '0',
  `killratio` float NOT NULL default '0',
  `team` enum('X','R','G','B','P','O','H') NOT NULL default 'X',
  PRIMARY KEY  (`id`),
  KEY `server` (`server`)
) TYPE=MyISAM;

--
-- Table structure for table `groupmembers`
--

CREATE TABLE `groupmembers` (
  `playerid` int(10) unsigned NOT NULL default '0',
  `groupid` int(10) unsigned NOT NULL default '0',
  `adddate` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`playerid`,`groupid`),
  KEY `playerid` (`playerid`),
  KEY `groupid` (`groupid`)
) TYPE=MyISAM COMMENT='many to many link - players to groups';

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `groupid` int(10) unsigned NOT NULL auto_increment,
  `groupname` varchar(32) NOT NULL default '',
  `playerid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`groupid`),
  UNIQUE KEY `groupname` (`groupname`),
  KEY `playerid` (`playerid`)
) TYPE=MyISAM COMMENT='names of central groups - playerid is the owner/editor';

--
-- Table structure for table `miscinfo`
--

CREATE TABLE `miscinfo` (
  `lastrefreshed` int(11) NOT NULL default '0'
) TYPE=MyISAM;

--
-- Table structure for table `playerinfo`
--

CREATE TABLE `playerinfo` (
  `id` int(11) NOT NULL auto_increment,
  `callsign` varchar(32) NOT NULL default '',
  `ip` varchar(15) NOT NULL default '',
  `email` varchar(128) NOT NULL default '',
  `highscore` int(11) NOT NULL default '0',
  `lowscore` int(11) NOT NULL default '0',
  `mosttks` int(11) NOT NULL default '0',
  `leasttks` int(11) NOT NULL default '0',
  `highstrengthindex` float NOT NULL default '0',
  `lowstrengthindex` float NOT NULL default '0',
  `highkillratio` float NOT NULL default '0',
  `lowkillratio` float NOT NULL default '0',
  `mostwins` int(11) NOT NULL default '0',
  `mostlosses` int(11) NOT NULL default '0',
  `lastseen` int(11) NOT NULL default '0',
  `lastserver` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `callsign` (`callsign`),
  KEY `highscore` (`highscore`,`lowscore`,`highstrengthindex`,`lowstrengthindex`),
  KEY `lowstrengthindex` (`lowstrengthindex`),
  KEY `highstrengthindex` (`highstrengthindex`),
  KEY `lowscore` (`lowscore`),
  KEY `callsign_2` (`callsign`,`lastserver`)
) TYPE=MyISAM;

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `playerid` int(10) unsigned NOT NULL auto_increment,
  `created` int(10) unsigned NOT NULL default '0',
  `email` varchar(64) NOT NULL default '',
  `callsign` varchar(32) NOT NULL default '',
  `password` varchar(16) NOT NULL default '',
  `lastmod` int(10) unsigned NOT NULL default '0',
  `randtext` varchar(8) default NULL,
  `token` int(10) unsigned NOT NULL default '0',
  `tokendate` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`playerid`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `callsign` (`callsign`)
) TYPE=MyISAM;

--
-- Table structure for table `server_advert_groups`
--

CREATE TABLE `server_advert_groups` (
  `server_id` int(11) default NULL,
  `group_id` int(11) default NULL,
  KEY `group_id` (`group_id`),
  KEY `server_id` (`server_id`)
) TYPE=MyISAM;

--
-- Table structure for table `serverbans`
--

CREATE TABLE `serverbans` (
  `banid` int(2) NOT NULL auto_increment,
  `type` varchar(32) NOT NULL,
  `address` varchar(64) NOT NULL,
  `owner` varchar(64) default NULL,
  `reason` varchar(128) default NULL,
  `lastby` int(2) NOT NULL,
  `active` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`banid`)
) TYPE=MyISAM;

--
-- Table structure for table `serverinfo`
--

CREATE TABLE `serverinfo` (
  `id` int(11) NOT NULL auto_increment,
  `server` varchar(255) NOT NULL default '',
  `players` int(11) NOT NULL default '0',
  `ctf` enum('Y','N') NOT NULL default 'N',
  `superflags` enum('Y','N') NOT NULL default 'N',
  `jumping` enum('Y','N') NOT NULL default 'N',
  `inertia` enum('Y','N') NOT NULL default 'N',
  `ricochet` enum('Y','N') NOT NULL default 'N',
  `shakable` enum('Y','N') NOT NULL default 'N',
  `antidoteflags` enum('Y','N') NOT NULL default 'N',
  `handicap` enum('Y','N') NOT NULL default 'N',
  `rabbitchase` enum('Y','N') NOT NULL default 'N',
  `maxshots` int(11) NOT NULL default '0',
  `shakewins` int(11) NOT NULL default '0',
  `shaketimeout` int(11) NOT NULL default '0',
  `maxplayerscore` int(11) NOT NULL default '0',
  `maxteamscore` int(11) NOT NULL default '0',
  `maxtime` int(11) NOT NULL default '0',
  `maxplayers` int(11) NOT NULL default '0',
  `roguesize` int(11) NOT NULL default '0',
  `roguemax` int(11) NOT NULL default '0',
  `redsize` int(11) NOT NULL default '0',
  `redmax` int(11) NOT NULL default '0',
  `greensize` int(11) NOT NULL default '0',
  `greenmax` int(11) NOT NULL default '0',
  `bluesize` int(11) NOT NULL default '0',
  `bluemax` int(11) NOT NULL default '0',
  `purplesize` int(11) NOT NULL default '0',
  `purplemax` int(11) NOT NULL default '0',
  `observersize` int(11) NOT NULL default '0',
  `observermax` int(11) NOT NULL default '0',
  `ip` text NOT NULL,
  `version` text NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `server` (`server`)
) TYPE=MyISAM;

--
-- Table structure for table `servers`
--

CREATE TABLE `servers` (
  `server_id` int(11) NOT NULL auto_increment,
  `players` int(11) NOT NULL default '0',
  `nameport` varchar(60) NOT NULL default '',
  `build` varchar(60) default NULL,
  `version` varchar(9) NOT NULL default '',
  `gameinfo` varchar(73) NOT NULL default '',
  `ipaddr` varchar(17) NOT NULL default '',
  `title` varchar(80) default NULL,
  `lastmod` int(11) NOT NULL default '0',
  `ctf` enum('Y','N') NOT NULL default 'N',
  `superflags` enum('Y','N') NOT NULL default 'N',
  `jumping` enum('Y','N') NOT NULL default 'N',
  `inertia` enum('Y','N') NOT NULL default 'N',
  `ricochet` enum('Y','N') NOT NULL default 'N',
  `shakable` enum('Y','N') NOT NULL default 'N',
  `antidoteflags` enum('Y','N') NOT NULL default 'N',
  `handicap` enum('Y','N') NOT NULL default 'N',
  `rabbitchase` enum('Y','N') NOT NULL default 'N',
  `maxshots` int(11) NOT NULL default '0',
  `shakewins` int(11) NOT NULL default '0',
  `shaketimeout` int(11) NOT NULL default '0',
  `maxplayerscore` int(11) NOT NULL default '0',
  `maxteamscore` int(11) NOT NULL default '0',
  `maxtime` int(11) NOT NULL default '0',
  `maxplayers` int(11) NOT NULL default '0',
  `roguesize` int(11) NOT NULL default '0',
  `roguemax` int(11) NOT NULL default '0',
  `redsize` int(11) NOT NULL default '0',
  `redmax` int(11) NOT NULL default '0',
  `greensize` int(11) NOT NULL default '0',
  `greenmax` int(11) NOT NULL default '0',
  `bluesize` int(11) NOT NULL default '0',
  `bluemax` int(11) NOT NULL default '0',
  `purplesize` int(11) NOT NULL default '0',
  `purplemax` int(11) NOT NULL default '0',
  `observersize` int(11) NOT NULL default '0',
  `observermax` int(11) NOT NULL default '0',
  PRIMARY KEY  (`server_id`),
  KEY `nameport` (`nameport`)
) TYPE=MyISAM;

--
-- Table structure for table `teaminfo`
--

CREATE TABLE `teaminfo` (
  `id` int(11) NOT NULL auto_increment,
  `server` varchar(255) NOT NULL default '',
  `team` enum('X','R','G','B','P') NOT NULL default 'X',
  `size` int(11) NOT NULL default '0',
  `score` int(11) NOT NULL default '0',
  `won` int(11) NOT NULL default '0',
  `lost` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `server` (`server`)
) TYPE=MyISAM;

