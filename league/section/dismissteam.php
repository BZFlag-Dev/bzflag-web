<?php

function section_dismissteam(&$templ)
{
	require_once('lib/common.php');

	$id = addslashes($_GET['id']);
	$f_ok_x = $_GET['f_ok_x'];
	$f_no_x = $_GET['f_no_x'];
	$link	= $_GET['link'];

	// Check permission
	$allowed = 0;
	$obj = mysql_fetch_object(mysql_query("select name, comment, leader, logo, password, status, adminclosed from l_team where id=".$id));
	if(($_SESSION['authenticated']=='admin')||($_SESSION['playerid']== $obj->leader)) {
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
	if(!$allowed) {
		echo "<center>Sorry, you don't have the permission to dismiss the <a href=\"index.php?link=teaminfo&id=".$id."&" .SID."\">".$obj->name."</a> team.</center>";
	} else {
		if($f_ok_x) {
			// Destroy the team
			sendTeamMessage(0, $id, 'Team dismissed!', '<center>---ADMINISTRATIVE MESSAGE---</center><br>Your team has been dismissed, so you are teamless now...');
			// Teamless players
			mysql_query("update l_player set team=0 where team=$id");
			// Remove team
			//mysql_query("delete from l_team where id=$id");
			mysql_query("UPDATE l_team SET status = 'deleted', status_changed=now(), leader=0  WHERE id=$id");
			// Remove fights
			//mysql_query("delete from l_fight where team1=$id or team2=$id");
			// Update session variables
			$_SESSION['teamid'] = 0;
			$_SESSION['leader'] = 0;
			echo "Team dismissed";
		} else {
			if($f_no_x) {
				echo "<center>Phew, the <a href=\"index.php?link=teaminfo&id=".$id."&".SID."\">".$obj->name."</a> team is still alive!</center>";
			} else {
					echo "<center>Do you really want to dismiss the <a href=\"index.php?link=teaminfo&id=".$id."&".SID."\">".$obj->name."</a> team?";
					echo "<form method=get>
							<input type=hidden name=link value='$link'>
							<input type=hidden name=id value=$id>".SID_FORM."<br>
							<input type=image name=f_ok src='images/yes.gif'>
							<input type=image name=f_no src='images/no.gif'>
							</form>";
			}
		}
	}
}
?>
