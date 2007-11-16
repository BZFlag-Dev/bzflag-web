<?php // $Id$ vim:sts=2:et:sw=2

function section_banplayer(){
  require_once('lib/common.php');
  $playerid   = $_GET['playerid'];
  $teamid     = $_GET['teamid'];
  $f_okban_x  = $_GET['f_okban_x'];
  $f_ok_x     = $_GET['f_ok_x'];
  $callsign   = $_GET['callsign'];
  $name       = $_GET['name'];
  $link       = $_GET['link'];

  $player = mysql_fetch_object(mysql_query("select callsign from l_player where id=".$playerid));
  $team = mysql_fetch_object(mysql_query("select name from l_team where id=".$teamid));

  // FIXME: Ok, for now I ignore that players with teampassword can ban other
  // players. This is just a quick fix, before there was NO checks WHATSOEVER
  // if the operation was allowed

  // Check permission
  $allowed = 0;
  $obj = mysql_fetch_object(mysql_query("select name, comment, leader, logo, password, status, adminclosed from l_team where id=".$teamid));
  $res = mysql_fetch_object(mysql_query("select count(*) num from l_player where team=".$teamid));
  $numplayer = $res->num;
  if(isFuncAllowed('teamadmin::edit_any_team')||($_SESSION['playerid'] == $obj->leader)) {
    // Admin or team leader, allowed
    $allowed = 1;
  } else {
    // FIXME: This wont work, as the link does not contain the teampassword.
    /*
    // Check password
    $cypher = substr (crypt($f_password, substr($obj->password, 0, 2)), 0, 13);
    if($cypher == $obj->password) {
      // Good password, allowed
      $allowed = 1;
    }
    */
    errorPage("Permission denied.");
    return;
  }

  if($f_okban_x) {
    // Ban confirmed
    // Open team is not administratively closed, or if num players<3
    $pl = mysql_fetch_object(mysql_query("select count(*) num from l_player where team=".$teamid));

    if($pl->num == 3) {
      mysql_query("update l_team set status='opened' where id=".$teamid);
    } else {
      mysql_query("update l_team set status='opened' where adminclosed='no' and id=".$teamid);
    }

    mysql_query("update l_player set team=0 where id=".$playerid);
    echo '<center>Well <a href="index.php?link=playerinfo&id='.$_SESSION['playerid'].'">'
            .$_SESSION['callsign'].'</a>,
          <a href="index.php?link=playerinfo&id='.$playerid.'">'.$callsign.'</a>
           is no longer a member of the team
           <a href="index.php?link=teaminfo&id='.$teamid.'">'.$name.'</a>';

//TODO: reload of page sends multiple messages ... ?
          echo '<br>A message has just been sent to the user.</center>';
          sendBzMail(0, $playerid, 'You have been released!', 'Sorry, but '.$_SESSION['callsign'].' released you from the '.$team->name.' team<br>');
  } else {
  
    if(!$f_ok_x) {
      // Someone is playing with the headers
      echo "<center>Hey! Please stop playing with your browser!</center>";
    } else {
      echo '<center>Please confirm that you want to ban <a href="index.php?link=playerinfo&id='.$playerid.'&'.SID.'">'.$player->callsign.'</a>
      from the team <a href="index.php?link=teaminfo&id='.$teamid.'&'.SID.'">'.$team->name.'</a>.</center>';
      echo '<center><BR><form method=GET>'.SID_FORM.'
          <input type=hidden name=link value='.$link.'>
          <input type=hidden name=playerid value='.$playerid.'>
          <input type=hidden name=teamid value='.$teamid.'>
          <input type=hidden name=callsign value="'.$player->callsign.'">
          <input type=hidden name=name value="'.$team->name.'">' .

'<table border=0><TR><TD>'.
          htmlFormButton (' BAN ', 'f_okban_x') .
' &nbsp;</td><TD> '.
          htmlURLbutton('Cancel', 'teaminfo', "id=$teamid", CLRBUT) .'
</td></tr></table>

          </form></center>';
    }
  }
}
?>
