<?php // $Id$ vim:sts=2:et:sw=2

function section_jointeam(){
  require_once('lib/common.php');

  $id = addslashes($_GET['id']);
  $f_yes_x = $_GET['f_yes_x'];
  $f_no_x = $_GET['f_no_x'];



  if($f_yes_x) {
    section_jointeam_doJoin ($id , $_GET['invite'] );
  } else if($f_no_x) {
    echo "<center>Okay then, you still don't belong to any team...</center>";
  } else {
    if($_SESSION['teamid']) {
      echo '<center><BR>Well, '.$_SESSION['callsign'].', you already belong to a team.<br></center>';
    } else {
      $team = mysql_fetch_object(mysql_query("select name, id, status from l_team where id=".$id));
      switch( $team->status )
      {
      case 'opened':
        section_jointeam_dispForm ($team, false);
        break;
      case 'closed':
        $invite = mysql_fetch_object (sqlQuery ("SELECT * FROM bzl_invites WHERE teamid=$team->id 
            AND playerid={$_SESSION['playerid']} AND expires > NOW()"));
        if ($invite)
          section_jointeam_dispForm ($team, $invite);
        else
          echo "<center>SORRY, but $team->name is a closed team<BR>
              If you had an invitation to join, it has expired.</center>";
        break;
      case 'deleted':
        echo "<center>$team->name is a deleted team, is only here for historical reasons.</center>";
        break;
      }
    }
  }
}




function section_jointeam_doJoin ($id, $invite){
    // Try to add player to team
    $obj = mysql_fetch_object(mysql_query("select l_team.id, l_team.name, l_team.leader, count(callsign) nump
      from l_team left join l_player  on l_team.id = l_player.team
      where l_team.id = ".$id."
      group by l_team.id, l_team.name, l_team.leader"));
    if($obj->nump == $TEAMSIZE) {
      // Team full
      echo "<center>Sorry, this team is full. Probably someone was joining it at the same time...</center>";
    } else {
      // Okay, let's do the update
      mysql_query("update l_player set team=".$id." where id=".$_SESSION['playerid']);
      // Close team if it's full
      if($obj->nump == $TEAMSIZE-1) {
        mysql_query("update l_team set status='closed' where id=".$id);
      }
      // Close team if it has 3 players and is adminclosed
      if($obj->nump == 2) {
        mysql_query("update l_team set status='closed' where adminclosed='yes' and id=".$id);
      }
      echo '<center>You are now a member of the '. teamLink($obj->name, $obj->id, false) .' team.<BR> 
        A message has been sent to the team leader.</center>';
      session_refresh();

      // Send a message to the team leader
      $player = playerLink ($_SESSION['playerid'], $_SESSION['callsign']);
      if ($invite){
        $msg = "$player has accepted your invitation, and has joined your team!<BR>";
        sqlQuery ("DELETE FROM bzl_invites WHERE teamid=$obj->id  AND 
            playerid={$_SESSION['playerid']}");
      }else
        $msg = "A new player just joined your team: $player";
      sendBzMail(0, $obj->leader, $_SESSION['callsign'] .' joined your team!', $msg);
    }
}



function section_jointeam_dispForm ($team, $invite){
  echo '<center><BR>' . playerLink ($_SESSION['playerid'], $_SESSION['callsign']) .', are you sure that
      you really want to join the fabulous '. teamLink($team->name, $team->id, false) .'?<br>'; 
      echo '<BR><form method=get>
      <input type=hidden name=link value="jointeam">
      <input type=hidden name=id value='.$team->id.'>';
  if ($invite)
      echo '<input type=hidden name=invite value="yep">';
  echo htmlFormbutton ('&nbsp;YES&nbsp;', 'f_yes_x') . '&nbsp;'
      . htmlFormbutton ('&nbsp;NO&nbsp;', 'f_no_x', CLRBUT) .'
      </form></center>';
}
?>
