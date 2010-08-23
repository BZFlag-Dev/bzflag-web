CREATE TABLE l_badpass (
  id smallint(6) NOT NULL auto_increment,
  gmtime datetime default NULL,
  ip varchar(16) default NULL,
  name varchar(40) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

