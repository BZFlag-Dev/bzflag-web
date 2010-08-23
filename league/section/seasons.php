<?php // $Id: seasons.php,v 1.2 2006/08/29 01:22:39 dennismp Exp $ vim:sts=2:et:sw=2

define (FIELDS_SEASON,       'id,startdate,enddate,active');


// Args:  id=teamID  (null means all matches)
//       days=#ofDays  (0 means all, null means default (30 days))
//       det=detail level
//
//  If $_SESSION['level']  ==  'admin', allow edit buttons

function section_seasons()
{  
  echo "<TABLE align=center cellpadding=1px><TR><TD>";
  section_seasons_dispSeasons();
  echo "</td></tr></table>";
}

function section_seasons_dispSeasons()
{  
  $now = nowDate();

  if( isset($_GET['detail_level']) )
    $detail_level =  $_GET['detail_level'];
  else
    $detail_level = 1;

  if ($detail_level == 3)
    $team_span = 6;
  else
    $team_span = 3;

  section_seasons_doForm($detail_level);
  echo "<table align=center border=0 cellspacing=0 cellpadding=2>
      <tr class=tabhead valign=top align=center>
      <td colspan=3>Season</td>
      <td colspan=$team_span>1st</td>
      <td colspan=$team_span>2nd</td>
      <td colspan=$team_span>3rd</td>
      " . ($detail_level == 2 ? "<td colspan=3>Most&nbsp;Active</td>":"") . 
      "<td></td></tr>";

  $res = sqlQuery("SELECT s.id, s.identer, s.idchange, s.startdate, s.enddate, s.finished,s.active,
                          s.position1, s.score1,t1.name team1,t1.status status1,ts1.zelo zelo1,ts1.matches matches1,ts1.won won1,ts1.lost lost1,ts1.draw draw1,
                          s.position2, s.score2,t2.name team2,t2.status status2,ts2.zelo zelo2,ts2.matches matches2,ts2.won won2,ts2.lost lost2,ts2.draw draw2,
                          s.position3, s.score3,t3.name team3,t3.status status3,ts3.zelo zelo3,ts3.matches matches3,ts3.won won3,ts3.lost lost3,ts3.draw draw3,
                          s.mostactive,         t4.name team4,t4.status status4,ts4.zelo zelo4,ts4.matches matches4,ts4.won won4,ts4.lost lost4,ts4.draw draw4,
                          s.mvp,count(distinct m.id) matches
                   from  l_season s
                    left join l_team t1 on t1.id = s.position1
                    left join l_team t2 on t2.id = s.position2
                    left join l_team t3 on t3.id = s.position3
                    left join l_team t4 on t4.id = s.mostactive
                    left join bzl_match m on m.season = s.id
                    left join l_teamscore ts1 on (ts1.season = s.id and ts1.team = s.position1)
                    left join l_teamscore ts2 on (ts2.season = s.id and ts2.team = s.position2)
                    left join l_teamscore ts3 on (ts3.season = s.id and ts3.team = s.position3)
                    left join l_teamscore ts4 on (ts4.season = s.id and ts4.team = s.mostactive)
                    WHERE NOT (s.position1 IS NULL)
                    GROUP BY s.id
                    ORDER BY s.startdate desc");


  $row = 0;
  if ($res) while($obj = mysql_fetch_object($res)) {
    if(++$row %2)
      $cl = "rowOdd";
    else
      $cl = "rowEven";

    $style=null;
    echo "<tr class=\"$cl\">";
    if ($detail_level == 1)
    {
      echo "<td align=left>". seasonLink($obj->startdate,$obj->id) ."</td>";
      echo "<td align=centre> - </td>";
    }
    else
    {
      echo "<td align=right colspan=2></td>";
    }
    echo "<td align=right>". seasonLink($obj->enddate,$obj->id) ."</td>";
    if ($obj->team1)
    {
      echo "<td align=left style='white-space:nowrap;'>&nbsp;&nbsp;". teamLink ($obj->team1,  "$obj->position1", $obj->status1, null) ."</td>";
      echo "<td align=right>&nbsp;". $obj->score1 . "</td>";
      echo "<td align=right>(". $obj->matches1 . ")</td>";
      if ($detail_level == 3)
      {
        echo "<td align=right>&nbsp;". $obj->won1 . "</td>";
        echo "<td align=right>/". $obj->lost1 . "</td>";
        echo "<td align=right>/". $obj->draw1 . "&nbsp;</td>";
      }
    }
    else
      echo "<td align=center colspan=6 width=60px>&nbsp;&mdash;&nbsp;</td>";
    if ($obj->team2)
    {
      echo "<td align=left style='white-space:nowrap;'>&nbsp;&nbsp;". teamLink ($obj->team2,  "$obj->position2", $obj->status2, null) ."</td>";
      echo "<td align=right>&nbsp;". $obj->score2 . "</td>";
      echo "<td align=right>(". $obj->matches2 . ")</td>";
      if ($detail_level == 3)
      {
        echo "<td align=right>&nbsp;". $obj->won2 . "</td>";
        echo "<td align=right>/". $obj->lost2 . "</td>";
        echo "<td align=right>/". $obj->draw2 . "&nbsp;</td>";
      }
    }
    else
      echo "<td align=center colspan=6 width=60px>&nbsp;&mdash;&nbsp;</td>";
    if ($obj->team3)
    {
      echo "<td align=left style='white-space:nowrap;'>&nbsp;&nbsp;". teamLink ($obj->team3,  "$obj->position3", $obj->status3, null) ."</td>";
      echo "<td align=right>&nbsp;". $obj->score3 . "</td>";
      echo "<td align=right>(". $obj->matches3 . ")</td>";
      if ($detail_level == 3)
      {
        echo "<td align=right>&nbsp;". $obj->won3 . "</td>";
        echo "<td align=right>/". $obj->lost3 . "</td>";
        echo "<td align=right>/". $obj->draw3 . "&nbsp;</td>";
      }
    }
    else
      echo "<td align=center colspan=6 width=60px>&nbsp;&mdash;&nbsp;</td>";
    if ($detail_level == 2)
    {
      if ($obj->team4)
      {
        echo "<td align=left style='white-space:nowrap;'>&nbsp;&nbsp;". teamLink ($obj->team4,  "$obj->position4", $obj->status4, null) ."</td>";
        echo "<td align=right>&nbsp;(" . $obj->matches4 .")</td>";
        echo "<td align=right>&nbsp;</td>";
      }
      else
        echo "<td align=center colspan=6 width=60px>&nbsp;&mdash;&nbsp;</td>";
    }
    echo "<td align=left></td>";
    echo "</tr>";
  }

  if ($row == 0)
  {
    echo "<TR><TD colspan=28><HR></td></tr>
    <TR><TD colspan=28>There are no seasons played yet.</td></td></tr>
    <TR><TD colspan=28><HR></td></tr>
    </td></tr>";
  }
  /*echo "<TR><TD colspan=10><HR></td></tr>
    <TR><TD colspan=2></td><TD align=left colspan=5>Number of active teams in this season:</td><TD align=right>$row</td><TD colspan=3></td></tr>
    <TR><TD colspan=2></td><TD align=left colspan=5>Number of matches in this season:</td><TD align=right>$matches</td><TD colspan=3></td></tr>
    <TR><TD colspan=10><HR></td></tr>
    </td></tr>";
*/
  echo "</table>";
}

function section_seasons_doForm ($detail_level)
{
  echo '<TABLE align=center class=insetForm><TR><TD>';
  echo "<TABLE border=0 cellpadding=0 cellspacing=0><TR valign=middle><TD>
  <form action=\"index.php\" name=none>
  <input type=hidden name=link value=seasons>
  Details:&nbsp;</td><TD>
  <select name=detail_level>";
    htmlOption (1, 'Normal', $detail_level);
    htmlOption (2, 'Include most active team', $detail_level);
    htmlOption (3, 'Match details (W/L/T)', $detail_level);
  echo "</select>
  </td><TD width=15></td>";
  echo '<TD align=left>'. htmlFormButSmall ('Show me', '')
  .'</td></tr></table></td></tr></table></form>';
}

function section_seasons_permissions() {
  return array(
    'match_detail' => 'View match details'
  );
}
?>
