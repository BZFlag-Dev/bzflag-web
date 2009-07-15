<?php

include_once("args.php");

function BuildTeamScoreLog ( $team )
{
	return GetTeamScore($team) . ",". GetTeamWins($team) . "," . GetTeamLosses($team);		return $str;
}

function BuildPlayerLog ( $index )
{
	$str = $index . "=";
	$str .= GetPlayerCallsign($index);
		
	$str .= ",";
	$str .= GetPlayerMotto($index);

	$str .= ",";
	$str .= GetPlayerTeam($index);

	$str .= ",";
	$str .= GetPlayerBZID($index);

		
	$str .= ",";
	$str .= GetPlayerToken($index);
	
	$str .= ",";
	$str .= GetPlayerWins($index);
		
	$str .= ",";
	$str .= GetPlayerLosses($index);
		
	$str .= ",";
	$str .= GetPlayerTKs($index);
	
	$str .= ",";
	$str .= GetPlayerVersion($index);
	
	$str .= ";";
		
	return $str;
}

function LogTransaction()
{
	$action = $_REQUEST['action'];
	
	$host = Sanitize($_SERVER['HTTP_HOST']);
		
	$host .= ":" . GetPort();
		
	$name = GetHost();;

	$gameinfo= "game=" . GetGame();
		
	$gameinfo .= "&desc=" . GetDesc();

	$gameinfo .= "&map=" . GetMap();

	$gameinfo .= "&teamscores=";
	
	$gameinfo .= BuildTeamScoreLog("red");
	$gameinfo .= ',' . BuildTeamScoreLog("green");
	$gameinfo .= ',' . BuildTeamScoreLog("blue");
	$gameinfo .= ',' . BuildTeamScoreLog("purple");

	$hash = "&hash=" . Gethash();

		
	$players = "players=" . GetPlayerCount();

	if ($action == "part")
	{
		$players .= "&";
		$players .= BuildPlayerLog("-1");
	}
	if (isset($_REQUEST['playercount']))
	{
		$count = GetPlayerCount();
		
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