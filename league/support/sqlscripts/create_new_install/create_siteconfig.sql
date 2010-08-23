drop table if exists bzl_siteconfig;

create table bzl_siteconfig(
	name	char(12) PRIMARY KEY NOT NULL,
	text	text
);

set @UTCnow = DATE_ADD( '1970-01-01', INTERVAL UNIX_TIMESTAMP() SECOND);

insert into bzl_siteconfig (name,text) VALUES 
	('tablever', '2.01.00'),

	('motd', 'Not used anymore'),
	('maintenance', NOW()),

	('homepage', '<BR><CENTER><H2>Welcome to this league site</h2>
	  Site admins can edit this text using the "homepage" edit function!<p>'),

	('todo', 'This is a general "notepad" area.<BR>Think of it as a VERY simple wiki.'),

	('faq', '<HEAD>FAQs / League Info, etc. goes here!
<Q>How can a site admin edit these FAQs?
<A>By using the admin "FAQ edit".
<Q>Where can I find the "FAQ edit"?
<A>Login as admin, click on the "FAQ edit" button in the admin menu.'),

	('contact', '<BR><CENTER>Contact Information goes here.<BR><BR>
	 Site admins can edit this text using the "contacts" edit function!');


