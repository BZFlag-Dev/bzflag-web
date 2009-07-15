<?php
include_once("db.php");

function HandleAPIRequest()
{

}

function ValidCredentials()
{
	return TRUE;
}

ConnectToDB();

if (ValidCredentials())
{
	if (isset($_REQUEST['action']) && !isset($_REQUEST['isgameserver']))
	{
		HandleAPIRequest();
	}
	echo "ok";
}
else
	echo "fail";

?>