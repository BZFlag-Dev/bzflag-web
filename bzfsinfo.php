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

function OutputServerInfo ( $servers )
{
	foreach ($servers as $server)
	{
		$query = "SELECT username_clean FROM bzbb3_users WHERE user_id=" . $server['owner'];
		$result = mysql_query($query);
		if ($result && mysql_num_rows($result))
		{
			$row=mysql_fetch_row($result);
			echo $server['version'] . " " . $server['nameport'] . " " . $row[0]. "(" . $server['owner'] . ")\n";
		}
	}
}

header('Content-type: text/plain');
$db = mysql_pconnect($dbhost, $dbuname, $dbpass);
if (!$db)
{
	echo "Error";
	die();
}

$result = mysql_select_db($dbname);
if (!$result)
	die();

$query = "SELECT nameport, owner, version FROM servers WHERE owner >= 1" . $value;
$result = mysql_query($query);

if ($result && mysql_num_rows($result))
{
	$servers = array();
	
	$count = mysql_num_rows($result);
	for ($i = 0; i < $count; $i += 1)
	{
		$row=mysql_fetch_row($result);
		
		$info = $array();
		$info['nameport'] = Unsanitize ($row[0]);
		$info['owner'] = Unsanitize ($row[1]);
		$info['version'] = Unsanitize ($row[2]);
		
		$servers[] = $info;
	}

	$result = mysql_select_db($bbdbname);
	if (!$result)
		die();
		
	OutputServerInfo($servers);
}
else
{
	echo "No Servers";
	die();
}
?>