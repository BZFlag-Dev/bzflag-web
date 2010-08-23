<?php
$root = dirname($_SERVER['PATH_TRANSLATED']) . '/../../../' ;
ini_set('include_path', ini_get('include_path') . ':' . $root );
require_once($root.".config/config.php");
require_once("lib/helpers.php");
require_once("lib/common.php");
require_once("lib/sql.php");
sqlConnect(SQL_CONFIG);
require_once("lib/session.php");

$res = mysql_query("SELECT id FROM l_team") or die(mysql_error());

while ($row = mysql_fetch_assoc($res) ) {
	print "Updating team #$team_id<br />";
	$team_id = $row['id'];

	$res2 = mysql_query("SELECT count(*) FROM bzl_match WHERE ( team1 = $team_id OR team2 = $team_id )") or die(mysql_error());
	$row = mysql_fetch_array($res2);
	$matches = $row[0];
	mysql_query("UPDATE l_team SET matches=$matches WHERE id = $team_id") or die(mysql_error());
}
?>
