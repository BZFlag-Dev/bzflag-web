<?php
include_once("db.php");
include_once("log.php");
include_once("servers.php");

function ProcessAdd ()
{
	$host = Sanitize($_SERVER['HTTP_HOST']);
	if (isset($_REQUEST['port']))
		$host .= ":" . Sanitize($_REQUEST['port']);
	else
		$host .= ":5154";

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
		
	if ($hash != $currentHash) // it's update time
	{
		// ok update all the server stuff
		//$query = "UPDATE current_servers SET 
	
	}

	// update the heartbeat time
	SetDBFieldForID ( $id, "current_servers", "last_heartbeat", date('Y-m-d h:m:s') );
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