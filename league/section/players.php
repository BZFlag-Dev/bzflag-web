<?php
function section_players(&$templ)
{
	$res = mysql_query("select l_player.id, l_player.callsign, l_team.name, l_team.leader, l_team.id teamid, l_player.logo
			from l_player
			left join l_team on l_player.team = l_team.id
			where l_player.id != 929
			order by l_team.name, l_player.callsign");


	echo "<table align=center border=0 cellspacing=0 cellpadding=1><tr class=tablehead align=center><td>Callsign</td><td colspan=2>Team</td></tr>";
	$curteam = "";

	while($obj = mysql_fetch_object($res)) {
		if($obj->logo != '') {
			$logo = '<img src="images/islogo.gif">';
		} else {
			$logo = '&nbsp;';
		}

		if($obj->name != "") {

			if($obj->id == $obj->leader)
				$mem = '<img src="images/leader.gif">';
			else
				$mem = '&nbsp;';
			$pteam = '<a href="index.php?link=teaminfo&id='.$obj->teamid.'&'.SID.'">'.$obj->name.'</a>';

		} else {

			$mem = "";
			$pteam = "<i><font color=#a0a0a0>none</font></i>";
		}

		if($obj->name != $curteam) {
			$curteam = $obj->name;
			echo '<tr><td colspan=3><hr></td></tr>';
		}
		echo '<tr><td><a href="index.php?link=playerinfo&id='.$obj->id.'&'.SID.'">'.$obj->callsign.'</a></td><td align=center>'.$pteam.'</td><td>'.$logo.$mem.'</td></tr>';
	}
	echo "</table>";
}
?>
