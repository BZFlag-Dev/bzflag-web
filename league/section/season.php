<?php // $Id: season.php,v 1.1 2006/08/29 00:29:50 dennismp Exp $ vim:sts=2:et:sw=2

//    The score is calculated as:
//    win * POINTS_WIN + draw * POINTS_DRAW + lost * POINTS_LOSS
//    This field is also used in QUERY_RANKINGORDER to determin
//    the ranking order of the league.
define (FIELD_SCORE,          '(ts.won * s.points_win + ts.draw *  s.points_draw + ts.lost * s.points_lost)');

//    The best team is first the one with the best overall score,
//    which is calculated out of the wins, lost and draw fields
//    Note that the actual score is never saved, but calculated
//    from the values in config.php (see FIELD_SCORE).
//    For two teams with the same score, the next value considered
//    is their zelo improvement during the season. Meaning that 
//    those teams with the hardest matches will win.
//    If this is the same (very unlikely) then the numbers of matches
//    won, draws and as the last, the total amount of matches.
//    NOTE: if you change this, also change in addseason.php
define (QUERY_RANKINGORDER,   'ORDER BY tscore desc, ts.zelo desc, ts.won desc, ts.draw desc, ts.matches asc');

// Args:  id=teamID  (null means all matches)
//       days=#ofDays  (0 means all, null means default (30 days))
//       det=detail level
//
//  If $_SESSION['level']  ==  'admin', allow edit buttons

function section_season(){


  $season_next = 0;
  $season_prev = 0;

  $season = null;
  if( isset($_GET['season_id']) )
  {
    $season_id =  $_GET['season_id'];
    $season = sqlQuerySingle("select * from l_season where id = $season_id");
  }
  else
  {
    $now = nowDateTime();
                         $season = sqlQuerySingle("select * from l_season where startdate <= '$now' and fdate >= '$now'");
    if ($season == null) $season = sqlQuerySingle("select * from l_season where active = 'yes' and id > 1");
    if ($season == null) $season = sqlQuerySingle("select * from l_season where enddate <= '$now' and id > 1 order by enddate desc limit 1");
    if ($season) 
      $season_id = $season->id;
    else
      $season_id = 0;
  }
  
  $now    = nowDateTime();
  if ($season_id > 1)
  {
    $res = sqlQuerySingle("select id from l_season where enddate < '$season->startdate' ORDER BY enddate desc limit 1");
    if ($res)
      $season_prev = $res->id;
    $res = sqlQuerySingle("select id from l_season where startdate > '$season->enddate' and startdate <= '$now' ORDER BY startdate asc limit 1");
    if ($res)
      $season_next = $res->id;
  }
  
  $res = sqlQuery("SELECT ts.team, " . FIELD_SCORE . " tscore, ts.won,ts.lost,ts.draw, ts.zelo szelo,ts.matches,
                          team.name, team.leader,team.score gzelo,team.status,
                          p.callsign
                   FROM (l_teamscore ts, l_season s)
                    left join l_team team on team.id = ts.team
                    left join l_player p on p.id = team.leader
                   WHERE ts.season = $season_id
                   AND   s.id = $season_id
                   GROUP BY ts.team
                   " . QUERY_RANKINGORDER);
                   
  $matches = sqlQuerySingle("SELECT count(id) matches FROM bzl_match WHERE season = $season_id");
  $matches = $matches->matches;
  //section_fights_doForm ($teamid, $numdays, $detail);

  echo "<TABLE align=center><TR class=contTitle>";

  if ($season_prev > 1)
    echo "<TD width=60px align=left>" . seasonLink("previous",$season_prev,1) ."</td>";
  else
    echo "<TD width=60px align=left></td>";

  if ($season->active=='yes')
    $tacts = teamActivity (null, 45);
 
  if ($res && $season)
  {
  
    echo "<TD>Showing ";
    if (strtotime($season->startdate) <= strtotime($now) && strtotime($season->fdate) >= strtotime($now))
      echo "Ladder for current season " . $season->startdate . " - " . $season->enddate;
    else
      echo "Ladder for season " . $season->startdate . " - " . $season->enddate;
    echo '</td><td width=60px align=right>&nbsp;' . matchesLink($season_id) . '</td>';
  }
  else  
    echo "<td colspan=2>No season to display</td>";
  if ($season_next > 1)
    echo "<TD width=60px align=right>" . seasonLink("next",$season_next,1) ."</td>";
  else
    echo "<TD width=60px align=right></td>";
 
  echo '</tr>';
  echo "<tr><td></td><td align=center><font size=-1>Win=$season->points_win points&nbsp;&nbsp;Loss=$season->points_lost points&nbsp;&nbsp;Tie=$season->points_draw points</font></td><td></td></tr>";
  echo '<tr><td colspan=4>';

  if (!$res)
    return;
  echo "<table align=center border=0 cellspacing=0 cellpadding=2 width=100%>
      <tr class=tabhead valign=top align=center><td>Pos.</td><td>Team</td><td>Leader</td><td>#</td><td>Score</td><td>W/L/T</td><td colspan=2>Rating</td>" . ($tacts?"<td>Activity</td>":"<td></td>") . "<td></td></tr>
      <tr class=tabhead valign=top align=center><td colspan=6><td><font size=-2>season</font></td><td><font size=-2>overall</font></td><td colspan=2></td></tr>";

  $score_last = 0;
  $pos = 0;
  $show_pos = 0;
  $row = 0;
  while($obj = mysql_fetch_object($res)) {
    if(++$row %2)
      $cl = "rowOdd";
    else
      $cl = "rowEven";
    $pos++;
    $style=null;
    echo "<tr class=\"$cl\">";
    echo "<td align=left>";
    if( $score_last != $obj->tscore) {
      $show_pos = $pos;
      echo "$show_pos.";
    }
    else {
      echo "<font color=\"grey\">$show_pos</font>";
    }

    echo "</td>";
    echo "<td align=left>". teamLink ($obj->name,  "$obj->team", $obj->status, null) ."&nbsp;&nbsp;</td>";
    echo "<td align=left>". playerLink ($obj->leader, $obj->callsign)  ."</td>";
    echo "<td align=right>". $obj->matches ."&nbsp;</td>";
    echo "<td align=right>". $obj->tscore ."</td>";
    echo "<td align=right>{$obj->won}/{$obj->lost}/{$obj->draw}&nbsp;</td>";
    echo "<td align=right>". $obj->szelo ."&nbsp;</td>";
    echo "<td align=left>". displayRating($obj->team). "&nbsp;</td>";
    if ($tacts)
    {
      $act = sprintf ('%1.2f', $tacts[$obj->team]);
      echo "<TD align=center>$act</td>";
    }
    else
      echo "<TD align=center></td>";
    echo "<td align=left></td>";
    echo "</tr>";
    $score_last = $obj->tscore;
  }

  echo "<TR><TD colspan=13><HR></td></tr>
    <TR><TD colspan=2></td><TD align=left colspan=5>Number of active teams in this season:</td><TD align=right>$row</td><TD colspan=6></td></tr>
    <TR><TD colspan=2></td><TD align=left colspan=5>Number of matches in this season:</td><TD align=right>$matches</td><TD colspan=6></td></tr>
    <TR><TD colspan=13><HR></td></tr>
    </td></tr></table>";
  echo '</td></tr></table><p>';

}

function section_season_permissions() {
  return array(
    'match_detail' => 'View match details'
  );
}
?>
