-- MySQL dump 8.21
--
-- Host: localhost    Database: bzflag
---------------------------------------------------------
-- Server version	3.23.49-log

--
-- Table structure for table 'currentplayers'
--

CREATE TABLE currentplayers (
  id int(11) NOT NULL auto_increment,
  callsign varchar(255) NOT NULL default '',
  email varchar(128) NOT NULL default '',
  server varchar(255) NOT NULL default '',
  score int(11) NOT NULL default '0',
  tks int(11) NOT NULL default '0',
  strengthindex float NOT NULL default '0',
  killratio float NOT NULL default '0',
  team enum('X','R','G','B','P','O','H') NOT NULL default 'X',
  PRIMARY KEY  (id),
  KEY server (server)
) TYPE=MyISAM;

--
-- Table structure for table 'karma'
--

CREATE TABLE karma (
  from int(11) NOT NULL default '0',
  to int(11) NOT NULL default '0',
  karma int(11) NOT NULL default '0',
  PRIMARY KEY  (from,to)
) TYPE=MyISAM;

--
-- Table structure for table 'miscinfo'
--

CREATE TABLE miscinfo (
  lastrefreshed int(11) NOT NULL default '0'
) TYPE=MyISAM;

--
-- Table structure for table 'playerinfo'
--

CREATE TABLE playerinfo (
  id int(11) NOT NULL auto_increment,
  callsign varchar(32) NOT NULL default '',
  ip varchar(15) NOT NULL default '',
  email varchar(128) NOT NULL default '',
  highscore int(11) NOT NULL default '0',
  lowscore int(11) NOT NULL default '0',
  mosttks int(11) NOT NULL default '0',
  leasttks int(11) NOT NULL default '0',
  highstrengthindex float NOT NULL default '0',
  lowstrengthindex float NOT NULL default '0',
  highkillratio float NOT NULL default '0',
  lowkillratio float NOT NULL default '0',
  mostwins int(11) NOT NULL default '0',
  mostlosses int(11) NOT NULL default '0',
  lastseen int(11) NOT NULL default '0',
  lastserver varchar(128) NOT NULL default '',
  PRIMARY KEY  (id),
  UNIQUE KEY callsign (callsign)
) TYPE=MyISAM;

--
-- Table structure for table 'players'
--

CREATE TABLE players (
  playerid int(10) unsigned NOT NULL auto_increment,
  created int(10) unsigned NOT NULL default '0',
  email varchar(64) NOT NULL default '',
  callsign varchar(32) NOT NULL default '',
  password varchar(16) NOT NULL default '',
  karma tinyint(4) NOT NULL default '0',
  assignments text,
  playtime int(10) unsigned NOT NULL default '0',
  lastmod int(10) unsigned NOT NULL default '0',
  randtext varchar(8) default NULL,
  token int(10) unsigned NOT NULL default '0',
  tokendate int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (playerid),
  UNIQUE KEY callsign (callsign),
  UNIQUE KEY email (email)
) TYPE=MyISAM;

--
-- Table structure for table 'serverinfo'
--

CREATE TABLE serverinfo (
  id int(11) NOT NULL auto_increment,
  server varchar(255) NOT NULL default '',
  players int(11) NOT NULL default '0',
  ctf enum('Y','N') NOT NULL default 'N',
  superflags enum('Y','N') NOT NULL default 'N',
  rogues enum('Y','N') NOT NULL default 'N',
  jumping enum('Y','N') NOT NULL default 'N',
  inertia enum('Y','N') NOT NULL default 'N',
  ricochet enum('Y','N') NOT NULL default 'N',
  shakable enum('Y','N') NOT NULL default 'N',
  antidoteflags enum('Y','N') NOT NULL default 'N',
  timesync enum('Y','N') NOT NULL default 'N',
  rabbitchase enum('Y','N') NOT NULL default 'N',
  maxshots int(11) NOT NULL default '0',
  shakewins int(11) NOT NULL default '0',
  shaketimeout int(11) NOT NULL default '0',
  maxplayerscore int(11) NOT NULL default '0',
  maxteamscore int(11) NOT NULL default '0',
  maxtime int(11) NOT NULL default '0',
  maxplayers int(11) NOT NULL default '0',
  roguesize int(11) NOT NULL default '0',
  roguemax int(11) NOT NULL default '0',
  redsize int(11) NOT NULL default '0',
  redmax int(11) NOT NULL default '0',
  greensize int(11) NOT NULL default '0',
  greenmax int(11) NOT NULL default '0',
  bluesize int(11) NOT NULL default '0',
  bluemax int(11) NOT NULL default '0',
  purplesize int(11) NOT NULL default '0',
  purplemax int(11) NOT NULL default '0',
  observersize int(11) NOT NULL default '0',
  observermax int(11) NOT NULL default '0',
  ip text NOT NULL,
  version text NOT NULL,
  description text NOT NULL,
  PRIMARY KEY  (id),
  KEY server (server)
) TYPE=MyISAM;

--
-- Table structure for table 'servers'
--

CREATE TABLE servers (
  nameport varchar(60) NOT NULL default '',
  build varchar(60) default NULL,
  version varchar(9) NOT NULL default '',
  gameinfo varchar(73) NOT NULL default '',
  ipaddr varchar(17) NOT NULL default '',
  title varchar(80) default NULL,
  lastmod int(11) NOT NULL default '0',
  PRIMARY KEY  (nameport)
) TYPE=MyISAM;

--
-- Table structure for table 'teaminfo'
--

CREATE TABLE teaminfo (
  id int(11) NOT NULL auto_increment,
  server varchar(255) NOT NULL default '',
  team enum('X','R','G','B','P') NOT NULL default 'X',
  size int(11) NOT NULL default '0',
  score int(11) NOT NULL default '0',
  won int(11) NOT NULL default '0',
  lost int(11) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY server (server)
) TYPE=MyISAM;

