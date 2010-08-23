<?php // $Id: reviveteam.php,v 1.6 2005/04/26 17:13:43 menotume Exp $ vim:sts=2:et:sw=2

function section_reviveteam (){
  require_once("lib/common.php");

  if (!isFuncAllowed('revive_team'))
    return errorPage ('not authorized');

  $vars = array('OK', 'tid', 'leader');
  foreach($vars as $var)
    $$var = isset($_POST[$var]) ? $_POST[$var] : $_GET[$var];
  echo '<BR>';
  if ($OK=='yes' && section_reviveteam_checkSubmit($tid, $leader))
    return; 
  section_reviveteam_presentForm ();
}

function section_reviveteam_permissions() {
  return array(
    'revive_team' => 'Allow to re-create a deleted team'
  );
}

function section_reviveteam_checkSubmit ($tid, $leader){
  if ($tid>0 && $leader>0){
    sqlQuery ("update l_team set status='opened', adminclosed='no', active='no',
      leader=$leader  where id=$tid" );
    sqlQuery ("update l_player set team=$tid where id=$leader");    
    $team = queryGetTeam ($tid);

    sendBzMail (0, $leader, 'Team revived!', 
      'The team "' . teamLink($team->name, $tid, $team->status) .'" has been revived by a league administrator, and you are now
      the team leader.  YAY!  Before you are eligible for matches, you must add at least 
      one player to the team', false, true);  
  
    echo '<CENTER>The "'. teamLink($team->name, $tid, $team->status) .'" team has been revived.  YAY!';
    return 1;
  } else {
    echo '<div class="feedback" align=center>ERROR, try again</div><BR><BR>';
    return 0;
  }
}



function section_reviveteam_presentForm (){
  echo '<CENTER>Select a team AND a team leader:<BR><BR>
    <FORM method=post name="xgi"><TABLE><TR><TD valign=top><TABLE cellspacing=0>
    <TR class=tabhead align=center><TD>Team Name</td><td width=8></td><TD>Deleted</td></tr>';
  
    $res = sqlQuery ("select name, id, SUBSTRING(status_changed,1,10) as ts from l_team 
        where status='deleted' AND name != 'DELETED TEAM' order by name");

    while ($team = mysql_fetch_object($res)){
      echo "<TR><TD><input type=radio name=\"tid\" value=\"$team->id\">
        $team->name</td><TD>&nbsp;&nbsp; [$team->id] &nbsp;&nbsp;</td>
        <TD>$team->ts</td></tr>\n";
    }

  echo '</table></td><td width=10></td><TD valign=top>';
    $res = sqlQuery ("select id, callsign from l_player where team=0 and status!='deleted' order by callsign");
    echo '<BR><input type=hidden name="OK" value="yes"><select name="leader">';
    echo '<option value="0"> - select team leader - </option>';
    while ( $player = mysql_fetch_object($res) )
      echo "<option value='$player->id'>$player->callsign</option>\n";
    echo '</select>';

  echo '<p><TABLE align=center><TR><TD>' 
    . htmlFormButton ('Revive', '') 
    . '</td><TD width=8></td><TD>' .htmlFormReset ('Reset')
    . '</td></tr></table>';

  echo '</td></tr></table></form>';
}


?>
