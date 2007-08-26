--
-- Table structure for table `group_members`
--

DROP TABLE IF EXISTS `group_members`;
CREATE TABLE `group_members` (
  `id` int(2) NOT NULL auto_increment,
  `playerid` int(2) NOT NULL,
  `groupid` int(2) NOT NULL,
  `lastupdate` datetime NOT NULL,
  `lastlogin` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `groupid` int(2) unsigned NOT NULL auto_increment,
  `groupname` varchar(25) NOT NULL,
  `ownergroup` int(2) unsigned NOT NULL,
  `created` datetime NOT NULL,
  `createdipaddress` varchar(15) NOT NULL,
  PRIMARY KEY  (`groupid`),
  UNIQUE KEY `groupname` (`groupname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
