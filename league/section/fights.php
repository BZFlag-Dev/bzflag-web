<?php

function section_fights(&$templ)
{
	if( isset($_GET['id']) )
		$query = 'WHERE (l_team1.id = \'' . addslashes($_GET['id']) . '\' OR l_team2.id = \''. addslashes($_GET['id']) . '\') ';
	else
		$query = '';

	$res = mysql_query("select tstamp, team1, l_team1.name name1, score1, team2, l_team2.name name2, score2, unix_timestamp(tstamp) as tstamp_ts
			from l_fight
			left join l_team l_team1
			on l_team1.id = team1
			left join l_team l_team2
			on l_team2.id = team2
			$query
			order by tstamp desc");

	echo "<table align=center border=0 cellspacing=0 cellpadding=2>
			<tr class=tablehead align=center><td>Date</td><td>Opposing Teams</td><td colspan=3>Score</td></tr>";

	$count = 1;
	while($obj = mysql_fetch_object($res)) {
		$count ++;

		if($count %2)
			$cl = "biobg1";
		else
			$cl = "biobg2";

		if( isset($_SESSION['last_login']) && ($_SESSION['last_login'] < $obj->tstamp_ts ))
		{
			$new1 = "<font color=\"red\">";
			$new2 = "</font>";
		}
		else
		{
			$new1 = '';
			$new2 = '';
		}

		echo "<tr class=\"$cl\"><td>".$obj->tstamp.'</td><td align=center>';
		print '<a href="index.php?link=teaminfo&id='.$obj->team1.'&'.SID.'">'.$new1.$obj->name1.$new2.'</a>&nbsp;<b>vs.</b>&nbsp;';
		print '<a href="index.php?link=teaminfo&id='.$obj->team2.'&'.SID.'">'.$new1.$obj->name2.$new2.'</a></td>';
		print '<td align=center>'.$obj->score1.'</td><td>-</td><td align=center>'.$obj->score2.'</td></tr>';
	}
	echo "</table>";

}
?>