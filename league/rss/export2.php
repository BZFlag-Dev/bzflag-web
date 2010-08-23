<?php
// Makes teams/players available for easy parsing (used for ladder currently)


require_once("phplib.php");


	header("Content-type: text/plain\n\n");

	$sql =	"SELECT id, name ".
			"FROM l_team WHERE status <> 'deleted'";

	$res = mysql_query($sql);
	while($row = mysql_fetch_array($res))
	{
		print "TE: ". $row[0] . ", " . stripslashes($row[1]) . "\n";
	}

	#$sql =	"SELECT team, callsign, id ".
	#		"FROM l_player ".
	#		"WHERE team != 0 ";
	$sql =	"SELECT team, callsign, id ".
			"FROM l_player ".
			"WHERE status != 'deleted' ";

	$res = mysql_query($sql);
	while($row = mysql_fetch_array($res))
	{
		print "PL: $row[0], $row[2],  " . stripslashes($row[1]) . "\n";
	}

?>
