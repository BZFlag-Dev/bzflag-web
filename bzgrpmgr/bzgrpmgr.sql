-- 
-- Table structure for table `groups`
-- 

CREATE TABLE `groups` (
  `groupid` mediumint(8) NOT NULL auto_increment,
  `groupname` varchar(25) NOT NULL,
  `ownerid` mediumint(8) NOT NULL,
  `created` datetime NOT NULL,
  `createdipaddress` varchar(15) NOT NULL,
  PRIMARY KEY  (`groupid`),
  UNIQUE KEY `groupname` (`groupname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `group_members`
-- 

CREATE TABLE `group_members` (
  `id` int(2) NOT NULL auto_increment,
  `playerid` int(2) NOT NULL,
  `groupid` int(2) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  `lastupdate` datetime NOT NULL,
  `lastlogin` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
