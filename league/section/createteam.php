<?php
function section_createteam(&$templ)
{
	$vars = array('f_ok_x','f_name','f_logo','f_password','f_password2');
	foreach($vars as $var)
		$$var = isset($_POST[$var]) ? $_POST[$var] : $_GET[$var];

	$error = 1;
	if($f_ok_x) {
		$error = 0;
		// Check fields

		// Check team name
		if($f_name == "") {
			$error = 1;
			echo "<div class=error>Team name must not be empty</div>";
		} else {
			// Check unicity
			if(mysql_num_rows(mysql_query("select id from l_team where name='".$f_name."'"))) {
				$error = 1;
			echo "<div class=error>This team already exists</div>";
		}
	}

	// Check logo
	//$msg = checkLogoSize($f_logo); - empty function
	$msg = '';
	if($msg != '') {
		$error = 1;
		echo "<div class=error>$msg</div>";
	}

	// Check admin password
	if($f_password != $f_password2) {
		$error = 1;
		echo "<div class=error>The passwords don't match</div>";
	} else {
		if($f_password == "") {
			$error = 1;
			echo "<div class=error>The admin passowrd must not be empty</div>";
		} else {
			$cypher = crypt($f_password);
		}
	}

	}
	if($error) {
		echo '<form method=post>'.SID_FORM.'<table align=center border=0 cellspacing=0 cellpadding=1>
		<tr><td>Team name:</td><td><input type=text size=40 maxlength=40 name=f_name value="'.$f_name.'"></td></tr>
		<tr><td colspan=2><hr></td></tr>
		<tr><td>Admin password:</td><td><input type=password size=8 maxlength=8 name=f_password value="'.$f_password.'"></td></tr>
		<tr><td>Verify:</td><td><input type=password size=8 maxlength=8 name=f_password2 value="'.$f_password2.'"></td></tr>
		<tr><td colspan=2><hr></td></tr>
		<tr><td>Optional team logo URL (400x300 max!):</td><td><input type=text size=40 maxlength=200 name=f_logo value="'.$f_logo.'"></td></tr>
		<tr><td colspan=2><hr></td></tr>
		<tr><td colspan=2>Optional (but recommended) description:<br><textarea cols=50 rows=6 name=f_comment>'.$f_comment.'</textarea></td></tr>
		<tr><td colspan=2><hr></td></tr>
		<tr><td colspan=2 align=center><input type=image src="images/ok.gif" name=f_ok>&nbsp;<input type=image src="images/cancel.gif" name=f_cancel></td></tr>
		</table></form>';
	} else {
		// Insert data into table
		$f_name = htmlentities($f_name);
		mysql_query("insert into l_team (id, name, comment, leader, logo, status, score, password, adminclosed) values(0, '".addSlashes($f_name)."', '".addSlashes($f_comment)."', ".$_SESSION['playerid'].", '".addSlashes($f_logo)."', 'opened', 1200, '".$cypher."', 
		'no')");

		$_SESSION['teamid'] = mysql_insert_id();
		$_SESSION['leader'] = 1;

		mysql_query("update l_team set status='opened' where id=".$_SESSION['teamid']);

		// Update me so I belong to my team
		mysql_query("update l_player set team=" . $_SESSION['teamid'] . " where id=".$_SESSION['playerid']);
		echo '<center>Thank you, <a href="index.php?link=playerinfo&id='.$_SESSION['playerid'].'&'.SID.'">'.$_SESSION['callsign'].'</a>, for creating the brand new <a href="index.php?link=teaminfo&id='.$_SESSION['teamid'].'&'.SID.'">'.$f_name.'</a> team.<br>
		<br>We hope that your team will show courage on the battlefield ;-)</center>';
	}
}
?>
