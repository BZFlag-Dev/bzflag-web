<?php
function section_banplayer(&$templ)
{
	require_once('lib/common.php');
	$playerid		= $_GET['playerid'];
	$teamid			= $_GET['teamid'];
	$f_okban_x		= $_GET['f_okban_x'];
	$f_ok_x			= $_GET['f_ok_x'];
	$callsign		= $_GET['callsign'];
	$name			= $_GET['name'];

	$link			= $_GET['link'];

	$player = mysql_fetch_object(mysql_query("select callsign from l_player where id=".$playerid));
	$team = mysql_fetch_object(mysql_query("select name from l_team where id=".$teamid));

	if($f_okban_x) {
		// Ban confirmed
		// Open team is not administratively closed, or if num players<3
		$pl = mysql_fetch_object(mysql_query("select count(*) num from l_player where team=".$teamid));

		if($pl->num == 3) {
			mysql_query("update l_team set status='opened' where id=".$teamid);
		} else {
			mysql_query("update l_team set status='opened' where adminclosed='no' and id=".$teamid);
		}

		mysql_query("update l_player set team=0 where id=".$playerid);
		echo '<center>Well <a href="index.php?link=playerinfo&id='.$s_playerid.'&'.SID.'">'.$_SESSION['callsign'].'</a>,
					<a href="index.php?link=playerinfo&id='.$playerid.'&'.SID.'">'.$callsign.'</a> is no more a member
					of the team <a href="index.php?link=teaminfo&id='.$teamid.'&'.SID.'">'.$name.'</a>
					<br>A message has just been sent to the user.</center>';
					sendMessage(0, $playerid, 'You have been banned!', '<center>---ADMINISTRATIVE MESSAGE---</center>Sorry, but '.$_SESSION['callsign'].' banned you from the '.$team->name.' team<br>');
	} else {
		if(!$f_ok_x) {
			// Someone is playing with the headers
			echo "<center>Hey! Please stop playing with you browser!</center>";
		} else {
			echo '<center>Please confirm that you want to ban <a href="index.php?link=playerinfo&id='.$playerid.'&'.SID.'">'.$player->callsign.'</a>
			from the team <a href="index.php?link=teaminfo&id='.$teamid.'&'.SID.'">'.$team->name.'</a>.</center>';
			echo '<center><form method=GET>'.SID_FORM.'
					<input type=hidden name=link value='.$link.'>
					<input type=hidden name=playerid value='.$playerid.'>
					<input type=hidden name=teamid value='.$teamid.'>
					<input type=hidden name=callsign value="'.$player->callsign.'">
					<input type=hidden name=name value="'.$team->name.'">
					<input type=image src="images/ok.gif" name=f_okban>&nbsp;<a href="index.php?link=teaminfo&id='.$teamid.'&'.SID.'"><img border=0 src="images/cancel.gif"></a>
					</form></center>';
		}
	}
}
?>