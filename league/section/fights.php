<?php // $Id$ vim:sts=2:et:sw=2


// Args:  id=teamID  (null means all matches)
//       days=#ofDays  (0 means all, null means default (30 days))
//       det=detail level
//
//  If $_SESSION['level']  ==  'admin', allow edit buttons


define (DEFAULTDAYS, 30);

# Isnt used?
#function section_fights_editLink ($matchid ){
# return htmlURLbutSmall ('EDIT', 'editmatch', "mid=$matchid", ADMBUT);   
#}

function section_fights (){
  //$_SESSION['new_match'] = 0;

  if( isset($_GET['days']) )
    $numdays =  $_GET['days'];
  else
    $numdays = DEFAULTDAYS;

  if( isset($_GET['id']) )
    $teamid = $_GET['id'];
  else
    $teamid = -1;

  if( isset($_GET['season_id']) && ENABLE_SEASONS)   {
    $season_id = $_GET['season_id'];
    $numdays = null;
  }
  else   {
    $season_id = -1;
  }

  if (isFuncAllowed('entermatch::edit_match') || isFuncAllowed('entermatch::delete_match'))
    $admin = true;

  if (isset($_GET['det']) )
    $detail = $_GET['det'];
  if (!isFuncAllowed('match_detail'))
    $detail=0;

  if ($admin)
    $utFreeze = section_fights_queryGetFreezeDate();

  $qwhere = '';
 
  if ($teamid>=0 || $numdays!=0 || $season_id>-1) {
    $qwhere = "WHERE ";
    $and = false;
    if ($teamid>=0)
      $qwhere = 'WHERE (l_team1.id = \'' . intval($_GET['id']) . '\' OR l_team2.id = \''. intval($_GET['id']) . '\') ';
    else
      $qwhere = "WHERE ";

	if ($numdays != 0) {
      if ($teamid>=0) $qwhere = $qwhere . "\nAND ";
      $qwhere = $qwhere . "tsactual > DATE_SUB(NOW(), INTERVAL $numdays day)";
    }

    if ($season_id>-1) {
      $qwhere = $qwhere . ($and? "\nAND ":" ") . "season = $season_id";
      $and = true;
    }
  }

  if ($detail == 0) {
    $res = sqlQuery($sql="SELECT matchtab.id, tsactual, team1, l_team1.name name1, score1, team2, 
        l_team2.name name2, score2, unix_timestamp(tsenter) as tsenter_unix,
      matchtab.id as matchid, l_team1.status stat1, l_team2.status stat2, season, matchtab.mlength
      FROM " . TBL_MATCH . " matchtab
      left join l_team l_team1 on l_team1.id = team1
      left join l_team l_team2 on l_team2.id = team2
      $qwhere ORDER BY tsactual desc");
  }else{
    $res = sqlQuery("SELECT matchtab.id, tsactual, team1, l_team1.name name1, score1, team2, 
        l_team2.name name2, score2, unix_timestamp(tsenter) as tsenter_unix,
        player1.callsign enteredby, tsenter, identer,
        player2.callsign editedby, tsedit, idedit, matchtab.id as matchid,
        oldrankt1, oldrankt2, newrankt1, newrankt2, l_team1.status stat1, l_team2.status stat2,
	matchtab.mlength, season
      FROM " . TBL_MATCH . " matchtab
      left join l_team l_team1 on l_team1.id = team1
      left join l_team l_team2 on l_team2.id = team2
      left join l_player player1 on player1.id = identer
      left join l_player player2 on player2.id = idedit
      $qwhere ORDER BY tsactual desc");
  }
  
  if ($season_id < 1)
    section_fights_pageStartRegular($teamid, $numdays, $detail);
  else
    section_fights_pageStartSeasonal($season_id);

  $show_mlength = (count(explode(',',VALID_MLENGTH)) > 1);

  $numGames = 0;
  $last_season = -1;
  while($obj = mysql_fetch_object($res)) {
    if(++$numGames %2)
      $cl = "rowOdd";
    else
      $cl = "rowEven";

    $style=null;

    if( isset($_SESSION['last_login']) && ($_SESSION['last_login'] < $obj->tsenter_unix ))
      $style = LINK_NEW;

    if (ENABLE_SEASONS && $season_id < 1 && $last_season != $obj->season) {
      $season = sqlQuerySingle("SELECT startdate,enddate FROM l_season WHERE id = $obj->season AND id > 1");
      if ($season)
        echo "<tr style='background-color:darkred;color:white;'><td>Season: </td><td colspan=7>$season->startdate - $season->enddate</td></tr>";
      else
        echo "<tr style='background-color:darkred;color:white;'><td colspan=8 align=\"center\">Off season</td></tr>";
      $last_season  = $obj->season;
    }
    echo "<tr class=\"$cl\"><td><a name=\"{$obj->id}\" href=\"#{$obj->id}\">".substr($obj->tsactual,0,-3).'</a>';
    if($show_mlength)
        print " [{$obj->mlength}] ";
    print '<td align=right><nobr>&nbsp;';
    echo teamLink ($obj->name1,  "$obj->team1", $obj->stat1, $style );
    echo '</td><TD>&nbsp;<b>vs.</b>&nbsp;</td><TD>';
    echo teamLink ($obj->name2,  "$obj->team2", $obj->stat2, $style );
    print '&nbsp</nobr><td align=center>'.$obj->score1.'</td><td>-</td><td align=center>'.$obj->score2.'&nbsp;</td>';

    print '<td>';
    if ($admin && (strtotime($obj->tsactual) > $utFreeze))
      echo htmlURLbutSmall ('EDIT', 'editmatch', "mid=$obj->matchid", ADMBUT);

    print "</td></tr>\n";

    if ($detail != 0){
      $enteredBy = '?';
      if ($obj->identer != 0)
        $enteredBy = $obj->enteredby;
      echo "<TR class=\"$cl\"><td></td><td class=\"dim\" colspan=5 align=left>
        * Entered AT: $obj->tsenter, BY: $enteredBy</td></tr>\n";
      if ($detail>1 && $obj->idedit!=0){
        echo "<TR class=\"$cl\"><td></td><td colspan=5 class=\"dim\" align=left>
        * Edited AT: $obj->tsedit, BY: $obj->editedby</td></tr>\n";
      }
      if ($detail>2 && $obj->oldrankt1!=0){
        echo "<TR class=\"$cl\"><td></td><td class=\"dim\" colspan=5 align=left>
          * $obj->name1 rank changed from $obj->oldrankt1 to $obj->newrankt1</td></tr>\n
          <TR class=\"$cl\"><td></td><td class=\"dim\" colspan=5 align=left>
          * $obj->name2 rank changed from $obj->oldrankt2 to $obj->newrankt2</td></tr>\n";
      }
    }
  }
  

  echo "<TR><TD colspan=8><HR></td></tr>
    <TR><td></td><TD colspan=2>Number of matches shown:</td><td>$numGames</td><td colspan=4></td></tr>
    <TR><TD colspan=8><HR></td></tr>
    </td></tr></table>";
  section_fights_pageEnd();
}

function section_fights_permissions() {
  return array(
    'match_detail' => 'View match details'
  );
}



function section_fights_doForm ($teamid, $numdays, $detail){
echo '<TABLE align=center class=insetForm><TR><TD>';

  echo "<TABLE border=0 cellpadding=0 cellspacing=0><TR valign=middle><TD>
  <form action=\"index.php\" name=none>
  <input type=hidden name=link value=fights>
  <input type=hidden name=id value=$teamid>
  Period:&nbsp;</td><TD>
  <select name=days>";
    htmlOption (15, '15 days', $numdays);
    htmlOption (30, '30 days', $numdays);
    htmlOption (60, '60 days', $numdays);
    htmlOption (90, '90 days', $numdays);
    htmlOption (182, '6 months', $numdays);
    htmlOption (365, '1 year', $numdays);
    htmlOption (0, 'ALL', $numdays);
  echo "</select><BR>
  </td><TD width=15></td>";
  if (isFuncAllowed('match_detail')){
    echo "<TD>Detail:&nbsp;</td><TD>
    <select name=det>";
      htmlOption (0, 'none', $detail);
      htmlOption (2, 'some', $detail);
      htmlOption (5, 'all', $detail);
    echo "</select><BR>
    </td><TD width=15></td>";
  }

  echo '<TD align=left>'. htmlFormButSmall ('Show me', '')
  .'</td></tr></table></td></tr></table></form>';
}


function section_fights_pageStartRegular($teamid, $numdays, $detail)
{
    section_fights_doForm ($teamid, $numdays, $detail);
    echo "<TABLE align=center><TR class=contTitle><TD colspan=4 align=center>Showing ";
    if ($numdays==0)
      echo "ALL matches:";
    else  
      echo "matches for the last $numdays days:";
    echo '</font></td></tr>';
    echo "<tr><td colspan=4>";
}

function section_fights_pageStartSeasonal($season_id)
{
    $now    = nowDateTime();
    $season = sqlQuerySingle("SELECT startdate,enddate FROM l_season WHERE id = $season_id");
    $season_next = 0;
    $season_prev = 0;
    $season_prev = sqlQuerySingle("select id from l_season where enddate < '$season->startdate' ORDER BY enddate desc limit 1");
    if ($season_prev)
      $season_prev = $season_prev->id;
    $season_next = sqlQuerySingle("select id from l_season where startdate > '$season->enddate' and startdate <= '$now' ORDER BY startdate asc limit 1");
    if ($season_next)
      $season_next = $season_next->id;

    echo "<TABLE align=center><TR class=contTitle>";
    if ($season_prev > 1)
      echo "<TD width=60px align=left>" . matchesLink($season_prev,"previous") ."</td>";
    else
      echo "<TD width=60px align=left></td>";
    echo "<TD>Showing matches for the ";
    if (strtotime($season->startdate) <= strtotime($now) && strtotime($season->enddate) >= strtotime($now))
      echo "current ";
    echo "season $season->startdate - $season->enddate</td>";
    echo "<TD width=60px align=right>&nbsp;" . seasonLink("season", $season_id,1) ."</td>";
    if ($season_next > 1)
      echo "<TD width=60px align=right>" . matchesLink($season_next,"next") ."</td>";
    else
      echo "<TD width=60px align=right></td>";
    echo '</tr>';
    echo "<tr><td colspan=4>";
}

function section_fights_pageEnd()
{
  echo "</td></tr></table>";
}

// returns 'rank freeze' timestamp as UnixTime
function section_fights_queryGetFreezeDate (){
  $res = sqlQuery ("select ts from bzl_freezeranks limit 1");
  if (($row = mysql_fetch_object ($res)) === false)
    return false;
  return strtotime ($row->ts);
}

?>
