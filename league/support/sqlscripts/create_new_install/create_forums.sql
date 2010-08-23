--
-- Table structure for table `l_forum`
--

CREATE TABLE l_forum (
  id int(11) NOT NULL auto_increment,
  title varchar(80) NOT NULL default '',
  status enum('Open','Hidden') NOT NULL default 'Open',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `l_forummsg`
--

CREATE TABLE l_forummsg (
  msgid int(11) NOT NULL auto_increment,
  threadid int(11) NOT NULL default '0',
  fromid int(11) NOT NULL default '0',
  msg text NOT NULL,
  datesent datetime NOT NULL default '0000-00-00 00:00:00',
  `status` ENUM( 'normal', 'edited', 'deleted' ) NOT NULL DEFAULT 'normal',
  `status_by` INT, 
  `status_at` DATETIME,
  PRIMARY KEY  (msgid),
  KEY threadid (threadid,fromid)
) TYPE=MyISAM;

--
-- Table structure for table `l_forumthread`
--

CREATE TABLE l_forumthread (
  id int(11) NOT NULL auto_increment,
  forumid int(11) NOT NULL default '0',
  creatorid int(11) NOT NULL default '0',
  subject varchar(80) NOT NULL default '',
  `status` ENUM( 'normal', 'locked', 'deleted' ) NOT NULL DEFAULT 'normal',
  `status_by` INT, 
  `status_at` DATETIME,
  `is_sticky` TINYINT(0) default 0,
  PRIMARY KEY  (id),
  KEY forumid (forumid,creatorid)
) TYPE=MyISAM;


INSERT INTO l_forum (title) VALUES 
  ("General League Discussion");
	
