<?php
function section_jointeam(&$team)
{
	require_once('lib/common.php');

	$id = addslashes($_GET['id']);
	$link = 'jointeam';
	$f_yes_x = $_GET['f_yes_x'];
	$f_no_x = $_GET['f_no_x'];

	if($f_yes_x) {
		// Try to add player to team
		$obj = mysql_fetch_object(mysql_query("select l_team.id, l_team.name, l_team.leader, count(callsign) nump
			from l_team
			left join l_player
			on l_team.id = l_player.team
			where l_team.id = ".$id."
			group by l_team.id, l_team.name, l_team.leader"));
		if($obj->nump == $TEAMSIZE) {
			// Team full
			echo "<center>Sorry, this team is full. Probably someone was joining it at the same time...</center>";
		} else {
			// Okay, let's do the update
			mysql_query("update l_player set team=".$id." where id=".$_SESSION['playerid']);
			// Close team if it's full
			if($obj->nump == $TEAMSIZE-1) {
							mysql_query("update l_team set status='closed' where id=".$id);
			}
			// Close team if it has 3 players and is adminclosed
			if($obj->nump == 2) {
							mysql_query("update l_team set status='closed' where adminclosed='yes' and id=".$id);
			}
			echo '<center>You are now a member of the <a href="index.php?link=teaminfo&id='.$id.'&'.SID.'">'.$obj->name.'</a> team.<br>
											A message has been sent to the team leader.</center>';
			$_SESSION['teamid'] = $id;
			// Send a message to the team leader
			sendMessage(0, $obj->leader, $_SESSION['callsign'] .' joined your team!', '<center>---ADMINISTRATIVE MESSAGE---</center><br>A new player just joined your team: "'.$_SESSION['callsign'] .'"');
		}
	} else if($f_no_x) {
		echo "<center>Okay then, you still don't belong to any team...</center>";
	} else {
		if($_SESSION['teamid']) {
			// Stop playing with the headers
			echo '<center>Well, '.$_SESSION['callsign'].', you already belong to a team...<br>
					Please stop playing with the headers on your browser, and go play some <a href="http://bzflag.org" target="_blank">BZFlag</a> :-)</center>';
		} else {
			$obj = mysql_fetch_object(mysql_query("select name, status from l_team where id=".$id));
			switch( $obj->status )
			{
			case 'opened':
				echo '<center>Dear <a href="index.php?link=playerinfo&id='.$_SESSION['playerid'].'&'.SID.'">'.$_SESSION['callsign'].'</a>, are you sure that
			you really want to join the fabulous <a href="index.php?link=teaminfo&id='.$id.'&'.SID.'">'.$obj->name.'</a> team ?<br>';
				echo '<form method=get>'.SID_FORM.'
				<input type=hidden name=link value='.$link.'>
				<input type=hidden name=id value='.$id.'>
				<input type=image src="images/yes.gif" name=f_yes>&nbsp;<input type=image src="images/no.gif" name=f_no>
				</form></center>';
				break;
			case 'closed':
				echo "<center>{$obj->name} is a closed team</center>";
				break;
			case 'deleted':
				echo "<center>{$obj->name} is a deleted team, is only here for historical reasons.</center>";
				break;
			}
		}
	}
}
?>
