# phpMyAdmin MySQL-Dump
# version 2.5.1
# http://www.phpmyadmin.net/ (download page)
#
# Host: localhost
# Generation Time: Mar 16, 2004 at 06:28 PM
# Server version: 4.0.18
# PHP Version: 4.3.4
# Database : `bzflag`
# --------------------------------------------------------

#
# Table structure for table `currentplayers`
#
# Creation: Dec 15, 2003 at 05:27 PM
# Last update: Mar 16, 2004 at 06:28 PM
#

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
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=4924373 ;
# --------------------------------------------------------

#
# Table structure for table `miscinfo`
#
# Creation: Dec 01, 2003 at 09:52 PM
# Last update: Mar 16, 2004 at 06:14 PM
#

CREATE TABLE `miscinfo` (
  `lastrefreshed` int(11) NOT NULL default '0'
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `playerinfo`
#
# Creation: Dec 15, 2003 at 05:28 PM
# Last update: Mar 16, 2004 at 06:28 PM
#

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
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=50600 ;
# --------------------------------------------------------

#
# Table structure for table `serverinfo`
#
# Creation: Dec 15, 2003 at 05:41 PM
# Last update: Mar 16, 2004 at 06:28 PM
#

CREATE TABLE `serverinfo` (
  `id` int(11) NOT NULL auto_increment,
  `server` varchar(255) NOT NULL default '',
  `players` int(11) NOT NULL default '0',
  `ctf` enum('Y','N') NOT NULL default 'N',
  `superflags` enum('Y','N') NOT NULL default 'N',
  `rogues` enum('Y','N') NOT NULL default 'N',
  `jumping` enum('Y','N') NOT NULL default 'N',
  `inertia` enum('Y','N') NOT NULL default 'N',
  `ricochet` enum('Y','N') NOT NULL default 'N',
  `shakable` enum('Y','N') NOT NULL default 'N',
  `antidoteflags` enum('Y','N') NOT NULL default 'N',
  `timesync` enum('Y','N') NOT NULL default 'N',
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
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=2200917 ;
# --------------------------------------------------------

#
# Table structure for table `teaminfo`
#
# Creation: Nov 12, 2003 at 09:45 PM
# Last update: Mar 16, 2004 at 06:27 PM
#

CREATE TABLE `teaminfo` (
  `id` int(11) NOT NULL auto_increment,
  `server` varchar(255) NOT NULL default '',
  `team` enum('X','R','G','B','P') NOT NULL default 'X',
  `size` int(11) NOT NULL default '0',
  `score` int(11) NOT NULL default '0',
  `won` int(11) NOT NULL default '0',
  `lost` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=3126248 ;

