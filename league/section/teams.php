<?php
function section_teams(&$teams)
{
	require_once("lib/common.php");
	$s_logedin	= $_SESSION['authenticated'];
	$s_teamid	= $_SESSION['teamid'];

	$res = mysql_query("
		SELECT	l_team.id, l_team.name, l_team.logo, l_team.score,
				player2.callsign leader, player2.id leaderid,
				l_team.status, count(distinct l_player.callsign) numplayers,
				count(distinct l_fight.tstamp) nfights, count(distinct l_fight.tstamp)>0 sorter,
				l_team.active = 'yes' activeteam
		FROM	l_team, l_player player2
		LEFT JOIN l_player ON l_player.team = l_team.id
		LEFT JOIN l_fight ON (team1 = l_team.id OR team2 = l_team.id)
		WHERE player2.id = l_team.leader
		 AND l_team.status != 'deleted'
		 AND l_team.id != 251
		GROUP BY l_team.name, l_team.leader, l_team.status, l_team.score
		ORDER BY sorter desc, activeteam desc, l_team.score desc, nfights desc, l_team.name");

	echo '<center><img src="images/teams.gif"></center><br>';
	echo "<table align=center border=0 cellspacing=0 cellpadding=2>
			<tr class=tablehead align=center><td>Name</td><td>Leader</td><td colspan=2>Members</td><td colspan=2>Rating</td><td>Join</td></tr>";
	$separated = 0;
	while($obj = mysql_fetch_object($res)) {
		if( ($obj->activeteam == 0) && ($separated == 0 ) )
		{
			$separated = 1;
			echo '<tr><td align=center colspan=10><hr><b>Inactive Teams</b></td></tr>';
		}

		if( ($obj->sorter == 0) && ($separated == 1) ) {
			$separated = 2;
			echo '<tr><td align=center colspan=10><hr><b>Did not play any match</b></td></tr>';
		}

		if($obj->logo != '') {
			$logo = '<img src="images/islogo.gif">';
		} else {
			$logo = '&nbsp;';
		}

		if($s_teamid == $obj->id) {
			echo '<tr class=myteam>';
		} elseif( $obj->status == 'deleted' )	{
			echo "<tr class=deletedteam>";
		} else {
			echo '<tr>';
		}

		echo '<td><a href="index.php?link=teaminfo&id='.$obj->id.'&'.SID.'">'.$obj->name.'</a></td>
		<td><a href="index.php?link=playerinfo&id='.$obj->leaderid.'&'.SID.'">'.$obj->leader.'</a></td>
		<td align=center>'.$obj->numplayers.'</td><td>'.$logo.'</td>
		<td align=center>'.displayRating($obj->id).'</td>';

		if($separated) {
			echo '<td>&nbsp;</td>';
		} else {
			echo '<td align=center>('.$obj->nfights.')</td>';
		}
		// Print join or joinnot, not forgetting we may already belong to a team
		if($s_logedin) {
			if(!$s_teamid) {
				switch($obj->status)
				{
				case 'opened':
					if( $obj->numplayers < 20 )
						echo '<td align=center><a href="index.php?link=jointeam&id='.$obj->id.'&'.SID.'"><img border=0 src="images/join.gif"></td>';
					else
						echo '<td align=center><img src="images/closed.gif"></td>';
					break;
				case 'closed':
					echo '<td align=center><img src="images/closed.gif"></td>';
					break;
				default:
					echo '<td align=center>deleted.</td>';
					break;
				}
			} else {
				if($s_teamid==$obj->id) {
					echo '<td align=center><a href="index.php?link=leaveteam&id='.$obj->id.'&leader='.$obj->leaderid.'&'.SID.'"><img border=0 src="images/abandon.gif"></td>';
				} elseif($obj->status == 'closed') {
					echo '<td align=center><img src="images/closed.gif"></td>';
				} elseif($obj->status == 'deleted') {
					echo '<td align=center>deleted.</td>';
				} else {
					echo '<td>&nbsp;</td>';
				}
			}
		} elseif($obj->status == 'closed') {
			echo '<td><img src="images/closed.gif"></td>';
		} else {
			echo '<td>&nbsp;</td>';
		}

		echo "</tr>";
	}
	echo "</table>";
	// Create a new team, if logged in and not a team member

	if( $s_logedin && ! $s_teamid) {
		echo '<br><center><a href="index.php?link=createteam&'.SID.'"><img border=0 src="images/createanewteam.gif"></center>';
	}
}
?>
