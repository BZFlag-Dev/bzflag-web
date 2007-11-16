<?php // $Id$ vim:sts=2:et 

define (DUP_CHECK_DAYS, 3);
define (DAY_SECS, 60*60*24);

/*** args:
state:  null or 0  : initial
        1 : post request
        2 : post, already warned  (this should be post i/o get !!)
teama, teamb, scorea, scoreb, dte, tme, mlen valued if state==1 or 2
mid: valued if this is an edit of an existing match record
del: submit / delete match
***/


//function dispUnix ($unixtime){
//  return gmdate ('Y-m-d H:i:s', $unixtime) . ' GMT';
//}


function section_entermatch_permissions() {
  return array(
    'enter_match'   => 'Enter new matches',
    'edit_match'    => 'Edit an existing match',
    'delete_match'  => 'Delete an existing match'
  );
}

function section_entermatch_durations() {
	$mlens = explode(',',VALID_MLENGTH);
	if( $mlens == 0 ) 
		return array(30);
	else 
		return $mlens;
}

function section_entermatch_checkMaintenance(){
  if (!($row  = mysql_fetch_object (sqlQuery("select text from bzl_siteconfig where name='maintenance'"))))
    return;
  $last = strtotime($row->text);
  if ($last<0 || (mktime() - $last) > 24*60*60){
    require_once ('lib/maintfuncs.php');
    // it's time to run maintenance ...
    site_maintenance ('invoked during match entry', false );
    echo '<BR><font size=-1>(Site Maintenance performed)</font><BR>';
  }
}

function section_entermatch (){
  require_once('lib/common.php');
  $vars = array('state','teama','teamb','scorea','scoreb','dte','tme','mid','del','mlen');
  foreach($vars as $var)
    $$var = isset($_POST[$var]) ? $_POST[$var] : $_GET[$var];

  if (!isFuncAllowed ('enter_match'))
    return errorPage ('You are not authorized to enter a match result');
  if ($mid){
    if ($del && !isFuncAllowed ('delete_match'))
      return errorPage ('You are not authorized to delete a match result');
    if (!$del && !isFuncAllowed ('edit_match'))
      return errorPage ('You are not authorized to edit a match result');
  } 

  echo '<BR><CENTER>';
  if($state==1 || $state==2) {
    $tsActUnix = dt2Unix ($dte, $tme);
    $tsFreeze = section_entermatch_queryGetFreezeDate ();
    if($teama == $teamb) {
      section_entermatch_dispErr ('Teams are the same');
    } else if(($scorea == "")||($scoreb == "")) {
      section_entermatch_dispErr ('You must enter both scores');
    } else if(($scorea<0)||($scoreb<0)) {
      section_entermatch_dispErr ('Negative scores are not allowed');
    } else if( !is_numeric($scorea) || !is_numeric($scoreb)) {
      section_entermatch_dispErr ('Invalid score entered');
    } else if ($tsActUnix === -1) {     
      section_entermatch_dispErr ('Invalid date and/or time');
    } else if ( $tsActUnix < $tsFreeze){
      $disp = date ('Y-m-d H:i', $tsFreeze);
      section_entermatch_dispErr ("Invalid date/time. Dates prior to $disp are invalid");
    } else if ( $tsActUnix > gmTime()){
      section_entermatch_dispErr ("Specified date/time is in the future!");
    } else if ( ! in_array($mlen, section_entermatch_durations() )) {
      section_entermatch_dispErr ("Invalid match length!");
    } else {

      if ($state==1){
        if ($del){
          section_entermatch_dispMatch ('Are you sure that<BR> you want to<BR> DELETE this match ?', $teama, $teamb, $scorea, $scoreb, $tsActUnix, $mlen);
          section_entermatch_confirmForm ($teama, $teamb, $scorea, $scoreb, $dte, $tme, $mid, $del, $mlen);
          return;
        }

        $warns=array();
        if (!$mid){
          if ($row = section_entermatch_queryCheckDup ($teama, $teamb, $scorea, $scoreb, DUP_CHECK_DAYS)){
            $ts = strtotime ($row->tsactual);
            section_entermatch_dispMatch ('Possible Duplicate:', $teama, $teamb, $scorea, $scoreb, $ts, $mlen);
            $warns[] = 'Possible DUPLICATE post!';
          }
          if ($tsActUnix < section_entermatch_queryLastMatchTime ())
            $warns[] = 'The time entered is before the last match in the database.  
            This MAY cause previously calculated team ratings to change.';
          if (($tsActUnix + (2*DAY_SECS)) < gmTime())
            $warns[] = 'Match time is more than 48 hours ago';
        }

        if(($scorea+$scoreb)>30)
          $warns[] = 'Are you sure about that score?';
        
        section_entermatch_dispWarns ($warns);
        section_entermatch_dispMatch ('You Entered :', $teama, $teamb, $scorea, $scoreb, $tsActUnix, $mlen);
        section_entermatch_confirmForm ($teama, $teamb, $scorea, $scoreb, $dte, $tme, $mid, $del, $mlen);
        return;
      }
      
      if ($mid)
        section_entermatch_editIt ($mid,$teama,$teamb,$scorea,$scoreb, $tsActUnix, $del, $mlen);
      else
        section_entermatch_postIt ($teama,$teamb,$scorea,$scoreb, $tsActUnix, $mlen);
      return;
    }
  }


  if ($mid){    // this is an edit of an existing match
    if (! ($row = section_entermatch_queryGetMatch($mid))){
      echo "That match id can't be found!<BR>";
      return;
    }
    $teama = $row->team1;
    $teamb = $row->team2;
    $scorea = $row->score1;
    $scoreb = $row->score2;
    $ut = strtotime ($row->tsactual);
    $dte = date ('Y-m-d', $ut);
    $tme = date ('H:i:s', $ut);
    $mlen = $row->mlength;
  }

  // present entry/edit form ....
  section_entermatch_editForm ($mid, $teama, $teamb, $scorea, $scoreb, $dte, $tme, $mlen);
}




function section_entermatch_confirmForm ($teamA, $teamB, $scoreA, $scoreB, $dte, $tme, $mid, $del, $mlen){
  // display confirm / cancel form      
  echo "<TABLE cellpadding=5><TR><TD>
      <form method=post action=\"index.php\"><TR><TD>
      <input type=hidden name=link value=entermatch>
      <input type=hidden name=state value=2>
      <input type=hidden name=mid value=\"$mid\">
      <input type=hidden name=teama value=\"$teamA\">
      <input type=hidden name=teamb value=\"$teamB\">
      <input type=hidden name=scorea value=\"$scoreA\">
      <input type=hidden name=scoreb value=\"$scoreB\">
      <input type=hidden name=dte value=\"$dte\">
      <input type=hidden name=mlen value=\"$mlen\">
      <input type=hidden name=del value=\"$del\">
      <input type=hidden name=tme value=\"$tme\">";
      echo snForm();
      echo htmlFormButton ('CONFIRM', '');
      echo "</form>
    </td><TD>
      <form method=post action=index.php>
      <input type=hidden name=link value=entermatch>
      <input type=hidden name=teama value=\"$teamA\">
      <input type=hidden name=teamb value=\"$teamB\">
      <input type=hidden name=scorea value=\"$scoreA\">
      <input type=hidden name=scoreb value=\"$scoreB\">
      <input type=hidden name=mlen value=\"$mlen\">
      <input type=hidden name=dte value=\"$dte\">
      <input type=hidden name=tme value=\"$tme\">
      <input type=hidden name=mid value=\"$mid\">";
      echo snForm();
      echo htmlFormButton ('Cancel', '', CLRBUT)
      .'</form>
    </td></tr></table><BR>';
}

function section_entermatch_editIt ($mid,$teamA,$teamB,$scoreA,$scoreB, $tsActUnix, $del, $mlen){
  snCheck('fights');
  section_entermatch_orderResults (&$teamA, &$teamB, &$scoreA, &$scoreB);
  if (!($old = section_entermatch_queryGetMatch ($mid))){
    echo '<CENTER>That match does not exist<BR>';
    return;
  }
  $affected_dates = array();
  if ($del){
    sqlQuery ("UPDATE l_team SET matches = matches - 1 WHERE id IN($old->team1,$old->team2)");
    sqlQuery ("delete from ". TBL_MATCH ." where id = $mid"); 
    section_entermatch_dispMatch ('Deleted :', $teamA, $teamB, $scoreA, $scoreB, $tsActUnix, $mlen);
    $affected_dates[] = $old->tsactual;
  } else {
    $now = gmdate("Y-m-d H:i:s");
    $pid = $_SESSION['playerid'];
    $tsActStr = date ("Y-m-d H:i:s", $tsActUnix);
    if ($old->team1==$teamA && $old->team2==$teamB && $old->score1==$scoreA
    && $old->score2==$scoreB && strtotime($old->tsactual)==$tsActUnix && $old->mlength==$mlen){
      echo "<CENTER>No changes made!<BR>";
      return;   
    }
    sqlQuery ("UPDATE l_team SET matches = matches - 1 WHERE id IN($old->team1,$old->team2)");
    sqlQuery ("UPDATE l_team SET matches = matches + 1 WHERE id IN($teamA,$teamB)");
    sqlQuery ("update ". TBL_MATCH ." 
      set team1=$teamA, team2=$teamB, score1=$scoreA, score2=$scoreB,
      idedit=$pid, tsactual='$tsActStr', tsedit='$now', mlength='$mlen' where id = $mid");
    section_entermatch_dispMatch ('Changed from:', $old->team1, $old->team2, $old->score1, $old->score2, strtotime($old->tsactual), $old->mlength);
    section_entermatch_dispMatch ('&nbsp;&nbsp;Changed to:', $teamA, $teamB, $scoreA, $scoreB, $tsActUnix, $mlen);

    if (ENABLE_SEASONS) {
      $affected_dates[] = date ("Y-m-d H:i:s", $tsActUnix);
      if ($old->tsactual && $old->tsactual != $tsActStr)
        $affected_dates[] = $old->tsactual;
     }
  }
  section_entermatch_recalcAllRatings(&$affected_dates);
  foreach( $affected_dates as $d ) 
      section_entermatch_recalcSeasonRatingsByDate( $d );
  echo '<BR>'. htmlURLbutton ('back to matches', 'fights');
}

function section_entermatch_postIt ($teamA,$teamB,$scoreA,$scoreB, $tsActUnix,$mlen){
  snCheck('entermatch');
  section_entermatch_orderResults (&$teamA, &$teamB, &$scoreA, &$scoreB);
  $rowA = queryGetTeam ($teamA);
  $rowB = queryGetTeam ($teamB);
  section_entermatch_calculateRating ($scoreA, $scoreB, $rowA->score, $rowB->score, &$newA, &$newB);

  // Insert data into MATCH table
  $tsActStr = date ("Y-m-d H:i:s", $tsActUnix);
  $now = gmdate("Y-m-d H:i:s");
  $s_playerid = $_SESSION['playerid'];
  $season_id = 1;
  if( ENABLE_SEASONS ) {
    $res = sqlQuery($sql="SELECT id FROM l_season WHERE '$tsActStr' >= startdate AND '$tsActStr'  <= enddate ORDER BY id DESC LIMIT 1");
    if( $res ) {
      $row = mysql_fetch_assoc($res);
      if( $row['id'] ) 
          $season_id = $row['id'];
    }
  }

  sqlQuery("insert into ". TBL_MATCH ."
       (team1, score1, team2, score2, tsactual, identer, tsenter, 
          oldrankt1, oldrankt2, newrankt1, newrankt2, mlength, season) 
       values($teamA, $scoreA, $teamB, $scoreB, '$tsActStr', $s_playerid, '$now',
                  $rowA->score, $rowB->score, $newA, $newB,'$mlen',$season_id)");
  sqlQuery ("UPDATE l_team SET matches = matches + 1 WHERE id IN($teamA,$teamB)");

  if ($tsActUnix < section_entermatch_queryLastMatchTime ()){
     section_entermatch_recalcAllRatings();
  } else {
    section_entermatch_rankupStart();
    section_entermatch_rankupTran($teamA, $rowA->name, $rowA->score, $newA);
    section_entermatch_rankupTran($teamB, $rowB->name, $rowB->score, $newB);
    section_entermatch_rankupEnd();
  }
  if( ENABLE_SEASONS ) {
    section_entermatch_rankupStart();
    section_entermatch_recalcSeasonRatings($season_id);
    section_entermatch_rankupEnd();
  }

  echo '<BR><form><input type=hidden name=link value=entermatch>'. htmlFormButton ('Enter Another', null) .'</form>';
  section_entermatch_checkMaintenance();
}

function section_entermatch_rankupStart (){
    echo '<table border=0 cellspacing=0>
          <tr><td colspan=4><font class="feedback">Update team ranking</font></td></tr>
          <tr class=tabhead align=right><td valign=bottom align=center>Team</td><td></td>
          <td>OLD<BR>&nbsp;Rating</td><td>NEW<BR>&nbsp;Rating</td></tr>';

#    echo '<CENTER><font class="feedback"><BR>Team ratings updated:</font><BR><table border=0 cellspacing=0>
#      <tr class=tabhead align=right><td valign=bottom align=center>Team</td>
#      <td>OLD<BR>&nbsp;Rating</td><td>NEW<BR>&nbsp;Rating</td></tr>';
}

function section_entermatch_rankupTran ($teamId, $teamName, $old, $new){
    echo "<tr align=right><td>$teamName</td><td></td><td>$old</td><td>$new</td></tr>";
    sqlQuery("update l_team SET score='$new', active='yes' where id = '$teamId'");
}

function section_entermatch_rankupEnd (){
  echo '</table>';
}



//TODO: Style for error box
function section_entermatch_dispErr ($msg){
  echo "<CENTER><TABLE bgcolor=#cccccc border=1 cellpadding=5><TR><TD align=center>
        <font color=#660000 size=+1><B>ERROR<BR>$msg</b><BR>Try Again.</font></td></tr></table></center><BR>";
}

function section_entermatch_dispWarns (&$wa){
  if (($nw = count($wa)) <= 0)
    return;
  if ($nw > 1)
    $s="s";
  echo "<CENTER><TABLE width=60% border=1 cellpadding=3 class=\"warnbox\"><TR><TD>
        <TABLE align=center class=\"warnbox\" border=0><TR><TD colspan=2 align=center>
        <font><B style=\"text-decoration: blink\">WARNING$s:</b></font></td></tr>";
  for ($x=0; $x<$nw; $x++)
    echo "<TR style=\"warnbox\"><TD valign=top>*&nbsp;</td><TD>$wa[$x]</tr></tr>";
  echo  "</table></tr></td></table><p>";        
}

function section_entermatch_dispMatch ($msg, $teama, $teamb, $scorea, $scoreb, $tsu, $mlen){
  echo "<TABLE align=center cellspacing=10><TR>
        <TD align=right><font class=feedback>$msg</font></td><td>";
  $dte = date ('Y-m-d', $tsu);
  $tme = date ('H:i:s', $tsu);
  echo "<div class='outbox'>
    <TABLE border=0 cellpadding=0 cellspacing=0>
      <TR><td colspan=2 align=center>$dte $tme ($mlen mins)<BR><HR width=90%></td></tr>
    <TR><TD align=right>$scorea:&nbsp;&nbsp;</td>
      <TD align=left><nobr>". queryGetTeamName($teama) ."</nobr></td></tr>
    <TR><TD align=right>$scoreb:&nbsp;&nbsp;</td><TD align=left><nobr>" .
      queryGetTeamName($teamb) .'</nobr></td></tr></table></div>
    </td></tr></table>';
}

// create 'select list' of teams ...
$teamlist = null;
function section_entermatch_htmlTeamSelect ($selname, $selected){
  global $teamlist;
  if (!$teamlist){
    $teamlist = array();
    $class = '';
    $res = sqlQuery("select T.id, T.name, active='yes' break ,  count(P.team) as teamsize
        from l_team T, l_player P
        where T.status != 'deleted' and P.team=T.id
        group by P.team 
        order by break desc, T.name");
    while($row = mysql_fetch_object($res)) {
      if ($row->teamsize > 1){
        if( $row->break==0 && !$class )
          $class = 'inactsel';
        $teamlist[] = array ($row->id, $row->name, $class  );
      }
    }
  }
  echo "<select name=$selname>";
  foreach ($teamlist as $team)    
    htmlOption ($team[0], $team[1], $selected, $team[2]);
  echo '</select>';
}

function section_entermatch_editForm ($mid, $teama, $teamb, $scorea, $scoreb, $dte, $tme, $mlen){
  // enter form follows ....
  echo "<center><TABLE width=200><TR><TD align=center>
    <TABLE cellpadding=5><TR><TD align=center>TEAM</td><TD>Score</td></tr>
      <form method=post action=\"index.php\"><TR><TD>
      <input type=hidden name=link value=editmatch>
      <input type=hidden name=state value=1>
      <input type=hidden name=mid value=$mid>";
      echo snFormInit ();
      
      section_entermatch_htmlTeamSelect('teama', $teama);
      echo "</td><TD><input size=3 maxlength=3 value=\"$scorea\" name=scorea></td></tr><TR><TD>";
      section_entermatch_htmlTeamSelect("teamb", $teamb);
      echo "</td><TD><input size=3 maxlength=3 value=\"$scoreb\" name=scoreb></td></tr></table>";
    if ($dte == '')
      $dte = gmdate ('Y-m-d');
    if ($tme == '')
      $tme = gmdate ('H:i:s');
    echo "<BR><HR>
      <TABLE><TR><TD align=right>
      <nobr>DATE: <input type=text name=dte size=10 maxlength=10 value=\"$dte\"></nobr>
      <p>
    <nobr>TIME: <input type=text name=tme size=10 maxlength=8 value=\"$tme\"></nobr>
    </td><TD>&nbsp;</td><TD valign=top><nobr>Actual date/time of match (GMT).</nobr><BR>Chronological ";
    $mlens =  section_entermatch_durations();
    if( count($mlens) == 1 ) {
       print "<input type=hidden name=mlen value=" . $mlens[0] . ">";
    }
    else { 
      print "<p> <nobr>LENGTH: ";
      print "<select name=\"mlen\">";
      foreach($mlens as $duration) {
        htmlOption($duration, "$duration mins", $mlen);
      }
      print "</select>";
    }
    echo "</td><TD>&nbsp;</td><TD valign=top><nobr>Actual date/time of match (GMT).</nobr><BR>Chronological 
        order of matches can affect team rankings!";    
    echo '</td></tr></table><TR><TD align=center><HR><TABLE align=center><TR><TD>';

    if ($mid){
      echo htmlFormButton('&nbsp;EDIT&nbsp;', '') . '</td>';
      if (isFuncAllowed('delete_match'))
        echo '<TD width=8></td><TD>'. htmlFormButton('DELETE', 'del') . '</td>';
    }else{
      echo htmlFormButton ('Enter', '').'</td>';
    }
    echo '<TD width=8></td><TD>' .htmlFormReset ('Reset').'

  </td></tr></table></form>
  </td></tr></table><p><p>';

}

// assert that teama is the winner
function section_entermatch_orderResults (&$teama, &$teamb, &$scorea, &$scoreb){
  if ($scorea<$scoreb){
    $temp = $scorea;
    $scorea = $scoreb;
    $scoreb = $temp;
    $temp = $teama;
    $teama = $teamb;
    $teamb = $temp;
  }
}

function section_entermatch_calculateRating ($scoreA, $scoreB, $oldA, $oldB, &$newA, &$newB){
  if ($scoreA > $scoreB)
    $score = 1;
  elseif ($scoreA < $scoreB)
    $score = -1;
  else $score = 0;

  $factor = 50.0;
  $S = 1.0 / (1 + pow(10.0, ($oldB - $oldA) / 400.0));
  $Sc = ($score + 1) / 2.0;
  $diff = abs($factor * ($Sc - $S));
  if($diff < 1)
    $diff = 1;
  $d = floor($diff + 0.5);
  if($Sc - $S < 0)
    $d = -$d;
  if($Sc - $S == 0)
    $d = 0;
  $newA = $oldA + $d;
  $newB = $oldB - $d;
}

/*** Re-calculate all team's ratings - present results ***/

function section_entermatch_recalcAllRatings($affected_dates = null){
  $teams = array();   // running calculation

  $freezeTime=0;
  $changed_seasons = array();
  $res = section_entermatch_queryFreezes();
  while ($row = mysql_fetch_object ($res)){
    if ($freezeTime==0)
      $freezeTime = $row->ts;
    $teams[$row->id] = $row->rank;  
  }
  $res = section_entermatch_queryMatchesSince($freezeTime);
  while ($row = mysql_fetch_object ($res)){
    if (($oldA = $teams[$row->team1]) == 0)
      $oldA = 1200;
    if (($oldB = $teams[$row->team2]) == 0)
      $oldB = 1200;
    section_entermatch_calculateRating ($row->score1, $row->score2, $oldA, $oldB, &$newA, &$newB);
    $teams[$row->team1] = $newA;
    $teams[$row->team2] = $newB;
  }
  $keys = array_keys ($teams);
  $isFound = false;
  for ($x=0; $x<count($keys); $x++){
    $teamId = $keys[$x];
    $row = queryGetTeam ($teamId);
    if ($row && $row->status!='deleted'){
      $newRank = $teams[$teamId];   
      if ($newRank != $row->score){
        if (!$isFound){
          section_entermatch_rankupStart();
          $isFound=true;
        }
        section_entermatch_rankupTran($teamId, $row->name, $row->score, $newRank);
      }
    }
  }
  if($isFound)
      section_entermatch_rankupEnd();
}

//__________________________________________________________________
//
//                            section_entermatch_calcSeasonRatings()
//__________________________________________________________________
//
//  Arguments:    $rowA      - full db record of team A
//                $newA      - new zelo ranking for team A
//                $scoreA    - the score of team A in current match
//                $rowB      - full db record of team B
//                $newB      - new zelo ranking for team B
//                $scoreB    - the score of team B in current match
//                $matchDate - Date of current match as string
//  Returns:      the season id this matched was played in
//  Description:
//    Calculate the match result into a teams seasonal rating
//__________________________________________________________________

function section_entermatch_calcSeasonRatings($rowA,$newA,$scoreA,$rowB,$newB,$scoreB,$matchDate){
  $season_id = 1;
  if(ENABLE_SEASONS)  {
    $now = nowDate();
    echo "Match Date: " . $matchDate . "<br>";
    $res = sqlQuerySingle("select id from l_season where startdate <= '$matchDate' and fdate >= '$matchDate'");
    if ($res) {
      $season_id = $res->id;

      $INDEX_ZELO = 0;
      $INDEX_MATCHES = 1;
      $INDEX_WIN = 2;
      $INDEX_LOST = 3;
      $INDEX_DRAW = 4;

      $dataA = array();
      $dataB = array();
      $dataA[$INDEX_ZELO] = 0;
      $dataA[$INDEX_MATCHES] = 0;
      $dataA[$INDEX_WIN] = 0;
      $dataA[$INDEX_LOST] = 0;
      $dataA[$INDEX_DRAW] = 0;

      $dataB[$INDEX_ZELO] = 0;
      $dataB[$INDEX_MATCHES] = 0;
      $dataB[$INDEX_WIN] = 0;
      $dataB[$INDEX_LOST] = 0;
      $dataB[$INDEX_DRAW] = 0;
       

      // query data for team A
      $res = sqlQuerySingle("select 1 from l_teamscore where team = $rowA->id and season = $season_id");
      if(!$res || !mysql_fetch_assoc($res))
        sqlQuery("insert into l_teamscore (team,season,tsedit,tsmatch) values ($rowA->id,$season_id,'$now','$tsActStr')");
      $res = sqlQuerySingle("select won,lost,draw,matches,zelo from l_teamscore where team = $rowA->id and season = $season_id");
      if ($res) {
        $dataA[$INDEX_ZELO] = $res->zelo;
        $dataA[$INDEX_MATCHES] = $res->matches + 1;
        $dataA[$INDEX_WIN] = $res->won;
        $dataA[$INDEX_LOST] = $res->lost;
        $dataA[$INDEX_DRAW] = $res->draw;
      }

      // query data for team B
      $res = sqlQuerySingle("select 1 from l_teamscore where team = $rowB->id and season = $season_id");
      if (!$res)
        sqlQuery("insert into l_teamscore (team,season,tsedit,tsmatch) values ($rowB->id,$season_id,'$now','$tsActStr')");
      $res = sqlQuerySingle("select won,lost,draw,matches,zelo from l_teamscore where team = $rowB->id and season = $season_id");
      if ($res) {
        $dataB[$INDEX_ZELO] = $res->zelo;
        $dataB[$INDEX_MATCHES] = $res->matches + 1;
        $dataB[$INDEX_WIN] = $res->won;
        $dataB[$INDEX_LOST] = $res->lost;
        $dataB[$INDEX_DRAW] = $res->draw;
      }

      // update teams seasonal elo rating
      section_entermatch_calculateRating ($scoreA, $scoreB, $dataA[$INDEX_ZELO], $dataB[$INDEX_ZELO], &$newA, &$newB);
      $dataA[$INDEX_ZELO] = $newA;
      $dataB[$INDEX_ZELO] = $newB;
      // update team score
      // - this is the place to change for using score diff n points too i think
      if ($scoreA > $scoreB) {     // Team A wins
        $dataA[$INDEX_WIN]++;
        $dataB[$INDEX_LOST]++;
      }
      else if ($scoreA < $scoreB) { // this should not happen, since it is sorted first
        $dataA[$INDEX_LOST]++;
        $dataB[$INDEX_WIN]++;
      }
      else {                       // Team A wins
        $dataA[$INDEX_DRAW]++;
        $dataB[$INDEX_DRAW]++;
      }
      
      sqlQuery("update l_teamscore 
                  set won     = $dataA[$INDEX_WIN],
                      lost    = $dataA[$INDEX_LOST],
                      draw    = $dataA[$INDEX_DRAW],
                      matches = $dataA[$INDEX_MATCHES],   
                      zelo    = $dataA[$INDEX_ZELO],
                      tsedit  = '$now',
                      tsmatch = '$matchDate'
                  where team  = $rowA->id and season = $season_id");

      sqlQuery("update l_teamscore 
                  set won     = $dataB[$INDEX_WIN],
                      lost    = $dataB[$INDEX_LOST],
                      draw    = $dataB[$INDEX_DRAW],
                      matches = $dataB[$INDEX_MATCHES],   
                      zelo    = $dataB[$INDEX_ZELO],
                      tsedit  = '$now',
                      tsmatch = '$matchDate'
                  where team  = $rowB->id and season = $season_id");
    }
  }
  return $season_id;
}

//__________________________________________________________________
//
//                          section_entermatch_recalcSeasonRatings()
//__________________________________________________________________
//
//  Arguments:    $season_id - optional id of the season
//  Returns:      true if the season needs updating
//                false if not.
//                Note that the current season never needs updating
//  Description:
//    Recalculate table for specified season
//    Note: the zelo difference is not recalculated, so maybe
//          should not be considered in calculating ranking
//__________________________________________________________________

function section_entermatch_recalcSeasonRatingsByDate($tsActStr) {
  $row = sqlQuerySingle("SELECT id from l_season WHERE startdate <= '$tsActStr' AND enddate >= '$tsActStr'");
  if ($row) {
    return section_entermatch_recalcSeasonRatings($row->id);
  }
  else  {
    #echo "<tr><td colspan=4><font class=feedback><BR>Does not affect any running seasons.</font></td></tr>";    
    return false;
  }
}

function section_entermatch_recalcSeasonRatings($season_id = -1) {
  $now = nowDateTime();
  $INDEX_ZELO         = array();
  $INDEX_MATCHES      = array();
  $INDEX_WIN          = array();
  $INDEX_LOST         = array();
  $INDEX_DRAW         = array();
  $INDEX_DATE         = array();
  $SEASON_RECALC      = array();
  $dirty              = false;
  // select a season to update
  if ($season_id < 0) {
    $season = sqlQuerySingle("select * from l_season where startdate <= '$now' and fdate >= '$now' and id > 1 limit 1");
    if (!$season)
      $season = sqlQuerySingle("select * from l_season where active='yes' and id > 1 limit 1");
    if (!$season)
      $season = sqlQuerySingle("select * from l_season where finished='yes' and id > 1 ORDER by fdate desc limit 1");
    $season_id = $season?$season->id:-1;
  }
  else if ($season_id > 1)
    $season = sqlQuerySingle("select * from l_season where id = $season_id");
  if (!$season || $season_id < 2 || !$season->enddate) {
    echo "<tr><td colspan=4><font class=feedback><BR>No season found to update [#$season_id]</font></td></tr>";
    return;
  }

  echo "<tr><td colspan=4><font class=feedback><BR>Update season ranking: $season->startdate - $season->enddate [id=$season->id]</font></td></tr>
        <tr class=tabhead><td colspan=4>Check matches</td></tr>";
  
  $matches = sqlQuery ($sql="select * from ". TBL_MATCH ." where season = $season_id or (tsactual >= '$season->startdate 00:00:00' and tsactual <= '$season->enddate 23:59:59') order by tsactual");

  while ($row = mysql_fetch_object($matches)) {
    $team1 = null;
    $team2 = null;

    // 1. check if season id is ok
    if($row->season != $season_id) {
      if (!$team1) $team1 = queryGetTeamName($row->team1);
      if (!$team2) $team2 = queryGetTeamName($row->team2);
      echo "<tr><td>$team1 vs $team2</td><td><nobr>$row->tsactual</nobr></td><td colspan=2><nobr> changed season [$row->season to $season_id]</nobr></td></tr>";
      sqlQuery("update ". TBL_MATCH ." set season = $season_id where id = $row->id");
      $SEASON_RECALC[$new_season] = true;
      next;
    }
    else
    // 2. startdate check if match is in season
    // OR 3. enddate: check if match is in season 
    if( (strtotime($row->tsactual) < strtotime($season->startdate)) ||
        (strtotime($row->tsactual) > strtotime($season->enddate))) {
      if (!$team1) $team1 = queryGetTeamName($row->team1);
      if (!$team2) $team2 = queryGetTeamName($row->team2);
      $new_season = sqlQuerySingle("select * from l_season where startdate <= '$row->tsactual' and enddate >= '$row->tsactual' and id > 1 limit 1");
      $new_season = $new_season ? $new_season->id : 1;
      if( $season_id != $new_season ) {
          echo "<tr><td>$team1 vs $team2</td><td><nobr>$row->tsactual</nobr></td><td colspan=2><nobr> changed season due to seasondates [$season_id -> $new_season]</nobr></td></tr>";
          sqlQuery("update ". TBL_MATCH ." set season = $new_season where id = $row->id");
          $SEASON_RECALC[$new_season] = true;
          next;
       }
    } 

    // 4. update values
    $INDEX_ZELO[$row->team1] = $INDEX_ZELO[$row->team1]?$INDEX_ZELO[$row->team1]:1200;
    $INDEX_ZELO[$row->team2] = $INDEX_ZELO[$row->team2]?$INDEX_ZELO[$row->team2]:1200;
    $INDEX_DATE[$row->team1] = $row->tsactual;
    $INDEX_DATE[$row->team2] = $row->tsactual;
    $INDEX_MATCHES[$row->team1] = $INDEX_MATCHES[$row->team1]?$INDEX_MATCHES[$row->team1]+1:1;
    $INDEX_MATCHES[$row->team2] = $INDEX_MATCHES[$row->team2]?$INDEX_MATCHES[$row->team2]+1:1;
    if ($row->score1 > $row->score2) {
      $INDEX_WIN[$row->team1] = $INDEX_WIN[$row->team1]?$INDEX_WIN[$row->team1]+1:1;
      $INDEX_LOST[$row->team2] = $INDEX_LOST[$row->team2]?$INDEX_LOST[$row->team2]+1:1;
    }
    else if ($row->score1 < $row->score2) {
      $INDEX_LOST[$row->team1] = $INDEX_LOST[$row->team1]?$INDEX_LOST[$row->team1]+1:1;
      $INDEX_WIN[$row->team2] = $INDEX_WIN[$row->team2]?$INDEX_WIN[$row->team2]+1:1;
    }
    else {
      $INDEX_DRAW[$row->team1] = $INDEX_DRAW[$row->team1]?$INDEX_DRAW[$row->team1]+1:1;
      $INDEX_DRAW[$row->team2] = $INDEX_DRAW[$row->team2]?$INDEX_DRAW[$row->team2]+1:1;
    }
    section_entermatch_calculateRating ($row->score1, $row->score2, $INDEX_ZELO[$row->team1], $INDEX_ZELO[$row->team2], &$newA, &$newB);
    $INDEX_ZELO[$row->team1] = $newA;
    $INDEX_ZELO[$row->team2] = $newB;
  }

  echo "<tr><td colspan=4><hr></td></tr>
        <tr class=tabhead><td colspan=4>Check team scores</td></tr>
        <tr class=tabhead>
          <td>Team</td>
          <td>Action</td>
          <td>Old</td>
          <td>New</td>
        </tr>";

  // now update all teams for which a match was found
  $keys = array_keys ($INDEX_MATCHES);
  for ($x=0; $x<count($keys); $x++) {
    $teamId   = $keys[$x];
    $matches  = $INDEX_MATCHES[$teamId];
    $won      = $INDEX_WIN[$teamId] ?$INDEX_WIN[$teamId] :0;
    $lost     = $INDEX_LOST[$teamId]?$INDEX_LOST[$teamId]:0;
    $draw     = $INDEX_DRAW[$teamId]?$INDEX_DRAW[$teamId]:0;
    $date     = $INDEX_DATE[$teamId]?$INDEX_DATE[$teamId]:null;
    $zelo     = $INDEX_ZELO[$teamId]?$INDEX_ZELO[$teamId]:1200;
    $row = sqlQuerySingle("select 1 from l_teamscore where team = $teamId and season = $season_id");
    if (!$row)
      sqlQuery("insert into l_teamscore (team,season,tsedit,tsmatch) values ($teamId,$season_id,'$now','$date')");
    $row = sqlQuerySingle("select won,lost,draw,matches,zelo from l_teamscore where team = $teamId and season = $season_id");
    if ($won != $row->won || $lost != $row->lost || $draw != $row->draw || $zelo != $row->zelo) {
      $team1 = queryGetTeamName($teamId);
      $dirty = true;
      echo "<tr><td>$team1</td><td>Update</td><td>$row->won -  $row->lost - $row->draw ($row->zelo)</td><td>$won - $lost - $draw ($zelo)</td></tr>";
      sqlQuery("update l_teamscore 
                  set won     = $won,
                      lost    = $lost,
                      draw    = $draw,
                      zelo    = $zelo,
                      matches = $matches,   
                      tsedit  = '$now',
                      tsmatch = '$date'
                  where team  = $teamId and season = $season_id");
    }
  }

  echo "<tr><td colspan=4><hr></td></tr>
        <tr class=tabhead><td colspan=4>Remove teams with no matches</td></tr>
        <tr class=tabhead>
          <td>Team</td>
          <td>Action</td>
          <td colspan=2>Reason</td>
        </tr>";

  // now check if in the season we have teams that didn't play a match
  //  - question: how to do an intelligent query when MySQL 4.0 doesn't allow EXIST statements
  $tscores = sqlQuery ("select * from l_teamscore where season = $season_id");
  while ($row = mysql_fetch_object($tscores)) {
    $exists = sqlQuerySingle("select * from " . TBL_MATCH . " where (team1 = $row->team  OR team2 = $row->team) AND season = $season_id limit 1");
    if (!$exists) {
      $team1 = queryGetTeamName($row->team);
      echo "<tr><td>$team1</td><td>Delete</td><td colspan=2>Didn't play any matches</td></tr>";
      $dirty = true;
      sqlQuery("DELETE FROM l_teamscore WHERE season = $season_id AND team = $row->team");
    }
  }
  # seasion id 1 is not a real season.
  foreach( array_keys($SEASON_RECALC) as $i ) 
    if( $i != 1 ) section_entermatch_recalcSeasonRatings($i);

  if ($dirty) {
    if ($season->finished == 'yes') {
      echo "<tr><td colspan=4><hr></td></tr>";
      echo '<tr><td colspan=4>Match change has an effect on season ' . $season->startdate . ' - ' . $season->enddate . '. Update season score manually</td></tr>';
      sqlQuery("UPDATE l_season SET dirty = 'yes' WHERE id= $season_id");
      return true;    
    }
  }
  return false;
}

/******* SQL Queries: ****/

function section_entermatch_queryFreezes () {
  return sqlQuery ("select id, rank, ts from bzl_freezeranks");
}

function section_entermatch_queryMatchesSince ($since) {       // since freezedate !!
  return sqlQuery ("select * from ". TBL_MATCH ." where tsactual > '$since' order by tsactual ");
}

function section_entermatch_queryGetMatch ($id){
  $res = sqlQuery ("select * from ". TBL_MATCH ." where id = $id");
  return mysql_fetch_object ($res);
}

// returns 'rank freeze' timestamp as UnixTime
function section_entermatch_queryGetFreezeDate (){
  $res = sqlQuery ("select ts from bzl_freezeranks limit 1");
  if (($row = mysql_fetch_object ($res)) === false)
    return false;
  return strtotime ($row->ts);
}

function section_entermatch_queryLastMatchTime (){
  $res = sqlQuery ("select tsactual from " . TBL_MATCH . " ORDER by tsactual desc LIMIT 1");
  $row = mysql_fetch_object($res);
  return strtotime ($row->tsactual);
}

function section_entermatch_queryCheckDup ($teamA, $teamB, $scoreA, $scoreB, $numdays){
  $res = sqlQuery ("select team1, score1, team2, score2, tsactual from " . TBL_MATCH . "
  WHERE ((team1=$teamA AND team2=$teamB AND score1=$scoreA AND score2=$scoreB)
     OR (team1=$teamB AND team2=$teamA AND score1=$scoreB AND score2=$scoreA))
              AND tsactual > DATE_SUB(NOW(), INTERVAL $numdays day)
            ORDER by tsactual desc LIMIT 1");
  if (!$res)
    return false;
  return mysql_fetch_object($res);
}


?>
