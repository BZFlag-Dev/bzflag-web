<?php
function section_leaveteam(&$templ)
{
	require_once('lib/common.php');
	$id = addslashes($_GET['id']);
	$link = $_GET['link'];
	$answer = $_GET['answer'];

	$s_playerid = $_SESSION['playerid'];

	$obj = mysql_fetch_object(mysql_query("select name, leader from l_team where id=".$id));

	if($answer == "") {
		if($s_playerid == $leader) {
			// Prevent leaders from leaving their own team
			echo "<center>You just can't abandon the magnificient <a href=\"index.php?link=teaminfo&id=".$id."&".SID."\">$obj->name</a> team, you are its leader!</center>";
		} else {
			echo '<center>You are about to abandon the magnificient <a href="index.php?link=teaminfo&id='.$id.'&'.SID.'">'.$obj->name.'</a> team, its members will miss you...<br>';
			echo "Please, please, stay in the team!<br>";
			echo "Are you really sure you wan't to abandon this team ?<br><br>";
			echo '<a href="index.php?link='.$link.'&id='.$id.'&answer=yes&'.SID.'"><img border=0 src="images/yes.gif"></a>&nbsp;<a href="index.php?link='.$link.'&id='.$id.'&answer=no&'.SID.'"><img border=0 src="images/no.gif"></a></center>';
		}
	} else {
		if($answer == "yes") {
			// Remove this player from the team
			mysql_query("update l_player set team=0 where id=".$s_playerid);
			// Open the team, because it needs at least one more player,
			// but only if it is not administratively closed, or if num players<3
			$pl = mysql_fetch_object(mysql_query("select count(*) num from l_player where team=".$id));
			if($pl->num < 3) {
							mysql_query("update l_team set status='opened' where id=".$id);
			} else {
							mysql_query("update l_team set status='opened' where adminclosed='no' and id=".$id);
			}
			echo "<center>That's it, you are no more a member of the <a href=\"index.php?link=teaminfo&id=".$id."&".SID."\">".$obj->name."</a> team.<br>
											A message has just been sent to the leader</center>";
			$_SESSION['teamid'] = 0;
			// Send a message to the team leader
			sendMessage(0, $obj->leader, $_SESSION['callsign'].' quitted!', '<center>---ADMINISTRATIVE MESSAGE---</center><br>A player just left your team: "'.$_SESSION['callsign'].'"');
		} else {
			// Say thank you
			echo '<center>The <a href="index.php?link=teaminfo&id='.$id.'&'.SID.'">'.$obj->name.'</a> team members thank you for being so brave, and continue the fight!</center>';
		}
	}
}
?>
