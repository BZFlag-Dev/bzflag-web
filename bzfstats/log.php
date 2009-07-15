<?php

function BuildTeamScoreLog ( $team )
{
	$str = "";
	if (isset($_REQUEST[$team . 'teamscore']))
		$str .= Sanitize($_REQUEST[$team . 'teamscore']);
	else
		$str .= "0";
		
	$str .= ",";
	if (isset($_REQUEST[$team . 'teamwins']))
		$str .= Sanitize($_REQUEST[$team . 'teamwins']);
	else
		$str .= "0";

	$str .= ",";
	if (isset($_REQUEST[$team . 'teamlosses']))
		$str .= Sanitize($_REQUEST[$team . 'teamlosses']);
	else
		$str .= "0";
		
	return $str;
}

function BuildPlayerLog ( $index )
{
	$str = $index . "=";
	if (isset($_REQUEST['callsign' . $index] ))
		$str .= Sanitize($_REQUEST['callsign' . $index]);
	else
		$str .= "UNKNOWN";
		
	$str .= ",";
	if (isset($_REQUEST['motto' . $index] ))
		$str .= Sanitize($_REQUEST['motto' . $index]);
	else
		$str .= "UNKNOWN";

	$str .= ",";
	if (isset($_REQUEST['team' . $index] ))
		$str .= Sanitize($_REQUEST['team' . $index]);
	else
		$str .= "UNKNOWN";

	$str .= ",";
	if (isset($_REQUEST['bzID' . $index] ))
		$str .= Sanitize($_REQUEST['bzID' . $index]);
	else
		$str .= "UNKNOWN";
		
	$str .= ",";
	if (isset($_REQUEST['token' . $index] ))
		$str .= Sanitize($_REQUEST['token' . $index]);
	else
		$str .= "UNKNOWN";
		
	$str .= ",";
	if (isset($_REQUEST['wins' . $index] ))
		$str .= Sanitize($_REQUEST['wins' . $index]);
	else
		$str .= "UNKNOWN";
		
	$str .= ",";
	if (isset($_REQUEST['losses' . $index] ))
		$str .= Sanitize($_REQUEST['losses' . $index]);
	else
		$str .= "UNKNOWN";
		
	$str .= ",";
	if (isset($_REQUEST['teamkills' . $index] ))
		$str .= Sanitize($_REQUEST['teamkills' . $index]);
	else
		$str .= "UNKNOWN";
	
	$str .= ",";
	if (isset($_REQUEST['version' . $index] ))
		$str .= Sanitize($_REQUEST['version' . $index]);
	else
		$str .= "UNKNOWN";
	
	$str .= ";";
		
	return $str;
}

function LogTransaction()
{
	$action = $_REQUEST['action'];
	
	$host = Sanitize($_SERVER['HTTP_HOST']);
		
	if (isset($_REQUEST['port']))
		$host .= ":" . Sanitize($_REQUEST['port']);
	else
		$host .= ":5154";
		
	$name = "";
	if (isset($_REQUEST['host']))
		$name = Sanitize($_REQUEST['host']);
	else
		$name = Sanitize($_SERVER['HTTP_HOST']);
		
	$gameinfo= "game=";
	if (isset($_REQUEST['game']))
		$gameinfo .= Sanitize($_REQUEST['game']);
	else
		$gameinfo .= "TeamFFA";
		
	$gameinfo .= "&desc=";
	if (isset($_REQUEST['desc']))
		$gameinfo .= Sanitize($_REQUEST['desc']);
	else
		$gameinfo .= "NONE";

	$gameinfo .= "&map=";
	if (isset($_REQUEST['map']))
		$gameinfo .= Sanitize($_REQUEST['map']);
	else
		$gameinfo .= "UNKNOWN";

	$gameinfo .= "&teamscores=";
	
	$gameinfo .= BuildTeamScoreLog("red");
	$gameinfo .= ',' . BuildTeamScoreLog("green");
	$gameinfo .= ',' . BuildTeamScoreLog("blue");
	$gameinfo .= ',' . BuildTeamScoreLog("purple");

	$hash = "&hash=";
	if (isset($_REQUEST['hash']))
		$hash .= Sanitize($_REQUEST['hash']);
	else
		$hash .= "0";
		
	$players = "players=";
	if (isset($_REQUEST['playercount']))
		$players .= Sanitize($_REQUEST['playercount']);
	else
		$players .= "0";

	if ($action == "part")
	{
		$players .= "&";
		$players .= BuildPlayerLog("-1");
	}
	if (isset($_REQUEST['playercount']))
	{
		$count = Sanitize($_REQUEST['playercount']);
		
		for($i = 0; $i < $count; $i += 1)
		{
			$players .= "&";
			$players .= BuildPlayerLog($i);
		}
	}
	
	$query = "INSERT INTO log (host, name, hash, action, gameinfo, playerinfo) VALUES('". $host. "', '". $name . 
							"', '".$hash . "', '" . $action . "', '" . $gameinfo. "', '". $players . "')";
	$result = SQLSet($query);
	echo "result" . $result . "<br>";
	echo "query" . $query;
}

?>