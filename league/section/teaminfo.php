<?php
function section_teaminfo(&$templ)
{
	require_once('lib/common.php');
	$s_teamid =			$_SESSION['teamid'];
	$s_logedin =		$_SESSION['authenticated'];
	$s_level =			$_SESSION['level'];
	$s_playerid =		$_SESSION['playerid'];

	$id = addslashes($_GET['id']);

	$res = mysql_query("select name, comment, leader, logo, status, score, unix_timestamp(status_changed) as status_changed
		from l_team
		where id='$id'");

	$team = mysql_fetch_object($res);
	echo '<table align=center border=0 cellspacing=0 cellpadding=1>
		<tr><td colspan=2 class=teambg align=center><div class=teamname><b>'.$team->name.'</b></div></td></tr>';

	// Logo if any
	if($team->logo != "") {
		echo '<tr><td colspan=2 align=center><img src="'.$team->logo.'"><hr></td></tr>';
	}
	// Rating
	echo '<tr><td align=center>Rating</td><td align=\"center\">'.displayRating($id).'</td></tr>';
	// Matches statistics
	$sta1 = mysql_fetch_object(mysql_query("select ifnull(sum(if(score1>score2,1,0)),0) win,
		ifnull(sum(if(score1=score2,1,0)),0) draw,
		ifnull(sum(if(score1<score2,1,0)),0) loss
		from l_fight where team1=$id"));
	$sta2 = mysql_fetch_object(mysql_query("select ifnull(sum(if(score2>score1,1,0)),0) win,
		ifnull(sum(if(score2=score1,1,0)),0) draw,
		ifnull(sum(if(score2<score1,1,0)),0) loss
		from l_fight where team2=$id"));
	$win = $sta1->win + $sta2->win;
	$draw = $sta1->draw + $sta2->draw;
	$loss = $sta1->loss + $sta2->loss;
	echo "<tr><td colspan=2 align=center><hr>
	<table width=100% border=0 cellspacing=0 cellpadding=0 align=center>
	<tr>
	<td align=center>Wins</td><td align=center>Draws</td><td align=center>Losses</td></tr><tr>
	<td align=center>$win</td><td align=center>$draw</td><td align=center>$loss</td>
	</tr></table>
	<hr></td></tr>";
	// Comment if any
	if($team->comment != "") {
		echo '<tr><td colspan=2><ul>'.nl2br($team->comment).'</ul><hr></td></tr>';
	}
	// Players list
	$i = 0;
	$res = mysql_query("select id, callsign, comment, status
		from l_player
		where team=".$id."
		order by callsign");
	$members = 0;
	while($obj = mysql_fetch_object($res)) {
		$members++;
		if($i)
			$cl = "biobg1";
		else
			$cl = "biobg2";
		$i = 1 - $i;
		echo "<tr class=$cl><td align=center>";
		if($obj->id == $team->leader) {
			$lead = '&nbsp;<img src="images/leader.gif">';
		} else {
			$lead = '';
		}
		echo '<a href="index.php?link=playerinfo&id='.$obj->id.'&'.SID.'">'.$obj->callsign.'</a>'.$lead.'</td>';
		if($s_logedin&&($s_level=='admin'||$s_playerid==$team->leader)) {
			if($obj->id != $team->leader) {
				// Link to ban a player from a team
				echo '<td align=center><form method=get>'.SID_FORM.'
				<input type=hidden name=link value=banplayer>
				<input type=hidden name=playerid value='.$obj->id.'>
				<input type=hidden name=teamid value='.$id.'>
				<input type=image src="images/ban.gif" name=f_ok>
				</form></td>';
			} else {
				echo '<td>&nbsp;</td></tr>';
			}
		} else {
			echo '<td>&nbsp;</td></tr>';
		}
	}
	// Join this team is opened, and if we are logged and not belonging to any team
	if($s_logedin&&!$s_teamid) {
		if($members < 20 && $team->status == "opened") {
			echo '<tr><td colspan=2 align=center><hr><a href="index.php?link=jointhisteam&id='.$id.'&'.SID.'"><img border=0 src="images/jointhisteam.gif"></a></td></tr>';
		}
	}
	// Send a message to all the team members
	if($s_logedin && $team->status != 'deleted') {
		echo '<tr><td colspan=2><hr></td></tr><tr><td colspan=2 align=center><a href="index.php?link=sendmessage&tid='.$id.'&'.SID.'"><img border=0 src="images/sendmessagesmall.gif"></a></td></tr>';
	}
	// Show last fights

	$sql = "SELECT	t1.id, t1.name, f.score1, t2.id, t2.name, f.score2, f.tstamp, unix_timestamp(f.tstamp) tstamp_ts ".
			"FROM	l_fight f, l_team t1, l_team t2 ".
			"WHERE (f.team1 = '$id' OR f.team2 = '$id') ".
			" AND f.team1 = t1.id ".
			" AND f.team2 = t2.id ".
			"ORDER BY f.tstamp DESC ".
			"LIMIT 11 ";

	$res = mysql_query($sql);

	$tmp = '';
	$count = 0;
	while($row = mysql_fetch_array($res))
	{
		$count++;
		if( $count < 11 )
		{
			if( isset($_SESSION['last_login']) && ($_SESSION['last_login'] < $row[7] ))
			{
				$new1 = "<font color=\"red\">";
				$new2 = "</font>";
			}
			else
			{
				$new1 = '';
				$new2 = '';
			}

			if($count % 2)
				$cl = "biobg1";
			else
				$cl = "biobg2";

			$tmp .= "<tr class=\"$cl\">";
			
			$tmp .= '<td align="right">' . $new1. $row[6] . $new2. '</td>';
			if( $row[0] != $id )
			{
				$oppid		= $row[0];
				$oppname	= stripslashes($row[1]);
				$oppscore	= $row[2];

				$teamscore	= $row[5];
			}
			else
			{
				$oppid		= $row[3];
				$oppname	= stripslashes($row[4]);
				$oppscore	= $row[5];

				$teamscore	= $row[2];
			}

			$tmp .= "<td>$new1";

			$oppname = '<a href="index.php?link=teaminfo&id=' . $oppid . '&' . SID . '">' . 
				$new1 . $oppname . $new2 . '</a>';

			if( $oppscore < $teamscore )
				$tmp .= "<b>Won</b> against $oppname";
			elseif( $oppscore > $teamscore )
				$tmp .= "<b>Lost</b> against $oppname";
			else
				$tmp .= "<b>Tie</b> against $oppname";

			$tmp .= " ({$row[2]} - {$row[5]})";
			$tmp .= "$new2</td>";

			$tmp .= "</tr>";
		}
	}

	echo '<tr>';
	echo '<td colspan=2 align=center>&nbsp;<br><b>';
	if( $count == 1 )
	{
		echo 'Last match';
	}
	else
	{
		if( $count == 11 )
		{
			echo 'Last 10 matches, view them all <a href="index.php?link=fights&id=' . $id . '&' . SID . '">here</a>';
		}
		elseif( $count == 0 )
		{
			echo "No matches played";
		}
		else
		{
			echo "Last $count matches";
		}
	}
	echo '</b></td>';
	echo '</tr>';

	print  $tmp;
	echo "</table>";


	// Or... edit your team if you are a leader or ad admin, or with a password if you are a member
	// Team members can't change the password, though...
	// Unless ofcause the team is deleted

	if( $team->status == 'deleted' ) {
		if( $team->status_changed )
			echo "<br><center>This team is deleted as of " . gmdate(DATEFORMAT_FULL, $team->status_changed) . "</center>";
		else
			echo "<br><center>This team is deleted.</center>";
	} else {
		if($s_logedin&&($s_level=='admin'||$s_teamid==$id)) {
			if(($s_level != 'admin')&&($s_playerid != $team->leader)) {
				// Team member, need password
				echo '<form method=post>'.SID_FORM.'
				<input type=hidden name=link value=teamadmin>
				<input type=hidden name=id value='.$id.'>
				<br><center>Enter team password <input type=password size=8 maxlength=8 name=f_password> and <input type=image name=f_edit src="images/edit.gif"></center>
				</form>';
			} else {
				// Team leader or admin, let's go
				echo '<br><center><a href="index.php?link=teamadmin&id='.$id.'&'.SID.'"><img border=0 src="images/edit.gif"></a></center>';
				echo '<br><center><a href="index.php?link=dismissteam&id='.$id.'&'.SID.'"><img border=0 src="images/dismissteam.gif"></a></center>';
			}
		}
	}
}
?>
