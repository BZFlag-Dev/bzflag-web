<?php
function section_home(&$templ)
{
	$obj = mysql_fetch_object(mysql_query("select homepage from l_siteconfig"));
	$str = "<center><img src=\"images/home.gif\"></center>";
	$str .= "<table align=center border=0><tr><td>".nl2br($obj->homepage)."</td></tr></table>";
	// Display some statistics!!!

	$numplayer = mysql_fetch_object(mysql_query("select count(*) num from l_player"));
	$numnone = mysql_fetch_object(mysql_query("select count(*) num from l_player where team=0"));
	$numteam = mysql_fetch_object(mysql_query("select count(*) num from l_team"));
	$numfight = mysql_fetch_object(mysql_query("select count(*) num from l_fight"));
	$str .= '<hr><table border=0 align=center cellspacing=0 cellpadding=1>'.
			'<tr><td colspan=2 class=tablehead align=center>Some statistics</td></tr>'.
			'<tr><td>Number of players</td><td align=right>'.$numplayer->num.'</td></tr>'.
			'<tr><td>Players without a team</td><td align=right>'.$numnone->num.'</td></tr>'.
			'<tr><td>Number of teams</td><td align=right>'.$numteam->num.'</td></tr>'.
			'<tr><td>Number of matches played</td><td align=right>'.$numfight->num.'</td></tr>'.
			'</table>';

	$templ->set('page.content',$str);
}
?>
