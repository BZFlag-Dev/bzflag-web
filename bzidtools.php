<?php
include('/etc/bzflag/serversettings.php');

function Sanitize ( $value )
{
	return mysql_real_escape_string(addslashes($value));	
}

function Unsanitize ( $value )
{
	return stripslashes($value);	
}

header('Content-type: text/plain');
if (!isset($_REQUEST['action']) || !isset($_REQUEST['value']) )
	return;
	
$db = mysql_pconnect($dbhost, $dbuname, $dbpass);
if (!$db)
{
	echo "Error";
	return;
}
else
	$result = mysql_select_db($bbdbname);
	
if (!$result)
{
	echo "Error";
	return;
}

$action = $_REQUEST['action'];
$value = Sanitize($_REQUEST['value']);

$query = "";

if ( $action == 'name')
{
	$query = "SELECT username_clean FROM bzbb3_users WHERE user_id=" . $value;
}
else if ( $action == 'id')
{
	$query = "SELECT user_id FROM bzbb3_users WHERE username_clean='" . $value . "'";
}
else
	echo "Error";
	
if ($query)
{
	$result = mysql_query($query);
	if ($result && mysql_num_rows($result))
	{
		$row=mysql_fetch_row($result);
		echo $row[0];
	}
	else
		echo "Error: " . $query . " " . mysql_error();
}
?>