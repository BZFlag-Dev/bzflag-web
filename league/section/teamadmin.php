<?php
function section_teamadmin(&$templ)
{
	require_once('lib/common.php');
	$TEAMSIZE = 20;
	$vars = array('id','f_password','f_ok_x','f_comment','f_logo','f_name','f_status','f_password1','f_password2',
			'link','f_leader');

	foreach($vars as $var)
		$$var = isset($_POST[$var]) ? $_POST[$var] : $_GET[$var];

	// Check permission
	$allowed = 0;
	$obj = mysql_fetch_object(mysql_query("select name, comment, leader, logo, password, status, adminclosed from l_team where id=".$id));
	$res = mysql_fetch_object(mysql_query("select count(*) num from l_player where team=".$id));
	$numplayer = $res->num;

	if(($_SESSION['level']=='admin')||($_SESSION['playerid'] == $obj->leader)) {
		// Admin or team leader, allowed
		$allowed = 1;
	} else {
		// Check password
		$cypher = crypt($f_password, substr($obj->password, 0, 2));
		if($cypher == $obj->password) {
			// Good password, allowed
			$allowed = 1;
		}
	}

	if( $obj->status == 'deleted' )
	{
		echo "<center>Sorry, you cannot edit a deleted team.</center>";
	}
	elseif(!$allowed) {
		echo "<center>Sorry, you don't have the permission to edit the <a href=\"index.php?link=teaminfo&id=".$id."&".SID."\">".$obj->name."</a> team, or you entered an incorrect password.</center>";
	} else {
		if($f_ok_x) {
			$f_comment = stripslashes($f_comment);
			$f_logo = stripslashes($f_logo);
			$f_name = stripslashes($f_name);
			$f_status = stripslashes($f_status);
		} else {
			$f_comment = $obj->comment;
			$f_logo = $obj->logo;
			$f_name = $obj->name;
			$f_status = $obj->status;
		}

		$error = 1;
		if($f_ok_x) {
			$error = 0;
			// Check password
			if($f_password1 != $f_password2) {
				$error = 1;
				echo "<div class=error>The passwords don't match</div>";
			} else {
				if($f_password1 != "") {
					$cypher = ", password='".crypt($f_password1)."'";
				} else {
					$cypher = "";
				}
			}

			// Check duplicate team names
			$res = mysql_query("select name from l_team where id!=$id and name=\"$f_name\"");

			if(mysql_num_rows($res) != 0) {
				$error = 1;
				echo "<div class=error>A team with this name: '$f_name' already exists</div>";
				$f_name = $obj->name;
			}

			// Check empty team name
			if($f_name == '') {
				$error = 1;
				echo "<div class=error>The team name can't be empty</div>";
				$f_name = $obj->name;
			}
			// Check logo
			//$msg = checkLogoSize($f_logo);
			$msg = '';
			if($msg != '') {
				$error = 1;
				echo "<div class=error>$msg</div>";
			}
		}

		if($error) {
			echo "<form method=post>".SID_FORM."<table align=center border=0 cellspacing=0 cellpadding=1>";

			// Hidden fields
			echo '<input type=hidden name=f_password value="'.$f_password.'">';
			echo '<input type=hidden name=link value="'.$link.'">';

			if(($_SESSION['level']=='admin')||($_SESSION['playerid'] == $obj->leader)) {
				// Admins and leaders can change the team name
				echo '<tr><td>Team name:</td><td><input name=f_name size=40 maxlength=40 value="'.$f_name.'"></td></tr>';
			} else {
				echo "<tr><td align=center colspan=2 class=tablehead><div class=teamname><b>".$obj->name."</b></div><input type=hidden name=f_name value=\"$f_name\"></td></tr>";
			}

			// Logo
			if($obj->logo != "") {
				echo '<tr><td align=center colspan=2><img src="'.$obj->logo.'"></td></tr>';
			}

			echo '<tr><td>Logo URL (400x300 max!):</td><td><input type=text size=60 maxlength=200 name=f_logo value="'.$f_logo.'"></td></tr>';
			echo "<tr><td colspan=2><hr></td></tr>";

			// Admin and leaders can change the team password
			if(($_SESSION['level']=='admin')||($_SESSION['playerid'] == $obj->leader)) {
				// Password
				echo '<tr><td>Password:</td><td><input type=password size=8 maxlength=8 name=f_password1 value="'.$f_password1.'"> (leaving the fields empty will keep the current password)</td></tr>
				<tr><td>Password:</td><td><input type=password size=8 maxlength=8 name=f_password2 value="'.$f_password2.'"></td></tr>
				<tr><td colspan=2><hr></td></tr>';
			}

			// Admin and leaders can change the team status
			if(($_SESSION['level']=='admin')||($_SESSION['playerid'] == $obj->leader)) {
				if($numplayer >= 3 && $numplayer < $TEAMSIZE) {

					// Can change status IF there are 3 players or more
					echo "<tr><td>Team status:</td><td><select name=f_status>";

					if($f_status == 'opened') {
						echo '<option selected value=opened>opened</option>
							<option value=closed>closed</option>';
					} else {
						echo '<option value=opened>opened</option>
							<option selected value=closed>closed</option>';
					}
					echo "</select></td></tr>";
				} else {

					if($numplayer == 1) {
						$typo = 'player';
					} else {
						$typo = 'players';
					}

					echo '<input type=hidden name=f_status value="'.$obj->status.'">';
					echo '<tr><td align=center colspan=2>The team is currently '.$obj->status.' (forced because you have '.$numplayer.' '.$typo.')</td></tr>';
				}
			} else {
				echo '<tr><td align=center colspan=2>The team is currently '.$obj->status.'</td></tr>';
			}
			if(($_SESSION['level']=='admin')||($_SESSION['playerid'] == $obj->leader))
			{
				echo "<tr>";
				echo "<td>Leader</td>";
				echo "<td><select name=\"f_leader\">";
				$sql = "SELECT id, callsign FROM l_player WHERE team = '$id' ORDER by callsign";
				$res = mysql_query($sql);
				while($row = mysql_fetch_row($res))
				{
					if( $row[0] == $obj->leader )
						print "<option value=\"\" SELECTED>" . stripslashes($row[1]) . " (current leader)</option>";
					else
						print "<option value=\"{$row[0]}\">" . stripslashes($row[1]) . "</option>";
				}
				echo "</select>";

				echo "</td>";
				echo "</tr>";
			}

			// Comment
			echo '<tr><td colspan=2>Comment:<br><textarea name=f_comment cols=50 rows=6>'.$f_comment.'</textarea></td></tr>
				<tr><td colspan=2><hr></td></tr>
				<tr><td colspan=2><hr></td></tr>
				<tr><td align=center colspan=2><input type=image src="images/ok.gif" name=f_ok>&nbsp;<a href="index.php?link=teaminfo&id='.$id.'&'.SID.'"><img border=0 src="images/cancel.gif"></a></td></tr>
			</table></form>';
		} else {
			//				Update the table

			if(! empty($f_leader) )
			{
				$sql = "SELECT 1 FROM l_player WHERE id = '$f_leader' AND team = $id ";
				$res = mysql_query($sql);
				if( mysql_fetch_row($res) )
					$f_leader = 'leader = ' . $f_leader . ', ';
				else
					$f_leader = '';
			}
			else
				$f_leader = '';

			if($f_status == '') {
				$f_status = 'opened';
			}
			if($f_status != $obj->status) {
				// Changed the status
				if($f_status == 'closed') {
					$adminclosed = 'yes';
				} else {
					$adminclosed = 'no';
				}
			} else {
				// Keep current status
				$adminclosed = $obj->adminclosed;
			}

			mysql_query($sql = 'update l_team
				set logo="'.addSlashes($f_logo).'",
				comment="'.addSlashes($f_comment).'" '.$cypher.',
				name="'.addSlashes(htmlentities($f_name)).'",
				adminclosed="'.$adminclosed.'", ' . $f_leader . '
				status="'.$f_status.'"
				where id='.$id);

			echo '<center>Thank you, <a href="index.php?link=playerinfo&id='.$_SESSION['playerid'].'&'.SID.'">'.$_SESSION['callsign'].'</a>, for updating the <a href="index.php?link=teaminfo&id='.$id.'&'.SID.'">'.$f_name.'</a> team.</center>';

			// If changed the team name, inform the team members
			if($f_name != $obj->name) {
				echo '<center>You changed the team name, we inform your team members.</center>';
				sendTeamMessage(0, $id, 'Team renamed!', '<center>---ADMINISTRATIVE MESSAGE---</center><br>'.$_SESSION['callsign'].' just changed the name of your team from <i>'.$obj->name.'</i> to <i>'.$f_name.'</i>.');
			}
		}
	}
}
?>
