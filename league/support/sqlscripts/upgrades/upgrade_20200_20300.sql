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

ALTER TABLE `bzl_match` ADD `mvp` INT NULL ,
ADD `season` INT NOT NULL DEFAULT '1';
