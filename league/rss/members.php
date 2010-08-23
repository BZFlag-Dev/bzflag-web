<?php // $Id: members.php,v 1.1 2006/07/01 01:12:06 dennismp Exp $ vim:sts=2:et:sw=2
// Makes teams/players available for easy parsing (used for ladder currently)

header ('Content-type: text/plain');
header ('Content-disposition: inline; filename=members.txt');
//header ('Cache-Control: no-cache');
//header ('Expires: ' . date ('r'));

require_once("phplib.php");

$set = sqlQuery ('SELECT callsign, altnik1, altnik2, team.name 
       FROM l_player, l_team as team WHERE team=team.id ORDER BY team.name');
while ( $row = mysql_fetch_object ($set)){
  echo "$row->name, $row->callsign";
  if ($row->altnik1)
    echo ", $row->altnik1";
  if ($row->altnik2)
    echo ", $row->altnik2";
  echo "\n";
}



?>
