#
# Send BZmail message to all users ...
#


set @subject = 'Special Announcement!';

set @message = '<CENTER><b>TO ALL USERS<BR>Special Announcement!</b></center>
	As you can see, this website has had some changes made to it lately. Please read the "website" section on the FAQ page for more information. Visit <a href="http://bzleague.m-a-t.com/bb"><b>the website bb</b></a> if you would like to make a bug report or suggestions for the website.  As usual, see the contact page to ask questions about the league itself.  Thank you, and we hope you enjoy the new site.';



set @from=0;
set @UTCnow = DATE_ADD( '1970-01-01', INTERVAL UNIX_TIMESTAMP() SECOND);

insert into l_message (toid, fromid, datesent, subject, msg, status, team)
	select id, @from, @UTCnow, @subject, @message, 'new', 'no'
		 from l_player where status='registered';





