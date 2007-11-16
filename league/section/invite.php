<?php // $Id$ vim:sts=2:et:sw=2
require_once('lib/common.php');

$invite_durations = array(
  1  => '1 day',
  3  => '3 days',
  7  => '7 days', 
  10 => '10 days',
  30 => '30 days' 
);
$invite_duration_default = 7;

function section_invite(){
  global $invite_durations;
  global $invite_duration_default;

  $vars = array('id','f_ok_x','f_invite','link','days');
  foreach($vars as $var)
    $$var = isset($_POST[$var]) ? $_POST[$var] : $_GET[$var];

  echo '<BR>';

  // Quick test: See if user (invoking this page) is a teamleader
  if(! $_SESSION['leader']) {
    errorPage ('Not Authorized - only teamleaders can invite');
    return;
  }

  // Check if player exists
  $player = mysql_fetch_object(mysql_query("select id, callsign, team, status  from l_player where id=$id"));
  if(! $player ) {
    errorPage('Unknown player');
    return;
  }

  // Check if player is an active player (deleted players are no fun)
  if( $player->status != 'registered' ) {
    errorPage("You can only invite active players");
    return;
  }

  // Check if player is already a member
  if($player->team == $_SESSION['teamid']){
    echo playerLink($player->id, $player->callsign) .' is already on your team!<BR>';
    return;
  }

  // Check if player is already invited to the team
  $inv = mysql_fetch_object(sqlQuery ("SELECT 1 FROM bzl_invites WHERE teamid={$_SESSION ['teamid']} AND playerid=$id AND expires > NOW()"));

  if($inv) {
    echo '<CENTER>You already have an invitation active for this player <BR>Look on your <A HREF="index.php?link=teaminfo&id='. $_SESSION['teamid']  .'">team page</a> to see all active invitations.<BR>';
    return;
  }

  // Get team info
  $team = mysql_fetch_object(mysql_query("select id, name from l_team where id={$_SESSION['teamid']}"));
  if(! $team ) {
    errorPage("Unknown team");    // This shouldn't happen under normal operation. 
    return;
  }

  // Last step, if invite text was written, invite the player - otherwise complain an redisplay the form
  if($f_ok_x) {
    // Make sure that $days is valid
    if(! in_array($days, array_keys($invite_durations))) {
      errorPage("Not a valid duration for the invitation");
      return;
    }
  
    $f_invite = stripslashes($f_invite);
    if($f_invite == '') {
      echo "<div class=error>You must say something to the player you're inviting</div>";
      section_invite_dispForm ($team, $player, $link, $days );
    }
    else {
      section_invite_sendInvite ($team, $player, $days, $f_invite);
    }
  } 
  else {
    section_invite_dispForm ($team, $player, $link, $invite_duration_default);
  }
}

function section_invite_sendInvite ($team, $player, $days, $text){
  echo "<center>Invitation sent to player, thank you!</center>";
  $text = htmlentities($text);
  $msg = '<b>'. $_SESSION['callsign']. "</b> is inviting you to join his/her team: <b>$team->name</b>.<br>
      <u>Invitation text:</u><br>$text<p><br>
      <a href='index.php?link=jointeam&id=$team->id'><font size=+1>Click here to accept the invitation.</font></a><br>
      Note that the invitation expires $days days from when it was sent.";

  sendBzMail ($_SESSION['playerid'], $player->id, 'Invitation from '.$_SESSION['callsign'], $msg, false, true);

  sqlQuery ("INSERT INTO bzl_invites (teamid, playerid, expires) VALUES ($team->id, $player->id, 
     ADDDATE(NOW(), INTERVAL $days DAY))");

}

function section_invite_dispForm ($team, $player, $link, $days){
  global $invite_durations;
  echo '<center><form method=post><font size=+1>Sending an invitation to '
      . playerLink ($player->id, $player->callsign)
      . ' to join your team, '. teamLink ($team->name, $team->id, false) .'</font><br>';

  echo '<BR><TABLE><TR><TD><HR><p>This invitation will allow '. playerLink ($id, $player->callsign) 
      .' to join your team, even if it is closed.<BR>Please select when this invitation should expire:   
      &nbsp;&nbsp;&nbsp;<select name=days>';
  foreach($invite_durations as $val => $text )
    htmlOption ($val, $text, $days);

  echo '</select><p><hr><p>

  Enter a few words which will be attached to your invitation (mandatory):<br>
  <textarea cols=50 rows=6 name=f_invite>'.$f_invite.'</textarea>
  <center><p><TABLE align=center><TR><TD>'. htmlFormButton ("Invite", 'f_ok_x') .'</td><TD width=10></td><TD>'
     . htmlURLbutton ("Cancel", 'playerinfo', "id=$id", CLRBUT) .'</td></tr></table>';

  echo '<input type=hidden name=link value='.$link.'>
  <input type=hidden name=id value='.$player->id.'>
  </center></form></center>     </td></tr></table>';
}

?>
