<?php
define("SESSION_LIFETIME",3600);

session_set_save_handler(
	"mysql_session_open",
	"mysql_session_close",
	"mysql_session_read",
	"mysql_session_write",
	"mysql_session_destroy",
	"mysql_session_gc" );


function mysql_session_open($session_path, $session_name)
{
	//
}

function mysql_session_close()
{
	return 1;
} 

function mysql_session_read($SID)
{
	$ip = $_SERVER['REMOTE_ADDR'];

	$sql =	"SELECT data ".
			"FROM l_session ".
			"WHERE id = '$SID' ".
//			"AND ip = '$ip' ".
			"AND expire > now() ";

	$res = mysql_query($sql);
	if( $res )
	{
		$row = mysql_fetch_array($res);
		if( $row )
			return stripslashes($row[0]);
		else
			mysql_session_destroy($SID);
	}

	return "";
}

function mysql_session_write($SID,$value)
{
	$expiration = time() + SESSION_LIFETIME;

	$ip = $_SERVER['REMOTE_ADDR'];
	$SID = addslashes($SID);
	$value = addslashes($value);

	if( isset( $_SESSION['callsign'] ) ) {
		$callsign = "'" . addslashes($_SESSION['callsign']) . "'";
		$playerid = $_SESSION['playerid'];
	} else {
		$callsign = 'NULL';
		$playerid = 0;
	}

	$sql = "INSERT INTO l_session (id, expire, data, callsign, playerid,ip) ".
			"VALUES( '$SID',FROM_UNIXTIME($expiration),'$value',$callsign,$playerid,'$ip') ";

	$res = mysql_query($sql);

	// No point in much errorchecking, as
	// we cannot do anything about it. (this is executed after output is generated)

	if(! $res )	{ // If error, we try to update
		$sql = "UPDATE l_session SET ".
					"expire = FROM_UNIXTIME($expiration), ".
					"data = '$value', ".
					"callsign = $callsign, ".
					"playerid = $playerid ".
				"WHERE id = '$SID' ";

		mysql_query($sql);
	}

	return true;
}

function mysql_session_destroy($SID)
{
	$SID = addslashes($SID);
	$sql = "DELETE FROM l_session WHERE id = '$SID'";
	mysql_query($sql);
}

function mysql_session_gc($lifetime)
{
	$sql = "DELETE FROM l_session ".
			"WHERE expire < FROM_UNIXTIME(" . (time() - $lifetime) . ")";

	$res = mysql_query($sql);

	if( $res )
		return mysql_affected_rows();
	else
		return 0;
}
?>
