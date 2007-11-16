<?php // $Id$ vim:sts=2:et:sw=2

function section_teaminfo (){
  require_once('lib/common.php');
  $s_teamid     = $_SESSION['teamid'];
  $s_logedin    = isAuthenticated();
  $s_level      = $_SESSION['level'];
  $s_playerid   = $_SESSION['playerid'];

  $id = addslashes($_GET['id']);

  $res = mysql_query("SELECT name, comment, leader, logo, status, score, 
                     unix_timestamp(status_changed) as status_changed, unix_timestamp(created) as ucreated
    FROM l_team WHERE id='$id'");

  $team = mysql_fetch_object($res);

  if (!$team){
    echo '<BR><CENTER>Specified team does not exist<BR>';
    return;
  }

  echo '<TABLE align=center><TR><TD>'
      .htmlURLbutton ('Opponent summary', 'oppsumm', "id=$id") .'</td>';

  // Join this team if opened, and if we are logged and not belonging to any team
  if($s_logedin && !$s_teamid && $members < 20 && $team->status == "opened") {
    echo '<TD>'.htmlURLbutton ('Join Team', 'jointhisteam', "id=$id").'</td>';
  }
  // Send a message to all the team members
  if($s_logedin && $team->status != 'deleted') {
    echo '<TD>'.htmlURLbutton ('Send BZmessage', 'sendmessage', "tid=$id").'</td>';
  }
  echo '</tr></table>';

  if( $team->status == 'deleted' ) {
    echo '<div class=feedback>';
    if( $team->status_changed )
      echo "<br><center>This team was deleted on " . gmdate('Y-m-d', $team->status_changed) . "</center>";
    else
      echo "<br><center>This team is deleted.</center>";
    echo '</div>';
  }

  echo '<BR><table align=center border=0 cellspacing=0 cellpadding=1>
    <tr><td class=teamName align=center>'.$team->name.'<BR></td></tr>';

  // Logo if any
  if($team->logo != "") {
    echo '<tr><td align=center>
    <table><TR><TD bgcolor=white><img src="'.$team->logo.'"></td></tr></table>
    <hr></td></tr>';
  }



  // Ratings
  $act45 = teamActivity($id, 45);
  $act90 = teamActivity($id, 90);
  echo '<tr><td> <TABLE align=center>
    <TR><TD width=50% align=right>Created:</td><td width=10></td><td width=50%>'
        . gmdate ('Y-m-d', $team->ucreated).'</td></tr>
    <TR><TD align=right>Rating:</td><td></td><td>'. displayRating($id) .'</td></tr>
    <TR><TD align=right>Activity:</td><TD></td><td>'. 
    sprintf ('%1.2f / %1.2f', $act45, $act90) 
    .'</td></tr>
     <TR><TD colspan=3 align=center>  Average number of games played per day<BR>
     <NOBR>(exponential moving average over last 45 / 90 days)</nobr></td></tr>';
  echo '</td></td></table></td></tr>';



  // Matches statistics
  $sta1 = mysql_fetch_object(mysql_query("select ifnull(sum(if(score1>score2,1,0)),0) win,
    ifnull(sum(if(score1=score2,1,0)),0) draw,
    ifnull(sum(if(score1<score2,1,0)),0) loss
    from ". TBL_MATCH ." where team1=$id"));
  $sta2 = mysql_fetch_object(mysql_query("select ifnull(sum(if(score2>score1,1,0)),0) win,
    ifnull(sum(if(score2=score1,1,0)),0) draw,
    ifnull(sum(if(score2<score1,1,0)),0) loss
    from ". TBL_MATCH ." where team2=$id"));

  $win = $sta1->win + $sta2->win;
  $draw = $sta1->draw + $sta2->draw;
  $loss = $sta1->loss + $sta2->loss;
  echo "<tr><td align=center><hr>
  <table border=0 cellspacing=0 cellpadding=0 align=center><tr>
  <td align=center>Wins</td><td align=center>&nbsp;&nbsp;Draws&nbsp;&nbsp;</td><td align=center>Losses</td></tr><tr>
  <td align=center>$win</td><td align=center>$draw</td><td align=center>$loss</td>
  </tr></table>
  <hr></td></tr>";
  
  // Comment if any
  if($team->comment != "") {
    echo '<tr><td><ul>'.nl2br($team->comment).'</ul><hr></td></tr>';
  }
  // Players list
  $i = 0;
  if (SHOW_PLAYER_ACTIVE > 0)
    $activeDays = SHOW_PLAYER_ACTIVE;
  else
    $activeDays = 0;
  $res = mysql_query("select id, callsign, comment, status, C.flagname, 
    last_login > subdate(now(), INTERVAL $activeDays DAY) as active
    from l_player
    left join bzl_countries C on country = C.numcode
    where team=".$id."
    order by active desc,callsign");
  $members = 0;

  echo '<TR><TD><table border=0 cellspacing=0 cellpadding=0 align=center>';
  
  while($obj = mysql_fetch_object($res)) {
    $members++;
    if(++$i & 1)
      $cl = "rowEven";
    else
      $cl = "rowOdd";
    echo "<tr class='$cl' valign=middle>";

    echo "<TD align=right><a href='index.php?link=playerinfo&id=$obj->id'>$obj->callsign</a></td><TD>";
    if($obj->id == $team->leader)
      echo '&nbsp;<img TITLE="Team Leader" src="'.THEME_DIR.'leader.gif">';

    echo '</td><TD>';
    if ($obj->active)
      echo '&nbsp;<img TITLE="Active player (has logged into this site recently)" src="'.THEME_DIR.'active.gif">';
    
    echo '</td><TD width=10></td>';
    echo '<TD>'.smallflag ($obj->flagname).'</td>';

    if($s_logedin&&(isFuncAllowed('teamadmin::edit_any_team') ||$s_playerid==$team->leader)) {
      if($obj->id != $team->leader) {
        // Link to ban a player from a team
        echo '<td align=center>'.
        htmlURLbutSmall ('BAN', 'banplayer', 
          "playerid=$obj->id&teamid=$id&f_ok_x=2", ADMBUT)
        . '</form></td></tr>';
      } else {
        echo '<td>&nbsp;</td></tr>';
      }
    } else {
      echo '<td>&nbsp;</td></tr>';
    }
    echo "\n";
  }
  echo '</td></tr></table>';
  
  
  // Show last fights

  $sql = "SELECT  t1.id, t1.name, f.score1, t2.id, t2.name, f.score2, f.tsactual, unix_timestamp(f.tsactual) tstamp_ts \n".
      "FROM ". TBL_MATCH ." f, l_team t1, l_team t2 ".
      "WHERE (f.team1 = '$id' OR f.team2 = '$id') ".
      " AND f.team1 = t1.id ".
      " AND f.team2 = t2.id ".
      "ORDER BY f.tsactual DESC ".
      "LIMIT 11 ";

  $res = sqlQuery($sql);

  $tmp = '';
  $count = 0;
  while($row = mysql_fetch_array($res))
  {
    $count++;
    if( $count < 11 )
    {
      if( isset($_SESSION['last_login']) && ($_SESSION['last_login'] < $row[7] ))
      {
        $new1 = "<font color=\"red\">";
        $new2 = "</font>";
      }
      else
      {
        $new1 = '';
        $new2 = '';
      }

      if($count % 2)
        $cl = "rowEven";
      else
        $cl = "rowOdd";

      $tmp .= "<tr class=\"$cl\">";
      
      $tmp .= '<td align="right">' . $new1. $row[6] . $new2. '</td>';
      if( $row[0] != $id )  {
        $oppid    = $row[0];
        $oppname  = stripslashes($row[1]);
        $oppscore = $row[2];

        $teamscore  = $row[5];
      } else  {
        $oppid    = $row[3];
        $oppname  = stripslashes($row[4]);
        $oppscore = $row[5];
        $teamscore  = $row[2];
      }

      $tmp .= "<td>$new1";

      $oppname = '<a href="index.php?link=teaminfo&id=' . $oppid . '&' . SID . '">' . 
        $new1 . $oppname . $new2 . '</a>';

      if( $oppscore < $teamscore )
        $tmp .= "<b>Won</b> against $oppname";
      elseif( $oppscore > $teamscore )
        $tmp .= "<b>Lost</b> against $oppname";
      else
        $tmp .= "<b>Tie</b> against $oppname";

      $tmp .= " ({$row[2]} - {$row[5]})";
      $tmp .= "$new2</td>";

      $tmp .= "</tr>\n";
    }
  }

  echo '<tr>';
  echo '<td align=center>&nbsp;<br><b>';
  if( $count == 1 ) {
    echo 'Last match';
  }
  else{
    if( $count == 11 )
      echo 'Last 10 matches, view them all <a href="index.php?link=fights&id=' . $id . '&' . SID . '">here</a>';
    elseif( $count == 0 )
      echo "No matches played";
    else
      echo "Last $count matches";
  }
  echo '</b><BR><BR></td></tr><TR><TD>';
  echo "<table align=center border=0>$tmp</table>";


  // Or... edit your team if you are a leader or ad admin, or with a password if you are a member
  // Team members can't change the password, though...
  // Unless ofcause the team is deleted

  if( $team->status == 'deleted' ) {
    if( $team->status_changed )
      echo "<br><center>This team is deleted as of " . gmdate('Y-m-d', $team->status_changed) . "</center>";
    else
      echo "<br><center>This team is deleted.</center>";
  } else {
    if($s_logedin&&(isFuncAllowed('teamadmin::edit_any_team')||$s_teamid==$id)) {
      if((!isFuncAllowed('teamadmin::edit_any_team')) && ($s_playerid != $team->leader)) {
        // Team member, need password
        echo '<form method=post>
          <input type=hidden name=link value=teamadmin>
          <input type=hidden name=id value='.$id.'>
          <br><center>Enter team password <input type=password size=8 maxlength=8 name=f_password> and 
          '. htmlFormButton ('Edit Team', 'f_edit_x') .'
          </center></form>';
      } else {
        $invites = sqlQuery ("SELECT *, l_player.callsign FROM bzl_invites,  l_player
            WHERE teamid=$id AND expires > NOW() AND bzl_invites.playerid = l_player.id");

        if (mysql_num_rows($invites) > 0){
          echo '<BR><HR>Invitations currently active:<BR><TABLE>';
          while ($row = mysql_fetch_object ($invites)){
            echo '<TR><TD width=25></td><TD>'. playerLink($row->playerid, $row->callsign)
                ."</td><TD width=10></td><TD>(expires: $row->expires)</td></tr>";
          }
          echo '</table><HR>';
        }

        // Team leader or admin, let's go
        echo '<center><BR>
        '. htmlURLbutton ('Edit Team', 'teamadmin', "id=$id", ADMBUT) .'
        <BR>
        '. htmlURLbutton ('Dismiss Team', 'dismissteam', "id=$id", ADMBUT);
      }
    }
  }
}
?>
