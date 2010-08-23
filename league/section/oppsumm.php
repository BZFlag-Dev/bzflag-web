<?php // $Id: oppsumm.php,v 1.3 2005/03/24 13:27:14 dennismp Exp $ vim:sts=2:et:sw=2

define (DEFAULT_DAYS, 0);
define (DEFAULT_ORDER, 'oppname');

/****
 queryGetOppSum():
  sort: total, ratio, oppname, opprank
   result set: oppid, oppname, opprank, wins, losses, ties, total, ratio
****/

function section_oppsumm_queryGetOppSum($teamid, $days, $sort){

  sqlQuery ("DROP TABLE if exists  tsum");

  if(!$sort)
    $sort="total";
  if($sort!="oppname")
    $ascdesc = "desc";
  sqlQuery ("CREATE TEMPORARY TABLE tsum (
    oppid int not null,
    oppname VARCHAR(35),
    opprank int,
    oppstat CHAR(12),
    teamscore int,
    oppscore int)");

  $q="INSERT INTO tsum (oppid, oppname, opprank, oppstat, teamscore, oppscore)
  SELECT  t.id, t.name, t.score, t.status,
          IF (m.team1=$teamid, m.score1, m.score2), 
          IF (m.team1=$teamid, m.score2, m.score1)
  FROM  ". TBL_MATCH ." m, l_team t
  WHERE   (m.team1=$teamid OR m.team2=$teamid)
    AND   IF (m.team1=$teamid, m.team2, m.team1 ) = t.id";
  if ($days != 0)
    $q = $q ." AND m.tsactual > DATE_SUB(NOW(), INTERVAL $days day)";

  $q=$q." ORDER BY t.id";
  sqlQuery ($q);

  return sqlQuery("SELECT oppid, oppname, opprank, oppstat,
    SUM(IF (teamscore>oppscore, 1, 0)) AS wins,
    SUM(IF (teamscore<oppscore, 1, 0)) AS losses,
    SUM(IF (teamscore=oppscore, 1, 0)) AS ties,
    COUNT(oppid) as total,
    AVG(IF (teamscore>oppscore, 100, 0)) AS ratio
  FROM  tsum
  GROUP BY oppid
  ORDER BY $sort $ascdesc");
}




function section_oppsumm(){

  $id = $_GET['id'];
  $days = $_GET['days'];
  if (isset($_GET['sort']) )
    $sort = $_GET['sort'];
  else
    $sort = DEFAULT_ORDER;

  $teamname = queryGetTeamName($id);

  section_oppsumm_doSelectForm ($id, $days, $sort);

  echo "<BR><CENTER><div class=contTitle>$teamname : ";
  if ($days==0)
    echo "ALL games";
  else  
    echo "last $days days";

  echo "</div><BR>
  <TABLE align=center border=0 cellspacing=0 cellpadding=3>
    <tr class=tabhead align=center>
    <td>Opponent</td><td>Opponent<BR>rank</td>
    <td>Games<BR>played</td><td colspan=3>W/L/T</td>
    <td>Win<br>ratio</td></tr>";

  $res = section_oppsumm_queryGetOppSum($_GET['id'], $days, $sort);

  $numGames=0;
  $line=0;
  while ( $row = mysql_fetch_object ($res)){
    if ($line++ % 2)
      $sty = "rowOdd";
    else 
      $sty = "rowEven";
    $x = intval ($row->ratio);

    $tlink = teamLink($row->oppname, $row->oppid, $row->oppstat);
    echo "<TR class=\"$sty\">
    <td>$tlink</td>
    <td>$row->opprank</td>
    <td>$row->total</td><td>$row->wins</td><td>$row->losses</td><td>$row->ties</td>
    <td align=right>$x%</td>
    </tr>";
    $numGames += $row->total;
  }


  echo "<TR><TD colspan=7><HR></td></tr>
    <TR><TD align=center colspan=7>Number of games shown: $numGames
    <TR><TD colspan=7><HR></td></tr>
    </td></tr></table>";

}


function section_oppsumm_doSelectForm ($id, $days, $sort){
echo '<TABLE align=center class=insetForm><TR><TD>';
  echo "<TABLE border=0 cellpadding=0 cellspacing=0><TR><TD>
  <form action=\"index.php\" name=none>
  <input type=hidden name=link value=oppsumm>
  <input type=hidden name=id value=$id>
  SORT:&nbsp;</td><TD>
  <select name=sort>";
    htmlOption ('total', 'Games Played', $sort);
    htmlOption ('ratio', 'Win Ratio', $sort);
    htmlOption ('oppname', 'Team Name', $sort);
    htmlOption ('opprank', 'Team Rank', $sort);
  echo "</select></td><TD width=12></td><td>
  Period:&nbsp; 
  </td><TD>
  <select name=days>";
    htmlOption (30, '30 days', $days);
    htmlOption (60, '60 days', $days);
    htmlOption (90, '90 days', $days);
    htmlOption (182, '6 months', $days);
    htmlOption (365, '1 year', $days);
    htmlOption (0, 'ALL', $days);
  echo '</select></td><td width=12></td><TD>'
  . htmlFormButSmall ('Show Me', '')
  .'</td></tr></table></td></tr></table></form>';
}


?>
