<?php

$deadteam	= 251;	// id of dead team
$deadplayer	= 929;

require_once("lib/db.php");

// Delete empty teams and teams without login during the last 3 months.
$sql =	"SELECT	t.name, t.id, p.id, max(p.last_login) ".
	"FROM	l_team t ".
	"LEFT JOIN l_player p ON p.team = t.id ".
	"WHERE	t.id <> $deadteam ".
	"GROUP BY p.team ".
	"HAVING (max(p.last_login) < subdate(now(), INTERVAL 3 MONTH) ) ORDER BY t.name ";

$res = mysql_query($sql);
if(! $res )
	print " Failed: $sql\n";

while($row = mysql_fetch_array($res))
{
	$deleteteam++;
	print "Deleting team {$row[0]} #{$row[1]}\n";

	deleteTeam($row[1]);
}

// Delete empty teams
$sql =
	"SELECT	t.name, t.id  ".
	"FROM	l_team t ".
	"LEFT JOIN l_player p ON p.team = t.id ".
	"WHERE p.id is null ".
	"AND   t.id <> $deadteam ";

$res = mysql_query($sql);
if(! $res )
	print " Failed: $sql\n";

while($row = mysql_fetch_array($res))
{
	$deleteteam++;
	print "Deleting team {$row[0]} #{$row[1]}\n";

	deleteTeam($row[1]);
}


// Delete teams marked for deleting.

$sql = "SELECT t.name, t.id FROM l_team t WHERE t.status = 'deleted' AND t.id != $deadteam ";
$res = mysql_query($sql);
if(! $res )
	print " Failed: $sql\n";

while($row = mysql_fetch_array($res))
{
	$deleteteam++;
	print "Deleting team {$row[0]} #{$row[1]}\n";

	deleteTeam($row[1]);
}

// Delete all teamless players, who havnt been logged on for 3 months.

$sql =	"SELECT p.callsign, p.id ".
		"FROM l_player p ".
		"WHERE p.team = 0 ".
		"AND p.last_login < subdate(now(), INTERVAL 3 MONTH) ORDER BY p.callsign";

$res = mysql_query($sql);

if(! $res )
	print " Failed: $sql\n";

while($row = mysql_fetch_array($res))
{
	$deleteplayer++;
	print "Delete teamless player: " . $row[0] . "\n";

	deletePlayer($row[1],stripslashes($row[0]));
}

// Find and mark all active teams (played a match during the last 45 days)

// this may look odd if a user sees this, perhaps rewrite this.
$sql = "UPDATE l_team SET active = 'no'";
$res = mysql_query($sql);

if(! $res )
	print " Failed: $sql\n";

$sql = "SELECT distinct t.id ".
		"FROM l_team t, l_fight f ".
		"WHERE (f.team1 = t.id or f.team2 = t.id) ".
		" AND f.tstamp > subdate(now(), INTERVAL 45 DAY) ".
		" and t.id != $deadteam";

$res = mysql_query($sql);

if(! $res )
	print " Failed: $sql\n";

while($row = mysql_fetch_array($res))
{
	$sql = "UPDATE l_team SET active = 'yes' WHERE id = {$row[0]}";

	$res2 = mysql_query($sql);

	if(! $res2 )
		print " Failed: $sql\n";
}

if( $deleteteam )
	print "Deleted $deleteteam teams\n";
if( $deleteplayer )
	print "Deleted $deleteplayer players.\n";

function deleteTeam($id)
{
	global $deadteam;
	$sql = array();

	// Free players
	$sql[] = "UPDATE l_player SET team = 0 WHERE team = $id";

	// Assign battles to deleted team
	$sql[] = "UPDATE l_fight SET team1 = $deadteam WHERE team1 = $id";
	$sql[] = "UPDATE l_fight SET team2 = $deadteam WHERE team2 = $id";

	// if both teams are deleted, delete it
	$sql[] = "DELETE FROM l_fight WHERE team1 = team2 AND team1 = $deadteam";

	// And the team
	$sql[] = "DELETE FROM l_team WHERE id = $id";

	foreach($sql as $s)
	{
		$res = mysql_query($s);
		if(! $res )
			print " Failed: $s\n";
	}
}

function deletePlayer($id,$name)
{
	global $deadplayer;

	$name = addslashes($name);

	$sql = array();

	// Assign posts to a deleted player 
	$sql[] = "UPDATE l_forummsg SET fromid = $deadplayer WHERE fromid=$id";
	$sql[] = "UPDATE l_forumthread SET creatorid = $deadplayer WHERE creatorid = $id";

	// Messages
	$sql[] = "UPDATE l_message SET toid = $deadplayer WHERE toid = $id";
	$sql[] = "UPDATE l_message SET fromid = $deadplayer WHERE fromid = $id";

	// if both sender and reciever is deleted, remove the message
	$sql[] = "DELETE FROM l_message WHERE toid = fromid  AND toid = $deadplayer";

	// Visits
	$sql[] = "DELETE FROM l_visit WHERE callsign = '$name'";

	// and the player
	$sql[] = "DELETE FROM l_player WHERE id = $id";

	foreach($sql as $s)
	{
		$res = mysql_query($s);
		if(! $res )
			print " Failed: $s\n";
	}
}
?>
