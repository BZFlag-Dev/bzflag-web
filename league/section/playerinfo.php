<?php
function section_playerinfo(&$templ)
{
	$vars = array('id','link','f_ok_x','f_cancel_x','f_password','f_password2','f_callsign','f_logo','f_comment');
	foreach($vars as $var) {
		$$var = isset($_POST[$var]) ? $_POST[$var] : $_GET[$var];
	}

	if($f_cancel_x) {
		// Cancelled
		$link = "playerinfo";
	}
	// Administration form
	$res = mysql_query("select callsign, logo, comment, level from l_player where id='$id'");
	$obj = mysql_fetch_object($res);
	if($f_ok_x) {
		// Check the fields
		$error = 0;
		if(strlen($f_password)||strlen($f_password2)) {
			// Password entered, let's check
			if($f_password != $f_password2) {
				echo "<div class=error>Passwords don't match</div>";
				$error = 1;
				$f_password = "";
				$f_password2 = "";
			} else {
				$cypher = ", md5password=md5(\"".$f_password."\")";
			}
		}
		// Check callsign unicity
		$res = mysql_query("select callsign from l_player where id!=$id and callsign=\"".$f_callsign."\"");
		if(mysql_num_rows($res) != 0) {
			$error = 1;
			echo "<div class=error>This callsign already exists: $f_callsign</div>";
			$f_callsign = $obj->callsign;
		}
		if($f_callsign == '') {
			$error = 1;
			echo "<div class=error>The callsign can't be empty</div>";
			$f_callsign = $obj->callsign;
		}
		// Check logo
		//$msg = checkLogoSize($f_logo); - this always returned "" 
		$msg = '';
		if($msg != '') {
			$error = 1;
			echo "<div class=error>$msg</div>";
		}
		if(!$error) {
			if(strlen($f_level)) {
				$level = ", level=\"".$f_level."\"";
			}
			// Update the table
			$f_callsign = htmlentities($f_callsign);
			mysql_query("update l_player
				set logo=\"".$f_logo."\", callsign=\"".$f_callsign."\", comment=\"".$f_comment."\" ".$cypher." ".$level."
				where id=".$id);
			$link="playerinfo";
			// Check if I changed my privileges ;-)
			if($_SESSION['playerid'] == $id) {
				$_SESSION['level'] = $f_level;
			}
			// Update callsign if I changed mine
			if($id == $_SESSION['playerid'] ) {
				$_SESSION['callsign'] = $f_callsign;
			}
		}
	}
	
	
	if($link == "playeradmin") {
// security check ...
if ($id!=$_SESSION['playerid'] && $_SESSION['level']!='admin'){
	echo "<CENTER><font color=cc2222>You are not authorized to edit this profile.<P>";
	displayPlayer($id);
	return;
}
		if($f_ok_x) {
			$f_logo = stripslashes($f_logo);
			$f_comment = stripslashes($f_comment);
			$f_callsign = stripslashes($f_callsign);
		} else {
			$f_logo = $obj->logo;
			$f_comment = $obj->comment;
			$f_level = $obj->level;
			$f_callsign = $obj->callsign;
		}
		echo '<form method=post>'.SID_FORM.'
		<table align=center border=0 cellspacing=0 cellpadding=1>
		<input type=hidden name=link value='.$link.'>
		<input type=hidden name=id value='.$id.'>
		<tr class=playerbg><td>Callsign:</td><td><input size=40 maxlength=40 name=f_callsign value="'.$f_callsign.'"></td></tr>';
		if($obj->logo != "") {
			echo '<tr><td align=center colspan=2><img src="'.$obj->logo.'"></td></tr>';
		}
		echo '<tr><td>Logo URL (400x300 max!):</td><td><input type=text name=f_logo value="'.$f_logo.'" size=60 maxlength=200></td></tr>
		<tr><td colspan=2><hr></td></tr>
		<tr><td>Password:</td><td><input type=password name=f_password value="'.$f_password.'" size=8 maxlength=8> (leaving the fields empty will keep the current password)</td></tr>
		<tr><td>Verify:</td><td><input type=password name=f_password2 value="'.$f_password2.'" size=8 maxlength=8></td></tr>
		<tr><td colspan=2><hr></td></tr>
		<tr><td colspan=2>Comment:<br><textarea cols=50 rows=6 name=f_comment>'.$f_comment.'</textarea><hr></td></tr>';
		// Admins can change users level
		if($_SESSION['level'] == "admin") {
			echo "<tr><td>User level:</td><td><select name=f_level>";
			if($f_level == "player") {
				echo "<option selected value=player>player</option>
				<option value=admin>admin</option>";
			} else {
				echo "<option value=player>player</option>
				<option selected value=admin>admin</option>";
			}
			echo "</select></td></tr><tr><td colspan=2><hr></td></tr>";
		}
		echo '<tr><td align=center colspan=2><input type=image src="images/ok.gif" name=f_ok>
			&nbsp;<input type=image src="images/cancel.gif" name=f_cancel></td></tr>
			</table></form>';
	} else {
		displayPlayer($id);
	}

}



function displayPlayer($id){
	$s_level		= $_SESSION['level'];
	$s_logedin		= $_SESSION['authenticated'];
	$s_playerid		= $_SESSION['playerid'];
	$s_leader		= $_SESSION['leader'];
	$s_teamid		= $_SESSION['teamid'];

	$res = mysql_query($sql="select l_player.callsign, l_player.status, l_player.comment, l_player.logo, l_team.name, l_team.id teamid, l_team.leader, unix_timestamp(l_player.created) as created, unix_timestamp(l_player.last_login) as last_login
		from l_player
		left join l_team
		on l_player.team = l_team.id
		where l_player.id = ".$id);

	$player = mysql_fetch_object($res);

	echo '<table align=center border=0 cellspacing=0 cellpadding=1>
		<tr><td class=playerbg align=center><div class=playername><b>'.$player->callsign.'</b></div></td></tr>';
	// Logo if any
	if($player->logo != "") {
		echo '<tr><td align=center><img src="'.$player->logo.'"><hr></td></tr>';
	}
	// Bio if any
	if($player->comment != "") {
		echo "<tr><td class=playerbiobig>".nl2br($player->comment)."<hr></td></tr>";
	}
	// Team if any
	if($player->name != "") {
		echo "<tr><td>";
		if($player->leader == $id)
			$mem = 'Leader (<img src="images/leader.gif">) of team';
		else
			$mem = "Member of team";
		echo $mem.'&nbsp;<a href="index.php?link=teaminfo&id='.$player->teamid.'&'.SID.'">'.$player->name.'</a>';
	} else {
		echo "<tr><td>Does not belong to any team</td></tr>";
		// If I am a team leader, and my team is not full, I can invite him
		if($_SESSION['authenticated'] ) {
			// Check if my team full
			$team = mysql_fetch_object(mysql_query("select status from l_team where id={$_SESSION['teamid']}"));
			if($team->status == 'opened') {
				echo '<tr><td><hr></td></tr><tr><td align=center><a href="index.php?link=invite&id='.$id.'&'.SID.'"><img border=0 src="images/invitesmall.gif"></a>';
			}
		}
	}

	//
	print "</td></tr>";

	// Frequentation statistics
	echo '<tr><td><hr>';
	Frequentation($player->callsign);
	echo '</td></tr>';
	// Send a message to this player (but not to myself!)
	if($s_logedin&&($s_playerid != $id)) {
		echo '<tr><td><hr></td></tr><tr><td align=center><a href="index.php?link=sendmessage&pid='.$id.'&'.SID.'"><img border=0 src="images/sendmessagesmall.gif"></a></td></tr>';
	}
	// Administrators and owner can edit a player

	if($s_logedin&&(($s_level == 'admin')||($s_playerid == $id))) {
		echo '<tr><td align=center><hr><a href="index.php?link=playeradmin&id='.$id.'&'.SID.'"><img border=0 src="images/edit.gif"></a></td></tr>';
	}
	echo "</table>";
}


function Frequentation($callsign) {
	// Get timezone shift
	$now = mysql_fetch_object(mysql_query("select substring(sysdate(),12,2) ts"));
	$res = mysql_query("select substring(data, 12, 2) hour, count(*) hits
		from l_visit
		where callsign='$callsign'
		group by 1 order by 1");
	$tot = mysql_fetch_object(mysql_query("select count(*) num from l_visit where callsign='$callsign'"));
	echo '<table border=1 cellspacing=0 cellpadding=1 align=center><tr><td class=tablehead colspan=24>Frequentation statistics (hours are grayed, and GMT)</td></tr><tr>';
	for($i = 0 ; $i < 24; $i++) {
		$visit[$i] = 0;
		echo '<td bgcolor=e0e0e0 align=center>'.$i.'</td>';
	}
	while($row = mysql_fetch_object($res)) {
		$hour = $row->hour + gmdate('H') - $now->ts;
		if($hour < 0) $hour += 24;
		if($hour > 23) $hour -= 24;
		$visit[$hour] = $row->hits;
	}
	echo '</tr><tr>';
	for($i = 0; $i < 24; $i++) {
		if($visit[$i] > $tot->num / 24 ) {
			echo '<td bgcolor=#ff4040 align=center>&nbsp;'.$visit[$i].'&nbsp;</td>';
		} else {
			echo '<td align=center>&nbsp;'.$visit[$i].'&nbsp;</td>';
		}
	}
	echo '</tr></table>';
}

?>

