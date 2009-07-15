<?php

function GetTeamScore ( $team )
{
	if (isset($_REQUEST[$team . 'teamscore']))
		return Sanitize($_REQUEST[$team . 'teamscore']);
	return "0";
}	
function GetTeamWins ( $team )
{
	if (isset($_REQUEST[$team . 'teamwins']))
		return Sanitize($_REQUEST[$team . 'teamwins']);	
	return "0";
}

function GetTeamLosses ( $team )
{
	if (isset($_REQUEST[$team . 'teamlosses']))
		return Sanitize($_REQUEST[$team . 'teamlosses']);	
	return "0";
}

function GetPlayerCallsign ( $index )
{
	if (isset($_REQUEST['callsign' . $index] ))
		return Sanitize($_REQUEST['callsign' . $index]);
	return "UNKNOWN";
}
		
function GetPlayerMotto ( $index )
{
	if (isset($_REQUEST['motto' . $index] ))
		return Sanitize($_REQUEST['motto' . $index]);
	return "";
}

function GetPlayerTeam ( $index )
{
	if (isset($_REQUEST['team' . $index] ))
		return Sanitize($_REQUEST['team' . $index]);
	return "UNKNOWN";
}

function GetPlayerBZID ( $index )
{
	if (isset($_REQUEST['bzID' . $index] ))
		return Sanitize($_REQUEST['bzID' . $index]);
	return "-1";
}
		
function GetPlayerToken ( $index )
{
	if (isset($_REQUEST['token' . $index] ))
		return Sanitize($_REQUEST['token' . $index]);
	return "0";
}

function GetPlayerWins( $index )
{
	if (isset($_REQUEST['wins' . $index] ))
		return Sanitize($_REQUEST['wins' . $index]);
	return "0";
}

function GetPlayerLosses( $index )
{
	if (isset($_REQUEST['losses' . $index] ))
		return Sanitize($_REQUEST['losses' . $index]);
	return "0";
}
		
function GetPlayerTKs( $index )
{
	if (isset($_REQUEST['teamkills' . $index] ))
		return Sanitize($_REQUEST['teamkills' . $index]);
	return "0";
}
		
function GetPlayerVersion( $index )
{
	if (isset($_REQUEST['version' . $index] ))
		return Sanitize($_REQUEST['version' . $index]);

	return "UNKNOWN";
}

function GetPort()
{	
	if (isset($_REQUEST['port']))
		return Sanitize($_REQUEST['port']);
	return "5154";
}

function GetHost()
{	
	if (isset($_REQUEST['host']))
		return Sanitize($_REQUEST['host']);
	return Sanitize($_SERVER['HTTP_HOST']);
}

function GetGame()
{	
	if (isset($_REQUEST['game']))
		return Sanitize($_REQUEST['game']);
	return "TeamFFA";
}

function GetDesc()
{	
	if (isset($_REQUEST['desc']))
		return Sanitize($_REQUEST['desc']);
	return "";
}
		
function GetMap()
{	
	if (isset($_REQUEST['map']))
		return Sanitize($_REQUEST['map']);
	return "";
}	

function GetHash()
{	
	if (isset($_REQUEST['hash']))
		return Sanitize($_REQUEST['hash']);
	return "0";
}	

function GetPlayerCount()
{	
	if (isset($_REQUEST['playercount']))
		return Sanitize($_REQUEST['playercount']);
	return "0";
}	
?>