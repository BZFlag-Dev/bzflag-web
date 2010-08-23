drop table if exists bzl_themes;
create table bzl_themes(
	id int NOT NULL auto_increment PRIMARY KEY,
	tag		char(12),
	displayname	char(16),
	themedir	varchar(250)
);


insert into bzl_themes (tag, displayname, themedir) VALUES
  ('light', 'Lt. Blue', 'templates/genericlight'),
  ('dark', 'Dark', 'templates/genericdark');
