-- Table structure for table `bzl_shame`
--

CREATE TABLE bzl_shame (
  id int(11) NOT NULL auto_increment,
  newsdate datetime default NULL,
  authorname varchar(30) default NULL,
  text text,
  PRIMARY KEY  (id),
  KEY newsdate (newsdate)
) TYPE=MyISAM;

