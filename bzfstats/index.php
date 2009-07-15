<?php
include_once("db.php");
include_once("log.php");

ConnectToDB();

if (isset($_REQUEST['action']) && isset($_REQUEST['isgameserver']) && $_REQUEST['isgameserver']== '1')
{
	LogTransaction();
}
echo "ok";

?>