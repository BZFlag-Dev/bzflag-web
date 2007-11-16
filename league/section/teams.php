<?php // $Id$ vim:sts=2:et:sw=2

function section_teams (){
  require_once("lib/common.php");
  $s_logedin  = isAuthenticated();
  $s_teamid = $_SESSION['teamid'];

  $tacts = teamActivity (null, 45);

  echo '<BR>';

  $res = sqlQuery("
    SELECT  l_team.id, l_team.name, l_team.logo, l_team.score, 
        player2.callsign leader, player2.id leaderid, 
        l_team.status, count(distinct l_player.callsign) numplayers,
        l_team.active = 'yes' activeteam, l_team.matches, l_team.matches > 0 sorter
    FROM  l_team, l_player player2, l_player
    WHERE player2.id = l_team.leader 
     AND l_team.status != 'deleted' 
     AND l_player.team = l_team.id
    GROUP BY l_team.name, l_team.leader, l_team.status, l_team.score
    ORDER BY  sorter desc, activeteam desc, l_team.score desc,  l_team.name");

  echo "<table align=center border=0 cellspacing=0 cellpadding=2>
      <tr class=tabhead align=center>
      <td>Name</td><td>Leader</td><td colspan=2>Members</td>
      <td colspan=2>Rating</td><td>Join</td><TD>Activity</td></tr>";

  $separated = 0;
  $rownum=0;
  while($obj = mysql_fetch_object($res)) {
    ++$rownum;
    if( ($obj->activeteam == 0) && ($separated == 0 ) ){
      $separated = 1;
      echo '<tr><td align=center colspan=10><hr><b>Inactive Teams</b></td></tr>';
      $rownum=1;
    }

    if( ($obj->sorter == 0) && ($separated == 1) ) {
      $separated = 2;
      echo '<tr><td align=center colspan=10><hr><b>Did not play any match</b></td></tr>';
      $rownum=1;
    }

    if($obj->logo != '') {
      $logo = '<img src="'.THEME_DIR.'islogo.gif">';
    } else {
      $logo = ' ';
    }



    if($s_teamid == $obj->id) {
      $c='myteam';
    } elseif( $obj->status == 'deleted' ) {
      $c='deletedteam';
    } else {
      if ($rownum%2)
        $c='rowOdd';
      else
        $c='rowEven';
    }

    echo "<TR class='$c' valign=middle>";
$teamname = substr ($obj->name, 0, 35);
$act = $tacts[$obj->id];
    echo '<td><a href="index.php?link=teaminfo&id='.$obj->id.'">'.$teamname.'</a></td>
    <td><a href="index.php?link=playerinfo&id='.$obj->leaderid.'&'.SID.'">'.$obj->leader.'</a></td>
    <td align=center>'.$obj->numplayers.'</td><td>'.$logo.'</td>
    <td align=left>'.displayRating($obj->id).'</td>';

    if($separated) {
      echo '<td>&nbsp;</td>';
    } else {
      echo '<td align=center>('.$obj->matches.')</td>';
    }


    // Print join or joinnot, not forgetting we may already belong to a team
echo '<TD align=left>';
    if($s_logedin) {
      if(!$s_teamid) {
        switch($obj->status)
        {
        case 'opened':
          if( $obj->numplayers < 20 )
            echo htmlURLbutSmall ('JOIN', 'jointeam', "id=$obj->id").'</td>';
          else
            echo '&nbsp;[Closed]</td>';
          break;
        case 'closed':
            echo '&nbsp;[Closed]</td>';
          break;
        default:
          echo 'Deleted.</td>';
          break;
        }
      } else {
        if($s_teamid==$obj->id) {
//          if( $_SESSION['playerid'] != 2074 ) // Dont allow Admir to leave (SC request)
            echo htmlURLbutSmall ('Abandon', 'leaveteam', "id=$obj->id&leader=$obj->leaderid").'</td>';
        } elseif($obj->status == 'closed') {
            echo '&nbsp;[Closed]</td>';
        } elseif($obj->status == 'deleted') {
          echo 'deleted.</td>';
        } else {
          echo '</td>';
        }
      }
    } elseif($obj->status == 'closed') {
      echo '&nbsp;[Closed]</td>';
    } else {
      echo '</td>';
    }

$act = sprintf ('%1.2f', $tacts[$obj->id]);
echo "<TD align=center>$act</td>";

    echo "</tr>";

  }
  echo "</table>";


  // Create a new team, if logged in and not a team member
  if( $s_logedin && ! $s_teamid) {
    echo '<br><center>
    '.htmlURLbutton ('Create New Team', 'createteam', null ).'</center>';
  }
}








?>
