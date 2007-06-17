-- 
-- Table structure for table `servers`
-- 

CREATE TABLE `servers` (
  `serverid` int(10) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `port` smallint(5) unsigned NOT NULL,
  `ipaddress` varchar(15) NOT NULL,
  `title` varchar(80) NOT NULL,
  `owner` mediumint(8) unsigned NOT NULL,
  `build` varchar(64) NOT NULL,
  `version` varchar(9) NOT NULL,
  `gameinfo` varchar(128) NOT NULL,
  `lastmodified` datetime NOT NULL,
  PRIMARY KEY  (`serverid`),
  KEY `version` (`version`),
  KEY `lastmodified` (`lastmodified`)
) TYPE=MyISAM ;
