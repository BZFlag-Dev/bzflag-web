<?php // $Id: standings.php,v 1.1 2006/08/29 00:29:50 dennismp Exp $ vim:sts=2:et:sw=2

define (FIELDS_SEASON,       'id,startdate,enddate,active');


// Args:  id=teamID  (null means all matches)
//       days=#ofDays  (0 means all, null means default (30 days))
//       det=detail level
//
//  If $_SESSION['level']  ==  'admin', allow edit buttons

function section_standings()
{  
  echo "<TABLE align=center cellpadding=1px><TR><TD valign=top align=centre>";
  section_standings_dispBest();
  echo "</td></tr></table>";
}

function section_standings_dispBest()
{
  $tacts = teamActivity (null, 45);

  //echo "<b><font size+2>Teams</font></b>";
  echo "<table align=center border=0 cellspacing=0 cellpadding=2>
      <tr class=tabhead valign=top align=center>
      <td>Pos.</td>
      <td>Team</td>
      <td>Leader</td>
      <td>Points</td>
      <td>1st</td>
      <td>2nd</td>
      <td>3rd</td>
      <td>Rating</td>
      <td>Activity</td>
      </tr>";

  $res = sqlQuery("SELECT t.id,t.name,t.status,t.leader,p.callsign,COUNT(DISTINCT s1.id) no1,COUNT(DISTINCT s2.id) no2,COUNT(DISTINCT s3.id) no3,COUNT(DISTINCT s1.id) * " . POINTS_SEASON_1 . " + COUNT(DISTINCT s2.id) * " . POINTS_SEASON_2 . " + COUNT(DISTINCT s3.id) * " . POINTS_SEASON_3 . " totalscore
                   FROM l_team t
                   left join l_season s1 on (s1.finished = 'yes' and s1.position1 = t.id and s1.seasontype ='league')
                   left join l_season s2 on (s2.finished = 'yes' and s2.position2 = t.id and s2.seasontype ='league')
                   left join l_season s3 on (s3.finished = 'yes' and s3.position3 = t.id and s3.seasontype ='league')
                   left join l_player p  on (p.id = t.leader)
                   WHERE t.status != 'deleted'
                   GROUP BY t.id
                   ORDER BY totalscore desc, no1 desc,no2 desc, t.score desc
                  ");

  $score_last = 0;
  $pos = 0;
  $row = 0;
  $won = 0;
  while($obj = mysql_fetch_object($res)) 
  {
    if(++$row %2)
      $cl = "rowOdd";
    else
      $cl = "rowEven";
    if ($obj->no1 > 0)
      $won++;
    $style=null;
    $pos++;
    echo "<tr class=\"$cl\">";
    echo "<td align=left>". (($score_last != $obj->totalscore)? ($pos .".") :"")   ."</td>";
    echo "<td align=left>". teamLink ($obj->name,  "$obj->id", $obj->status, null) ."&nbsp;&nbsp;</td>";
    echo "<td align=left>&nbsp;". playerLink ($obj->leader, $obj->callsign)  ."&nbsp;</td>";
    echo "<td align=right>&nbsp;&nbsp;&nbsp;". $obj->totalscore ."</td>";
    echo "<td align=right>&nbsp;&nbsp;&nbsp;". $obj->no1 ."&nbsp;</td>";
    echo "<td align=right>&nbsp;&nbsp;&nbsp;". $obj->no2 ."&nbsp;</td>";
    echo "<td align=right>&nbsp;&nbsp;&nbsp;". $obj->no3 ."&nbsp;</td>";
    echo "<td align=right>".displayRating($obj->id)."</td>";
    $act = sprintf ('%1.2f', $tacts[$obj->id]);
    echo "<TD align=center>$act</td>";

    echo "</tr>";
    $score_last = $obj->totalscore;
  }

  echo "<TR><TD colspan=9><HR></td></tr>
    <TR><TD align=left colspan=8>Number of teams:</td><TD align=right>$row</td></tr>
    <TR><TD align=left colspan=8>Number of teams that won a season:</td><TD align=right>$won</td></tr>
    <TR><TD colspan=9><HR></td></tr>
    <TR><TD align=center colspan=8><font size=-2>
    <b>Legend:</b> First position ".POINTS_SEASON_1." points,
    Second position ".POINTS_SEASON_2." points,
    Third position ".POINTS_SEASON_3." points</font></td></tr>    
    </td></tr>";
  echo "</table>";

}

function section_standings_permissions() {
  return array(
    'match_detail' => 'View match details'
  );
}
?>
