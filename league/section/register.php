<?php
function section_register(&$templ)
{
	$vars = array('f_cancel_x','f_ok_x','f_comment','f_callsign','f_logo','f_password1','f_password2','link');
	foreach($vars as $var)
		$$var = isset($_POST[$var]) ? $_POST[$var] : $_GET[$var];

	if($_SESSION['callsign']) {
		// Already logged in
		echo "<center>You are already registered with the league system, but thanks!</center>";
	} else {
		if($f_cancel_x) {
			echo "<center>Okay, we won't register you right now.</center>";
		} else {
			$error = 1;

			if($f_ok_x) {
				$f_comment = stripslashes($f_comment);
				$f_callsign = stripslashes($f_callsign);
				$f_logo = stripslashes($f_logo);
				$f_password1 = stripslashes($f_password1);
				$f_password2 = stripslashes($f_password2);

				// Check fields
				$error = 0;

				// Check callsign unicity or nullity

				if($f_callsign == "") {
					$error = 1;
					echo "<div class=error>Please choose a callsign.</div>";
				} else {
					$res = mysql_query("select callsign from l_player where callsign=\"".$f_callsign."\"");
					if(mysql_num_rows($res) != 0) {
						// Duplicate callsign
						$error = 1;
						$f_callsign = "";
						echo "<div class=error>Callsign already used by another player.</div>";
				}
			}

			// Check password
			if($f_password1 != $f_password2) {
				$error = 1;
				echo "<div class=error>The passwords do not match.</div>";
				$f_password1 = "";
				$f_password2 = "";
			} else {
				if($f_password1 == "") {
					echo "<div class=error>Your password can't be empty.</div>";
				}
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
			// New user
			echo '<center>Welcome to the <a href="http://bzflag.org" target="_blank">BZFlag</a> Capture The Flag League registration page!<br><br>
			If you haven'."'".'t read the F.A.Q. yet, please <a href="index.php?link=faq&'.SID.'">do it now</a>.<br><hr>
			When you are ready, please fill in the following information to setup a new account in the league system.<br>
			<form method=post>'.SID_FORM.'
			<input type=hidden name=link value='.$link.'>
			<table align=center border=0 cellspacing=0 cellpadding=1>
			<tr><td>Please choose a callsign:</td><td><input type=text size=40 maxlength=40 name=f_callsign value="'.$f_callsign.'"></td></tr>
			<tr><td>Choose a password:</td><td><input type=password size=8 maxlength=8 name=f_password1 value="'.$f_password1.'"></td></tr>
			<tr><td>Enter your password again:</td><td><input type=password size=8 maxlength=8 name=f_password2 value="'.$f_password2.'"></td></tr>
			<tr><td colspan=2><hr></td></tr>
			<tr><td colspan=2>Enter an optional URL pointing to a picture that will be displayed with your personal informations (400x300 max!):<br><input type=text size=50 maxlength=200 name=f_logo value="'.$f_logo.'"></td></tr>
			<tr><td colspan=2>Enter an optional (but recommended) comment on yourself:<br>
			<textarea cols=45 rows=4 name=f_comment>'.$f_comment.'</textarea></td></tr>
			<tr><td colspan=2><hr></td></tr>
			<tr><td colspan=2 align=center><input type=image src="images/ok.gif" name=f_ok>&nbsp;
			<input type=image src="images/cancel.gif" name=f_cancel></td></tr>
			</table></form>
			</center>';
		} else {
			// Player OK, let's insert the row

			$cypher = crypt($f_password1);
			mysql_query($sql='insert into l_player (id, callsign, team, status,comment,logo,level, password, md5password, created, last_login) values(0, "'.addSlashes($f_callsign).'", 0, "registered", "'.addSlashes($f_comment).'", "'.addSlashes($f_logo).'", "'.player.'", encrypt("$f_password1"), "'.md5($f_password1).'",now(),now())');

			// Assign session variables
			$_SESSION['callsign']	= $f_callsign;
			$_SESSION['playerid']	= mysql_insert_id();
			$_SESSION['level']		= "player";

			// Insert an entry into the statistics table
				mysql_query("insert into l_visit values(sysdate(), '{$_SESSION['callsign']}')");

			$_SESSION['authenticated'] = true;
			$_SESSION['teamid'] = 0;
			$_SESSION['leader'] = 0;

			echo '<center>Welcome onboard, <a href="index.php?link=playerinfo&id='.$_SESSION['playerid'].'&'.SID.'">'.$_SESSION['callsign'].'</a>.<br><br>
			You are now a registered user on the league system, and are allowed to join a team, or create a new one.<br>
			Please read the F.A.Q. to know how.
			<hr>
			This is your personal information:<br>
			Login: '.$_SESSION['callsign'].'<br>
			Password: '.$f_password1.'<br>
			<hr>
			If you lose your password, the only way for you to have a new one is to contact one of the site admins.<br>
			See <a href="index.php?link=contact&'.SID.'">the contact page</a> if you wan'."'".'t to do that.
			</center>';
		}
	}
	}
}
?>