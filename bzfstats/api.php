<?php
include_once("db.php");
include_once("servers.php");

function ListCurrentServers()
{
	$result = GetQueryResultsArray(SQLGet("SELECT ID, host, last_update, hash FROM current_servers"));
	if (!$result)
		echo "count=0\r\n";
	else
	{
		echo "count=" . sizeof($result). "\r\n";
		foreach ( $result as $row )
		{
			echo "id=" . $row['ID'];
			echo ";host=" , $row['host'];
			echo ";hash=" . $row['hash'];
			echo ";last_update=" . $row['last_update'];
			echo "\r\n";
		}
	}
	echo "OK\r\n";
}

function ServerInfo()
{
	if (!isset($_REQUEST['id']))
	{
		echo "no id\r\n";
		echo "FAIL\r\n";
		return;
	}
	
	$id = Sanitize($_REQUEST['id']);
	
	if (!ServerExists($id))
	{
		echo "invalid id\r\n";
		echo "FAIL\r\n";
		return;
	}
	
	$result = GetQueryResultsArray(SQLGet("SELECT * FROM current_servers WHERE ID=" . $id));
	if (!$result)
	{
		echo "db error\r\n";
		echo "FAIL\r\n";
	}
	else
	{
		$row = $result[0];
		echo "id=" . $row['ID'] . "\r\n";
		echo "host=" , $row['host'] . "\r\n";
		echo "description=" . $row['description'] . "\r\n";
		echo "hash=" . $row['hash'] . "\r\n";
		echo "last_update=" . $row['last_update'] . "\r\n";
		echo "game=" . $row['mode'] . "\r\n";
		echo "map=" . $row['map'] . "\r\n";
		if ($row['mode'] == "ClassicCTF")
		{
			echo "red_wins=" . $row['red_wins'] . "\r\n";
			echo "red_losses=" . $row['red_losses'] . "\r\n";
			echo "red_score=" . $row['red_score'] . "\r\n";

			echo "blue_wins=" . $row['blue_wins'] . "\r\n";
			echo "blue_losses=" . $row['blue_losses'] . "\r\n";
			echo "rblue_score=" . $row['blue_score'] . "\r\n";

			echo "green_wins=" . $row['green_wins'] . "\r\n";
			echo "green_losses=" . $row['green_losses'] . "\r\n";
			echo "green_score=" . $row['green_score'] . "\r\n";

			echo "purple_wins=" . $row['purple_wins'] . "\r\n";
			echo "purple_losses=" . $row['purple_losses'] . "\r\n";
			echo "purple_score=" . $row['purple_score'] . "\r\n";
		}
		echo "players=0\r\n";
	}
	echo "OK\r\n";
}

function HandleAPIRequest()
{
	$action = $_REQUEST['action'];
	if ($action == "list")
		ListCurrentServers();
	else if ($action == "info")
		ServerInfo();
	else
		echo "FAIL\r\n";
}

function ValidCredentials()
{
	return TRUE;
}

ConnectToDB();

header('Content-type: text/plain');

if (ValidCredentials())
{
	if (isset($_REQUEST['action']) && !isset($_REQUEST['isgameserver']))
		HandleAPIRequest();
}
else
	echo "FAIL\r\n";

?>