<?php // $Id$ vim:sts=2:et:sw=2

function section_messages(){
  $vars = array('del','delbulk','checknum','read','link','read');
  foreach($vars as $var)
    $$var = isset($_POST[$var]) ? $_POST[$var] : $_GET[$var];

  echo '<BR>';

  if(isAuthenticated()) {
    $_SESSION['last_msg_read_ts'] = time();
    $_SESSION['new_mail'] = 0;

    if(isset($del)) {
      mysql_query("delete from l_message
              where msgid=$del
              and toid={$_SESSION['playerid']}");
    }

    if(isset($delbulk)) {
      $numdel = 0;
      for($i = 0; $i < $checknum; $i++) {
        $delid = $_POST["del".$i];
        if(isset($delid)) {
          $numdel++;
          mysql_query("delete from l_message where msgid=$delid and toid={$_SESSION['playerid']}");
        }
      }
      if($numdel != 1) {
        $esse = 's';
      } else {
        $esse = '';
      }
      echo "<center>Deleted $numdel message$esse.</center><BR>";
    }

    if(isset($read)) {
      // Display one message
      $res = sqlQuery("select l_player.callsign sender, l_message.status as msgstat, fromid, datesent, subject, msg, htmlok, l_message.team
        from l_message
        left join l_player
        on id = fromid
        where toid={$_SESSION['playerid']}
        and msgid=$read");

      if(mysql_num_rows($res) == 0) {
        return errorPage ('no messages found');
      } else {
        // Display the message
        $msg = mysql_fetch_object($res);
        echo '<table width=80% align=center border=0 cellspacing=0 cellpadding=1>
        
        <tr class=tabhead><td align=right width=10><nobr>Date sent:</nobr></td><TD width=6></td><TD>'.$msg->datesent.'</td></tr>';

        if($msg->sender == '') {
                // Administrative message
                echo '<tr class=tabhead><td align=right>From: </td><TD></td><TD><b>CTF League System</b></td></tr>';
        } else {
                echo '<tr class=tabhead><td align=right>From: </td><TD></td><TD><a href="index.php?link=playerinfo&id='.$msg->fromid.'&'.SID.'">'.$msg->sender.'</a></td></tr>';
        }

        if($msg->subject == '') {
            $subject = 'No subject';
        } else {
            $subject = stripslashes($msg->subject);
        }

        echo '<tr class=tabhead><td align=right>Subject:</td><TD></td><TD>'
            .wordwrap(htmlentities($subject), 40, '<br>')
            .'</td></tr>';
            
        echo '<tr><td align=right valign=top><BR>Message:</td><TD></td><TD><BR><TABLE width=100% cellpadding=10 style="border: solid 1px"><TR><TD>';

        if($msg->sender=='' || $msg->htmlok>0)    // if admin message, allow html
          echo nl2br($msg->msg);
        else
          echo nl2br(htmlentities($msg->msg));

        echo '</td></tr></table></td></tr></table>';



if ($msg->msgstat == 'new'){
 -- $_SESSION['mail_unread'];
        // Set message as read
        mysql_query("update l_message set status='read' where msgid=$read");
}


        // Display buttons: delete goback reply
        echo '<br><TABLE align=center><TR valign=top>';

        // Can't reply to administrative messages
        if($msg->sender != '') {
                echo '<TD><form method=post action="index.php">'.SID_FORM;
                echo '<input type=hidden name=link value=sendmessage>';
                echo '<input type=hidden name=pid value='.$msg->fromid.'>';
                echo '<input type=hidden name=toteam value="'.$msg->team.'">';
                echo '<input type=hidden name=reply value=1>';
                echo '<input type=hidden name=mid value='.$read.'>';
                echo htmlFormButton ('Reply', 'reply_direct' ) . '</td>';
                if( $msg->team == 'yes' ) echo '<td width=10></td><td>'.htmlFormButton ('Reply To Team', 'reply_team' ).'</td>';
                echo '</form><TD width=10></td>';
        }
        echo '<TD>'. htmlURLbutton ('Delete', 'messages', "del=$read")  .'</td><TD width=10></td><TD>'
            .htmlURLbutton ('Back', 'messages', null, CLRBUT) .'</td></tr></table>';
      }

    } else {
      // Display all messages
      $res = mysql_query("select msgid, l_player.callsign sender, fromid, datesent, 
          subject, l_message.status, l_message.team
          from l_message left join l_player on id = fromid
          where toid={$_SESSION['playerid']} order by datesent desc");

      echo '<div class=checkbox>';
      if(mysql_num_rows($res) == 0) {
        echo "<center>You don't have any message to read.</center>";
      } else {
        echo '<script type="text/javascript">
          function checkAll (form, checkallcheckbox)
          {
          for (i = 0; i < form.elements.length; i++)
            if (form.elements[i].type == "checkbox"){
              form.elements[i].checked = checkallcheckbox.checked;
            }
          }
          </script>';
        echo '<form name="myform" method=post>'.SID_FORM.'<table border=0 align=center cellspacing=0 cellpadding=1>
          <tr class=tabhead><td><input type="checkbox" name="CheckAll" value="Check All"
          onClick="checkAll(document.myform, document.myform.CheckAll)"</td><td>Date sent&nbsp;</td>
          <td>Subject&nbsp;</td><td width=5></td><td>From</td></tr>';
        
        $checknum = 0;
        $rownum=0;
        while($msg = mysql_fetch_object($res)) {
          $cl=(++$rownum%2)?'rowOdd':'rowEven';
          echo "\n<tr class=$cl valign=top><td>";

          // Display checkbox for deleting message
          echo '<input class=checkbox type=checkbox name=del'.$checknum.' value='.$msg->msgid.'>&nbsp;';
          $checknum++;

          // New messages are bold, so we prepare some stuff
          $bb = '';
          $be = '';

          switch($msg->status) {
            case 'new':
              echo '<img src="'.THEME_DIR.'msgnew.gif">';
              $bb = '<b>';
              $be = '</b>';
              break;
            case 'read':
              echo '<img src="'.THEME_DIR.'msgread.gif">';
              break;
            case 'replied':
              echo '<img src="'.THEME_DIR.'msgreplied.gif">';
              break;
          }

          if($msg->team == 'yes') {
              echo '<img src="'. THEME_DIR  .'/team.gif">';
          }
          echo '</td><td><font size=-2>'.$bb.$msg->datesent.$be.'</font>&nbsp;&nbsp;</td>';
          if($msg->subject == '') {
              $subject = 'No subject';
          } else {
              $subject = stripslashes($msg->subject);
          }

          echo "<TD>$bb". htmlLink (wordwrap($subject, 40, '<br>'), 'messages', 
                  "read=$msg->msgid", $bb?LINK_NEW:null)."</a>$be</td><TD></td>";

          if($msg->sender == '') {
                  // Administrative message
                  echo '<td>&nbsp;<b>CTF League System</b></td></tr>';
          } else {
                  echo '<td>&nbsp;<a href="index.php?link=playerinfo&id='.$msg->fromid.'&'.SID.'">'.$msg->sender.'</a></td></tr>';
          }
        }
      
      echo '</table><br>  </div>  <center>'
          . htmlFormButton ('Delete Checked', 'delbulk')
          .'<input type=hidden name=link value="messages">
          <input type=hidden name=checknum value='.$checknum.'
          </center></form>';
      }
    }
  } else {
    errorPage ('You are not allowed to view the messages');
  }
}
?>
