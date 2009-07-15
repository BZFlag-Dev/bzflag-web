<?php
include_once("db.php");
include_once("log.php");
include_once("servers.php");
include_once("args.php");

function ProcessAdd ()
{
	$host = Sanitize($_SERVER['HTTP_HOST']) . ":" . GetPort();

	$id = FindServerInCurrent($host);
	
	if (!$id )
		$id  = AddServerToCurrent($host);
		
	if (!$id )
		return; // oh crap!
		
	// get the current hash
	$currentHash = GetDBFieldForID ( $id, "current_servers", "hash" );
	// get the hash for this update
	if (isset($_REQUEST['hash']))
		$hash = Sanitize($_REQUEST['hash']);
	else
		$hash = "0";
	
	$now = date('Y-m-d h:m:s');
	
	if ($hash != $currentHash) // it's update time
	{
		$map = "UNKNOWN";
		
		// ok update all the server stuff
		$query = "UPDATE current_servers SET mode='".GetGame()."', map='".GetMap()."', description='".GetDesc()."', hash='".$hash."', last_update='". $now . "' WHERE ID=" . $id;
		SQLSet($query);
		
		// see if its' CTF
		if ( GetGame() == "ClassicCTF")
		{
			// do the teams
			$query = "UPDATE current_servers SET red_wins='".GetTeamWins("red")."', red_losses='".GetTeamLosses("red")."', red_score='".GetTeamScore("red")."' WHERE ID=" . $id;
			$query = "UPDATE current_servers SET green_wins='".GetTeamWins("green")."', green_losses='".GetTeamLosses("green")."', green_score='".GetTeamScore("green")."' WHERE ID=" . $id;
			$query = "UPDATE current_servers SET blue_wins='".GetTeamWins("blue")."', blue_losses='".GetTeamLosses("blue")."', blue_score='".GetTeamScore("blue")."' WHERE ID=" . $id;
			$query = "UPDATE current_servers SET purple_wins='".GetTeamWins("purple")."', purple_losses='".GetTeamLosses("purple")."', purple_score='".GetTeamScore("purple")."' WHERE ID=" . $id;
		}
		// update player data
	}

	// update the heartbeat time
	SetDBFieldForID ( $id, "current_servers", "last_heartbeat", $now );
}

ConnectToDB();
if (isset($_REQUEST['action']) && isset($_REQUEST['isgameserver']) && $_REQUEST['isgameserver']== '1')
{
	LogTransaction();
	$action = $_REQUEST['action'];
	if ($action == "add")
		ProcessAdd();
}
echo "ok";

?>