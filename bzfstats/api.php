<?php
include_once("db.php");

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

function HandleAPIRequest()
{
	$action = $_REQUEST['action'];
	if ($action == "list")
		ListCurrentServers();
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