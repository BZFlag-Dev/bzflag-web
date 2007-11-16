<?php // $Id$ vim:sts=2:et:sw=2

// print_r($_POST);

function section_sendmessage()
{
  require_once('lib/common.php');

  $vars = array('pid','tid','f_ok_x','toteam','reply','f_subject','f_msg','replying','repid','mid','link','dup');
  $s_playerid = $_SESSION['playerid'];

  foreach($vars as $var) {
    $$var = isset($_POST[$var]) ? $_POST[$var] : $_GET[$var];
  }

  if (isGuest())  
    return errorPage('Not Authorized');

  if ($dup){
    echo '<BR><CENTER>Duplicate mail not sent (refresh or back button detected)<BR>';
    return;
  }

  if(isset($pid)) {
    if($toteam == 'yes' && isset($_POST['reply_team'])) {
      $team = mysql_fetch_object(mysql_query("select l_team.id, name from l_team, l_player where l_player.id=$pid and l_team.id=team"));
      $tid = $team->id;
    } else {
      $toteam = 'no';
      $player = mysql_fetch_object(mysql_query("select callsign from l_player where id=$pid"));
    }
  }

  if(isset($tid)) {
    $team = mysql_fetch_object(mysql_query("select name from l_team where id=$tid"));
  }

  $error = 1;

  if($f_ok_x) {
    $error = 0;
    $f_msg = stripslashes($f_msg);
    if($f_msg == '') {
      $error = 1;
      echo "<div class=error>You must write something to send a message</div>";
    }
  }

  if($error) {
    if(isset($toteam)) {
      if($toteam == 'yes') {
        $rcpt = 'team <a href="index.php?link=teaminfo&id='.$tid.'&'.SID.'">'.$team->name.'</a>';
      } else {
        $rcpt = '<a href="index.php?link=playerinfo&id='.$pid.'&'.SID.'">'.$player->callsign.'</a>';
      }
    } else {
      if(isset($tid)) {
        $rcpt = 'team <a href="index.php?link=teaminfo&id='.$tid.'&'.SID.'">'.$team->name.'</a>';
      } else {
        $rcpt = 'player <a href="index.php?link=playerinfo&id='.$pid.'&'.SID.'">'.$player->callsign.'</a>';
      }
    }



    if(isset($reply)) {
      $msg = mysql_fetch_object(mysql_query($sql="select * from l_message where msgid=$mid"));
      $message = $msg->msg;

      $f_subject = $msg->subject;

      if( substr($f_subject,0,3) != 'Re:' )
        $f_subject = 'Re: ' . $f_subject;

      // Strip subject if it's too long
      if(strlen($f_subject) > 80) {
        $f_subject = substr($f_subject, 0, 80);
      }

      echo "<table border=0 align=center cellspacing=0 cellpadding=1>
        <tr class=tablehead><td>Original message:</td></tr>
        <tr class=msgquote><td>";
      echo stripslashes(nl2br($message)).'</td></tr></table><br>';

      // Quote initial message
      $f_msg = ereg_replace("\n", ">", ereg_replace("^", ">", $message));
      $f_msg = $f_msg."\n>\n";
      $action = "Replying to ";
    } else {
      $action = "Sending a message to ";
    }


    echo '<center><form method=post>'.SID_FORM.$action.$rcpt.'.<br><br>
        Enter your message below:<br>
        Subject:<input type=text maxlength=80 size=60 name=f_subject value="'.$f_subject.'"><BR>
        <textarea cols=50 rows=15 name=f_msg>'.stripslashes($f_msg).'</textarea>
        <hr><center>
        <input type=hidden name=link value='. $link .'>';
        snFormInit();
        echo '<TABLE><TR><TD>'
        .htmlFormButton ('SEND', 'f_ok_x')
        .'</td><TD width=10></td><TD>';

    if(isset($pid)) 
      echo htmlURLbutton ('Cancel', 'playerinfo', "id=$pid", CLRBUT);
    else
      echo htmlURLbutton ('Cancel', 'teaminfo', "id=$tid", CLRBUT);

    echo '</td></tr></table><input type=hidden name=link value='.$link.'>';

    if(isset($reply)) {
      // Flag that we are replying to a message
      echo '<input type=hidden name=replying value=1>';
      echo '<input type=hidden name=repid value='.$mid.'>';
    }
    if(isset($tid)) {
      echo '<input type=hidden name=tid value='.$tid.'>';
    } else {
      echo '<input type=hidden name=pid value='.$pid.'>';
    }
    echo '</center></form></center>';
    
    

    
  } else {
    // Do send the message
    echo "<center>Message sent, thank you!</center>";
snCheck ('sendmessage', 'dup=1');
    // If replying we mark the original message as replied
    if(isset($replying)) {
      mysql_query("update l_message set status='replied' where msgid=$repid");
    }
    if(isset($pid)) {
      if($toteam == 'yes') {
        // Send to a team
        sendBzMail($s_playerid, $tid, $f_subject, $f_msg, 'yes');
      } else {
        // Send to one player
        sendBzMail($s_playerid, $pid, $f_subject, $f_msg);
      }
    } else {
      // Send to a team
      sendBzMail($s_playerid, $tid, $f_subject, $f_msg, 'yes');
    }
  }
}
?>
