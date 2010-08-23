create table bzl_invites(
	id int NOT NULL auto_increment PRIMARY KEY,
	playerid	int,
	teamid		int,
	expires		datetime
);


--
-- Table structure for table `bzl_countries`
--

CREATE TABLE bzl_countries (
  iso char(2) default NULL,
  name varchar(40) default NULL,
  iso3 char(3) default NULL,
  numcode smallint(6) NOT NULL default '0',
  flagname varchar(4) default NULL,
  PRIMARY KEY  (numcode)
) TYPE=MyISAM;

--
-- Table structure for table `bzl_freezeranks`
--

CREATE TABLE bzl_freezeranks (
  id int(11) NOT NULL default '0',
  name varchar(40) default NULL,
  rank int(11) default NULL,
  ts datetime default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `bzl_links`
--

CREATE TABLE bzl_links (
  id int(11) NOT NULL auto_increment,
  name varchar(80) default NULL,
  url varchar(120) default NULL,
  comment text,
  dt date default NULL,
  ord smallint(6) default '0',
  PRIMARY KEY  (id),
  KEY dt (dt)
) TYPE=MyISAM;

--
-- Table structure for table `bzl_match`
--

CREATE TABLE bzl_match (
  id int(11) NOT NULL auto_increment,
  team1 int(11) NOT NULL default '0',
  score1 int(11) NOT NULL default '0',
  team2 int(11) NOT NULL default '0',
  score2 int(11) NOT NULL default '0',
  oldrankt1 int(11) default NULL,
  oldrankt2 int(11) default NULL,
  newrankt1 int(11) default NULL,
  newrankt2 int(11) default NULL,
  tsactual datetime NOT NULL default '0000-00-00 00:00:00',
  identer int(11) NOT NULL default '0',
  tsenter datetime NOT NULL default '0000-00-00 00:00:00',
  idedit int(11) default NULL,
  tsedit datetime default NULL,
  mvp    int(11) default NULL,
  season int(11) default '1',
  `mlength` SMALLINT UNSIGNED NOT NULL DEFAULT '30',
  PRIMARY KEY  (id),
  KEY team1 (team1),
  KEY team2 (team2),
  KEY tsactual (tsactual),
  KEY season (season)
) TYPE=MyISAM;

--
-- Table structure for table `bzl_news`
--

CREATE TABLE bzl_news (
  id int(11) NOT NULL auto_increment,
  newsdate datetime default NULL,
  authorname varchar(30) default NULL,
  text text,
  PRIMARY KEY  (id),
  KEY newsdate (newsdate)
) TYPE=MyISAM;


--
-- Table structure for table `bzl_bans`
--

CREATE TABLE bzl_bans (
  id int(11) NOT NULL auto_increment,
  bandate datetime default NULL,
  authorname varchar(30) default NULL,
  text text,
  PRIMARY KEY  (id),
  KEY bandate (bandate)
) TYPE=MyISAM;

--
-- Table structure for table `bzl_states`
--

CREATE TABLE bzl_states (
  id smallint(6) NOT NULL auto_increment,
  name char(30) NOT NULL default '',
  abbrev char(2) default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `bzl_visit`
--

CREATE TABLE bzl_visit (
  ts datetime NOT NULL default '0000-00-00 00:00:00',
  pid int(11) NOT NULL default '0',
  ip char(16) default NULL,
  KEY ip (ip),
  KEY pid (pid)
) TYPE=MyISAM;


--
-- Table structure for table `l_message`
--

CREATE TABLE l_message (
  msgid int(11) NOT NULL auto_increment,
  toid int(11) NOT NULL default '0',
  fromid int(11) NOT NULL default '0',
  datesent datetime NOT NULL default '0000-00-00 00:00:00',
  subject varchar(80) NOT NULL default '',
  msg text NOT NULL,
  status set('new','read','replied') NOT NULL default 'new',
  team set('yes','no') NOT NULL default 'no',
  htmlok char(1) default '0',
  PRIMARY KEY  (msgid),
  KEY playerid (toid),
  KEY fromid (fromid),
  KEY datesent (datesent)
) TYPE=MyISAM;


--
-- Table structure for table `l_player`
--

CREATE TABLE l_player (
  id int(11) NOT NULL auto_increment,
  callsign varchar(30) NOT NULL default '',
  team int(11) NOT NULL default '0',
  status set('unregistered','registered','deleted') NOT NULL default 'unregistered',
  comment text NOT NULL,
  logo varchar(200) NOT NULL default '',
  role_id int NOT NULL default 4,
  created datetime NOT NULL default '0000-00-00 00:00:00',
  last_login datetime NOT NULL default '0000-00-00 00:00:00',
  password varchar(13) NOT NULL default '',
  md5password varchar(32) default NULL,
  country smallint(6) NOT NULL default '-1',
  state smallint(6) NOT NULL default '-1',
  email varchar(40) default NULL,
  aim varchar(20) default NULL,
  yim varchar(20) default NULL,
  msm varchar(30) default NULL,
  jabber varchar(30) default NULL,
  altnik1 varchar(24) default NULL,
  altnik2 varchar(24) default NULL,
  ircnik1 varchar(20) default NULL,
  fwdbzmail char(1) default NULL,
  fwdsysmsg char(1) default NULL,
  emailpub char(1) default NULL,
  utczone smallint(6) default NULL,
  zonename varchar(10) default NULL,
  city varchar(20) default NULL,
  icq varchar(12) default NULL,
  logobg varchar(6) NOT NULL default 'FFFFFF',
  PRIMARY KEY  (id),
  KEY team (team),
  KEY callsign (callsign)
) TYPE=MyISAM;

--
-- Table structure for table `l_session`
--

CREATE TABLE l_session (
  id varchar(32) NOT NULL default '',
  expire DATETIME NULL DEFAULT NULL ,
  data text NOT NULL,
  callsign varchar(40) default NULL,
  playerid int(11) NOT NULL default '0',
  ip varchar(16) NOT NULL default '',
  refresh enum('Y','N') NOT NULL default 'N',
  PRIMARY KEY  (id),
  KEY expire (expire)
) TYPE=MyISAM;


--
-- Table structure for table `l_team`
--

CREATE TABLE l_team (
  id int(11) NOT NULL auto_increment,
  name varchar(40) NOT NULL default '',
  comment text NOT NULL,
  leader int(11) NOT NULL default '0',
  logo varchar(200) NOT NULL default '',
  status enum('opened','closed','deleted') NOT NULL default 'opened',
  status_changed datetime NOT NULL default '0000-00-00 00:00:00',
  score int(11) NOT NULL default '0',
  password varchar(13) NOT NULL default '',
  adminclosed set('yes','no') NOT NULL default 'no',
  active set('yes','no') default 'no',
  created datetime not null,
  `matches` SMALLINT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY  (id),
  KEY score (score),
  KEY name (name)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS `bzl_permissions` (
  `id` int(11) NOT NULL auto_increment,
  `role_id` int(11) NOT NULL default '0',
  `name` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS `bzl_roles` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE (`name`)
) TYPE=MyISAM AUTO_INCREMENT=10;

--
-- Stuff for seasonal league
--

CREATE TABLE  IF NOT EXISTS `l_season` (
  `id` int(11) NOT NULL auto_increment,
  `identer` int(11) NOT NULL default '0',
  `idchange` int(11) default NULL,
  `startdate` date,
  `enddate` date,
  `fdate` datetime default NULL,
  `seasontype` set('league','playoff') NOT NULL default 'league',
  `finished` set('yes','no') NOT NULL default 'no',
  `active` set('yes','no') NOT NULL default 'no',
  `paused` set('yes','no') NOT NULL default 'no',
  `dirty` set('yes','no') NOT NULL default 'no',
  `points_win` int(11) default NULL,
  `points_draw` int(11) default NULL,
  `points_lost` int(11) default NULL,
  `position1` int(11) default NULL,
  `position2` int(11) default NULL,
  `position3` int(11) default NULL,
  `score1` int(11) default NULL,
  `score2` int(11) default NULL,
  `score3` int(11) default NULL,
  `rating1` int(11) default NULL,
  `rating2` int(11) default NULL,
  `rating3` int(11) default NULL,
  `mostactive` int(11) default NULL,
  `mvp`       int(11) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE (`startdate`),
  UNIQUE (`enddate`)
) TYPE=MyISAM;

-- insert into l_season (id, startdate,enddate, active, paused, position1, position2, position3, mvp) 
--	      values (0,  NULL,    NULL,    'yes', 'yes',   NULL,      NULL,      NULL,      NULL);

insert into l_season (id, identer, active, paused) values (1,  1,    'yes', 'yes');
	      
CREATE TABLE  IF NOT EXISTS `l_teamscore` (
  `team`     int(11)  NOT NULL,
  `season`   int(11)  NOT NULL,
  `score`    int(11)  NOT NULL default '0',
  `won`      int(11)  NOT NULL default '0',
  `lost`     int(11)  NOT NULL default '0',
  `draw`     int(11)  NOT NULL default '0',
  `zelo`     int(11)  NOT NULL default '1200',
  `matches`  int(11)  NOT NULL default '0',
  `tsmatch`  datetime NOT NULL,
  `tsedit`   datetime NOT NULL,
  
  PRIMARY KEY  (`team`,`season`),
  KEY score (`score`),
  KEY season (`season`),
  KEY team (`team`)
) TYPE=MyISAM;
