create table l_badpass(
  id smallint NOT NULL auto_increment,
	gmtime datetime,
  ip char (16),
	name varchar(40),
	pass varchar (20),	
  PRIMARY KEY  (id)
)

