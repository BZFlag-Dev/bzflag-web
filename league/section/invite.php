<?php
function section_invite(&$templ)
{
	require_once('lib/common.php');

	$vars = array('id','f_ok_x','f_invite','link');
	foreach($vars as $var)
		$$var = isset($_POST[$var]) ? $_POST[$var] : $_GET[$var];


	if($_SESSION['leader']) {
		$player = mysql_fetch_object(mysql_query("select callsign from l_player where id=$id"));
		$team = mysql_fetch_object(mysql_query("select name from l_team where id={$_SESSION['teamid']}"));
		$error = 1;

		if($f_ok_x) {
			$error = 0;
			$f_invite = stripslashes($f_invite);
			if($f_invite == '') {
				$error=1;
				echo "<div class=error>You must say something to the player you're inviting</div>";
			}
		}

		if($error) {
			echo '<center><form method=post>'.SID_FORM.'Sending an invitation to <a href="index.php?link=playerinfo&id='.$id.'&'.SID.'">'.$player->callsign.'</a>&nbsp;
			to join your team, <a href="index.php?link=teaminfo&id='.$s_teamid.'&'.SID.'">'.$team->name.'</a>.<br><hr>
			Enter a few words which will be attached to your invitation (mandatory):<br>
			<textarea cols=50 rows=6 name=f_invite>'.$f_invite.'</textarea>
			<hr><center>
			<input type=image src="images/ok.gif" name=f_ok>&nbsp;
			<a href="index.php?link=playerinfo&id='.$id.'&'.SID.'"><img border=0 src="images/cancel.gif"></a>
			<input type=hidden name=link value='.$link.'>
			<input type=hidden name=id value='.$id.'>
			</center></form></center>';
		} else {
			// Send message to the player
			echo "<center>Message sent to player, thank you!</center>";
			$msg = '<b>'.$_SESSION['callsign'].'</b> is willing you to join his team: <b>'.$team->name.'</b>.<br><br>
					<u>Invitation text:</u><br>'.$f_invite;
			// Insert data
			sendMessage($s_playerid, $id, 'Invitation from '.$s_callsign, $msg);
		}

	} else {
	   echo "<center>Hey! Please stop playing with you browser!</center>";
	}
}
?>