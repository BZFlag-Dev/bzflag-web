<?php 
// $Id$
// match feed: requested by orange 9/6/2006
// Optional GET arguments:  s=seasonid

header ('Content-type: text/plain');
header ('Content-disposition: inline; filename=fights.csv');
//header ('Cache-Control: no-cache');
//header ('Expires: ' . date ('r'));

require_once("phplib.php");

$season = (int)$_GET['s'];

$set = sqlQuery ('SELECT id, name from l_team WHERE matches>0 order by id');
while ( $row = mysql_fetch_object ($set)){
	echo  "200, $row->id, $row->name\n";
}

if ($season)
	$set = sqlQuery ("SELECT * from bzl_match WHERE season=$season order by tsactual");
else
	$set = sqlQuery ('SELECT * from bzl_match order by tsactual');

while ( $row = mysql_fetch_object ($set)){
	echo "201, $row->team1, $row->team2, $row->score1, $row->score2, $row->tsactual, $row->season, $row->mlength\n";
}

?>
