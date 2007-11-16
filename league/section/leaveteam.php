<?php // $Id$ vim:sts=2:et:sw=2

function section_leaveteam(){
  require_once('lib/common.php');
  $id = addslashes($_GET['id']);
  $link = $_GET['link'];
  $answer = $_GET['answer'];

  $s_playerid = $_SESSION['playerid'];

  // Dont let admirarch leave a team. SC request
//  if( $s_playerid == 2074 ) {
//    errorpage("No can do");
//    return;
//  }

  $obj = mysql_fetch_object(sqlQuery("select name, leader from l_team where id=$id"));

  echo '<BR>';

  if($answer == "") {
    if($s_playerid == $obj->leader) {
      // Prevent leaders from leaving their own team
      echo "<center>You can't abandon the magnificient $obj->name team, because you are its leader.<BR>
        Please go to your <a href=\"index.php?link=teamadmin&id=".$id."&".SID."\"><b>team page</b></a>
        and assign another leader first!</center>";
    } else {
      echo '<center>You are about to abandon the magnificient <a href="index.php?link=teaminfo&id='.$id.'&'.SID.'">'.$obj->name.'</a> team, its members will miss you...<br>';
      echo "Please, please, stay in the team!<br>";
      echo "Are you really sure you want to abandon this team ?<br><br>";

      echo '<TABLE border=0><TR><TD>' 
          . htmlURLbutton ('ABANDON', $link, "id=$id&answer=yes") 
          .'</td><TD>&nbsp;</td><TD>'
          . htmlURLbutton ('CANCEL', $link, "id=$id&answer=no", CLRBUT)
          . '</td></tr></table>';
    }
  } else {
    if($answer == "yes") {
      // Remove this player from the team
      mysql_query("update l_player set team=0 where id=".$s_playerid);

      // Open the team, because it needs at least one more player,
      // but only if it is not administratively closed, or if num players<3
      $pl = mysql_fetch_object(mysql_query("select count(*) num from l_player where team=".$id));
      if($pl->num < 3) {
              mysql_query("update l_team set status='opened' where id=".$id);
      } else {
              mysql_query("update l_team set status='opened' where adminclosed='no' and id=".$id);
      }
      echo "<center>That's it, you are no longer a member of the <a href=\"index.php?link=teaminfo&id=".$id."&".SID."\">".$obj->name."</a> team.<br>
                      A message has just been sent to the team leader</center>";
      session_refresh();
      // Send a message to the team leader
      sendBzMail(0, $obj->leader, $_SESSION['callsign'].' has left your team!', 'A player just left your team: "'.$_SESSION['callsign'].'"');
    } else {
      // Say thank you
      echo '<BR><center>The <a href="index.php?link=teaminfo&id='.$id.'&'.SID.'">'.$obj->name.'</a> team members thank you for being so brave, and continuing the fight!</center>';
    }
  }
}
?>
