<?php

function section_login(&$templ)
{
	$vars = array('f_ok','f_callsign','f_password');

	foreach($vars as $var)
		$$var = isset($_POST[$var]) ? $_POST[$var] : $_GET[$var];

	if($f_ok) {
		// Check the password
		$res = mysql_query("select id, callsign, password, level, team, md5password, unix_timestamp(last_login) as last_login
							from l_player
							where callsign='".addSlashes(htmlentities($f_callsign))."'");
		if(mysql_num_rows($res) != 0) {

			$obj = mysql_fetch_object($res);

			// Check MD5 password, or password, and do the conversion
			$cyphermd5 = md5($f_password);

			// Do check the password
			if($obj->md5password == '') {
				$cypher = crypt($f_password, substr($obj->password, 0, 2));

				if($cypher == $obj->password) {
					$idsuccess = 1;
					// Update MD5 password in database
					mysql_query("update l_player set md5password='$cyphermd5' where id=".$obj->id);
				} else {
					$idsuccess = 0;
				}

			} else {
				if($cyphermd5 == $obj->md5password)
					$idsuccess = 1;
				else
					$idsuccess = 0;

			}
			if($idsuccess == 1) {
				// Logged in!
				// Insert an entry into the statistics table

// added IP logging - menotume 2004-12-10
//				mysql_query("insert into l_visit values(sysdate(), '$obj->callsign')");
				mysql_query("insert into l_visit values(sysdate(), '$obj->callsign',
					'{$_SERVER['REMOTE_ADDR']}', $obj->id)");



				$now = gmdate("Y-m-d H:i:s");
				mysql_query("UPDATE l_player SET last_login='$now' WHERE id=" . $obj->id);

				$_SESSION['authenticated']= true;
				$_SESSION['callsign'] = $obj->callsign;
				$_SESSION['level'] = $obj->level;
				$_SESSION['playerid'] = $obj->id;
				$_SESSION['teamid'] = $obj->team;
				$_SESSION['last_login'] = $obj->last_login;
				// Check if I am a leader
				$res = mysql_query("select id from l_team where leader={$_SESSION['playerid']}");
				$_SESSION['leader'] = mysql_num_rows($res);
			}
		}
	}
	if($f_ok and $idsuccess == 0) {
		// Unknown callsign
		echo "<div class=error>Wrong callsign or password</div>";
	}

	if($_SESSION['authenticated']) {
		echo '<center>Welcome back, <a href="index.php?link=playerinfo&id='.$_SESSION['playerid'].'&'.SID.'">'.$_SESSION['callsign'].'</a><br><br>';
		if($_SESSION['teamid'] == 0) {
			echo "You are not belonging to any team. What are you waiting for? ;-)";
		} else {
			// Get team name
			$obj=mysql_fetch_object(mysql_query("select name from l_team where id=".$_SESSION['teamid']));
			echo 'You are a member of the magnificent <a href="index.php?link=teaminfo&id='.$_SESSION['teamid'].'&'.SID.'">'.$obj->name.'</a> team.';
		}
		echo '<br><br><form method=post>'.SID_FORM.'<input type=image name=logout src="images/logout.gif"></form></center>';
	} else {
		echo '<form method=post>' . SID_FORM . '
			<table align=center border=0 cellspacing=0 cellpadding=1>
			<input type=hidden name=link value='.$link.'>
			<tr><td>Callsign:</td><td><input type=text name=f_callsign value="'.$f_callsign.'" size=40 maxlength=40></td></tr>
			<tr><td>Password:</td><td><input type=password name=f_password size=8 maxlength=8></td></tr>
			<tr><td colspan=2 align=center><input type=submit value="Login" name=f_ok></td></tr>
			</table></form>';
	}
}
?>
