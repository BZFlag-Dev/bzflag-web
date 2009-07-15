<?php

function GetConnectHeader( $action )
{
	$str = "isgameserver=1&action=" . $action;
	$str .= "&host=samplehost.com&port=5154&game=TeamFFA";
	$str .= "&desc=a_server&map=RANDOM&HASH=" . rand();
	
	return $str;
}

function GetTeamScoresHeader( )
{
	$str = "&redteamscore=10&redteamwins=3&redteamlosses=5";
	$str .= "&greenteamscore=10&greenteamwins=3&greenteamlosses=5";
	$str .= "&blueteamscore=10&blueteamwins=3&blueteamlosses=5";
	$str .= "&purpleteamscore=10&purpleteamwins=3&purpleteamlosses=5";
	
	return $str;
}

function PlayerUpdate( )
{
	$str = "&playercount=1&callsign0=motto0&team0=red";
	$str .= "&bzid0=10&token0=3232323";
	$str .= "&wins0=10&losses0=3&teamkills0=5";
	$str .= "&version0=A_TEST_WEBPAGE";
	
	return $str;
}


echo "	<html><head></head><body>
		<h3>Push</h3>
		<a href=\"../index.php?". GetConnectHeader("add") . GetTeamScoresHeader() . "\">No players</a><br>
		<a href=\"../index.php?". GetConnectHeader("add") . GetTeamScoresHeader() . PlayerUpdate() .  "\">Players(update)</a><br>
		<h3>API</h3>
		<a href=\"../api.php?action=list\">API LIST</a><br>
		</body></html>";

?>